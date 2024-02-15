<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace EBT\ExtensionBuilder\Parser;

use EBT\ExtensionBuilder\Domain\Model;
use EBT\ExtensionBuilder\Domain\Model\AbstractObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\Method;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;
use EBT\ExtensionBuilder\Domain\Model\FunctionObject;
use EBT\ExtensionBuilder\Domain\Model\NamespaceObject;
use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * factory for class objects and related objects (methods, properties etc)
 *
 * builds objects from PHP-Parser nodes
 */
class ClassFactory implements ClassFactoryInterface, SingletonInterface
{
    public function buildClassObject(Class_ $classNode): ClassObject
    {
        $classObject = new ClassObject((string)$classNode->name);
        foreach ($classNode->implements as $interfaceNode) {
            $classObject->addInterfaceName((string)$interfaceNode);
        }
        $classObject->setModifiers($classNode->flags);
        if ($classNode->extends !== null) {
            $classObject->setParentClassName(NodeConverter::getValueFromNode($classNode->extends));
        }
        $this->addCommentsFromAttributes($classObject, $classNode);
        return $classObject;
    }

    public function buildClassMethodObject(ClassMethod $methodNode): Method
    {
        $methodObject = new Method($methodNode->name->name);
        $methodObject->setModifiers($methodNode->flags);
        $returnType = $methodNode->getReturnType();
        if ($returnType instanceof FullyQualified) {
            $methodObject->setReturnType($returnType->toCodeString());
        }
        $this->addCommentsFromAttributes($methodObject, $methodNode);
        $this->setFunctionProperties($methodNode, $methodObject);
        return $methodObject;
    }

    public function buildFunctionObject(Function_ $functionNode): FunctionObject
    {
        $functionObject = new FunctionObject(NodeConverter::getNameFromNode($functionNode->name));
        $this->addCommentsFromAttributes($functionObject, $functionNode);
        $this->setFunctionProperties($functionNode, $functionObject);
        return $functionObject;
    }

    public function buildPropertyObject(Property $propertyNode): Model\ClassObject\Property
    {
        $propertyName = '';
        $propertyDefault = null;

        foreach ($propertyNode->props as $subNode) {
            if ($subNode instanceof PropertyProperty) {
                $propertyName = $subNode->name->name;
                if ($subNode->getAttribute('default')) {
                    $propertyDefault = $subNode->getAttribute('default');
                }
            }
        }

        $propertyObject = new Model\ClassObject\Property($propertyName);
        $propertyObject->setModifiers($propertyNode->flags);
        if (null !== $propertyDefault) {
            $propertyObject->setValue(NodeConverter::getValueFromNode($propertyDefault));
            $propertyObject->setDefaultValueNode($propertyDefault);
        }
        $this->addCommentsFromAttributes($propertyObject, $propertyNode);
        return $propertyObject;
    }

    public function buildNamespaceObject(Namespace_ $nameSpaceNode): NamespaceObject
    {
        $nameSpaceObject = new NamespaceObject(NodeConverter::getValueFromNode($nameSpaceNode));
        $this->addCommentsFromAttributes($nameSpaceObject, $nameSpaceNode);
        return $nameSpaceObject;
    }

    protected function setFunctionProperties(Stmt $node, FunctionObject $object): FunctionObject
    {
        if (property_exists($node, 'flags')) {
            $object->setModifiers($node->flags);
        }
        $object->setBodyStmts($node->stmts);
        $object->setStartLine($node->getAttribute('startLine'));
        $object->setEndLine($node->getAttribute('endLine'));
        $getVarTypeFromParamTag = false;
        $paramTags = [];
        if ($object->isTaggedWith('param') && is_array($object->getTagValues('param'))) {
            $paramTags = $object->getTagValues('param');
            if (count($paramTags) === count($node->params)) {
                $getVarTypeFromParamTag = true;
            }
        }
        $position = 0;
        foreach ($node->params as $param) {
            /** @var Param $param */
            $parameter = new MethodParameter($param->var->name);
            $parameter->setPosition($position);
            $parameter->setStartLine($param->getAttribute('startLine'));
            $parameter->setEndLine($param->getAttribute('endLine'));
            $parameter->setPassedByReference($param->byRef);
            if ($param->type !== null) {
                $parameter->setTypeHint(NodeConverter::getNameFromNode($param->type));
                if (!$getVarTypeFromParamTag) {
                    $parameter->setVarType(NodeConverter::getNameFromNode($param->type));
                }
            } elseif ($getVarTypeFromParamTag) {
                // if there is not type hint but a varType in the param tag,
                // we set the varType of the parameter
                $paramTag = explode(' ', $paramTags[$position]);
                if ($paramTag[0] !== '$' . $param->var->name) {
                    $parameter->setVarType($paramTag[0]);
                    $parameter->setTypeForParamTag($paramTag[0]);
                }
            }
            if ($param->getAttribute('default') !== null) {
                $parameter->setDefaultValue($param->getAttribute('default'));
            }
            $object->setParameter($parameter);
            $position++;
        }
        $object->updateParamTags();
        return $object;
    }

    protected function addCommentsFromAttributes(AbstractObject $object, Stmt $node): void
    {
        $comments = $node->getAttribute('comments');
        $docComments = [];
        if (is_array($comments)) {
            foreach ($comments as $comment) {
                if ($comment instanceof Doc) {
                    $docComments[] = $comment;
                } elseif ($comment instanceof Comment) {
                    $object->addComment($comment->getText());
                }
            }
        }
        $dc = count($docComments);
        if ($dc > 1) {
            foreach ($docComments as $index => $docComment) {
                if ($index < $dc -1) {
                    $object->addComment($docComment->getText());
                } else {
                    $object->setDocComment($docComment->getText());
                }
            }
        } elseif ($dc === 1) {
            $object->setDocComment($docComments[0]->getText());
        }
    }
}
