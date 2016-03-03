<?php
namespace EBT\ExtensionBuilder\Parser;

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

use EBT\ExtensionBuilder\Domain\Model;
use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;

/**
 * factory for class objects and related objects (methods, properties etc)
 *
 * builds objects from PHP-Parser nodes
 *
 */
class ClassFactory implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @param \PhpParser\Node\Stmt\Class_ $classNode
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    public function buildClassObject(\PhpParser\Node\Stmt\Class_ $classNode)
    {
        $classObject = new Model\ClassObject\ClassObject($classNode->name);
        foreach ($classNode->implements as $interfaceNode) {
            $classObject->addInterfaceName($interfaceNode, false);
        }
        $classObject->setModifiers($classNode->type);
        if (!is_null($classNode->extends)) {
            $classObject->setParentClassName(NodeConverter::getValueFromNode($classNode->extends));
        }
        $this->addCommentsFromAttributes($classObject, $classNode);
        return $classObject;
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $methodNode
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
     */
    public function buildClassMethodObject(\PhpParser\Node\Stmt\ClassMethod $methodNode)
    {
        $methodObject = new Model\ClassObject\Method($methodNode->name);
        $methodObject->setModifiers($methodNode->type);
        $this->addCommentsFromAttributes($methodObject, $methodNode);
        $this->setFunctionProperties($methodNode, $methodObject);
        return $methodObject;
    }

    /**
     * @param \PhpParser\Node\Stmt\Function_ $functionNode
     * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject
     */
    public function buildFunctionObject(\PhpParser\Node\Stmt\Function_ $functionNode)
    {
        $functionObject = new Model\FunctionObject($functionNode->name);
        $this->addCommentsFromAttributes($functionObject, $functionNode);
        $this->setFunctionProperties($functionNode, $functionObject);
        return $functionObject;
    }

    /**
     * @param \PhpParser\Node\Stmt\Property $propertyNode
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property
     */
    public function buildPropertyObject(\PhpParser\Node\Stmt\Property $propertyNode)
    {
        $propertyName = '';
        $propertyDefault = null;

        foreach ($propertyNode->props as $subNode) {
            if ($subNode instanceof \PhpParser\Node\Stmt\PropertyProperty) {
                $propertyName = $subNode->name;
                if ($subNode->default) {
                    $propertyDefault = $subNode->default;
                }
            }
        }

        $propertyObject = new Model\ClassObject\Property($propertyName);
        $propertyObject->setModifiers($propertyNode->type);
        if (null !== $propertyDefault) {
            $propertyObject->setValue(NodeConverter::getValueFromNode($propertyDefault), false, $propertyObject->isTaggedWith('var'));
            $propertyObject->setDefaultValueNode($propertyDefault);
        }
        $this->addCommentsFromAttributes($propertyObject, $propertyNode);
        return $propertyObject;
    }

    /**
     * @param \PhpParser\Node\Stmt\Namespace_ $nameSpaceNode
     * @return \EBT\ExtensionBuilder\Domain\Model\NamespaceObject
     */
    public function buildNamespaceObject(\PhpParser\Node\Stmt\Namespace_ $nameSpaceNode)
    {
        $nameSpaceObject = new Model\NamespaceObject(NodeConverter::getValueFromNode($nameSpaceNode));
        $this->addCommentsFromAttributes($nameSpaceObject, $nameSpaceNode);
        return $nameSpaceObject;
    }

    /**
     * @param \PhpParser\Node\Stmt $node
     * @param \EBT\ExtensionBuilder\Domain\Model\FunctionObject $object
     * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject
     */
    protected function setFunctionProperties(\PhpParser\Node\Stmt $node, Model\FunctionObject $object)
    {
        if (property_exists($node, 'type')) {
            $object->setModifiers($node->type);
        }
        $object->setBodyStmts($node->stmts);
        $position = 0;
        $object->setStartLine($node->getAttribute('startLine'));
        $object->setEndLine($node->getAttribute('endLine'));
        $getVarTypeFromParamTag = false;
        $paramTags = array();
        if ($object->isTaggedWith('param') && is_array($object->getTagValues('param'))) {
            $paramTags = $object->getTagValues('param');
            if (count($paramTags) == count($node->params)) {
                $getVarTypeFromParamTag = true;
            }
        }
        /** @var $param \PhpParser\NodeAbstract */
        foreach ($node->params as $param) {
            $parameter = new Model\ClassObject\MethodParameter($param->name);
            $parameter->setPosition($position);
            $parameter->setStartLine($param->getAttribute('startLine'));
            $parameter->setEndLine($param->getAttribute('endLine'));
            if (!is_null($param->type)) {
                $parameter->setTypeHint(NodeConverter::getValueFromNode($param->type));
                if (!$getVarTypeFromParamTag) {
                    $parameter->setVarType(NodeConverter::getValueFromNode($param->type));
                }
            } elseif ($getVarTypeFromParamTag) {
                // if there is not type hint but a varType in the param tag,
                // we set the varType of the parameter
                $paramTag = explode(' ', $paramTags[$position]);
                if ($paramTag[0] !== '$' . $param->name) {
                    $parameter->setVarType($paramTag[0]);
                    $parameter->setTypeForParamTag($paramTag[0]);
                }
            }
            if (!is_null($param->default)) {
                $parameter->setDefaultValue($param->default);
            }
            $object->setParameter($parameter);
            $position++;
        }
        $object->updateParamTags();
        return $object;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\AbstractObject $object
     * @param \PhpParser\Node\Stmt $node
     */
    protected function addCommentsFromAttributes(Model\AbstractObject $object, \PhpParser\Node\Stmt $node)
    {
        $comments = $node->getAttribute('comments');
        if (is_array($comments)) {
            foreach ($comments as $comment) {
                if ($comment instanceof \PhpParser\Comment\Doc) {
                    $object->setDocComment($comment->getReformattedText());
                } elseif ($comment instanceof \PhpParser\Comment) {
                    $object->addComment($comment->getText());
                }
            }
        }
    }
}
