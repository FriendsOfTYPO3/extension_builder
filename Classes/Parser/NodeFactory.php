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

use EBT\ExtensionBuilder\Domain\Model\AbstractObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\Method;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\Property;
use EBT\ExtensionBuilder\Domain\Model\Container;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Domain\Model\NamespaceObject;
use LogicException;
use PhpParser\BuilderFactory;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use TYPO3\CMS\Core\SingletonInterface;

class NodeFactory implements SingletonInterface
{
    public function buildClassNode(ClassObject $classObject, bool $skipStatements = false): Class_
    {
        $factory = new BuilderFactory();

        $classNodeBuilder = $factory->class((string)$classObject->getName());
        if ($classObject->getParentClassName()) {
            $classNodeBuilder->extend(self::buildNodeFromName($classObject->getParentClassName()));
        }
        $interfaceNames = $classObject->getInterfaceNames();
        if (count($interfaceNames) > 0) {
            call_user_func_array([$classNodeBuilder, 'implement'], $interfaceNames);
        }

        if (!$skipStatements) {
            $stmts = [];

            $properties = [];
            $methods = [];

            foreach ($classObject->getUseTraitStatement() as $statement) {
                $stmts[] = $statement;
            }

            foreach ($classObject->getProperties() as $property) {
                $properties[$property->getName()] = $this->buildPropertyNode($property);
            }

            foreach ($classObject->getMethods() as $method) {
                $methods[$method->getName()] = $this->buildMethodNode($method);
            }

            $constants = $classObject->getConstants();
            if (is_array($constants)) {
                foreach ($constants as $name => $value) {
                    $stmts[] = self::buildClassConstantNode($name, $value);
                }
            }
            foreach ($properties as $property) {
                $stmts[] = $property;
            }

            foreach ($methods as $method) {
                $stmts[] = $method;
            }
            $classNodeBuilder->addStmts($stmts);
        }

        $classNode = $classNodeBuilder->getNode();

        $classNode->flags = $classObject->getModifiers();

        $this->addCommentAttributes($classObject, $classNode);

        return $classNode;
    }

    public function getFileStatements(File $fileObject): array
    {
        if (!$fileObject->hasNamespaces()) {
            return $this->getContainerStatements($fileObject);
        }
        $stmts = [];
        foreach ($fileObject->getNamespaces() as $namespace) {
            $stmts[] = $this->buildNamespaceNode($namespace);
            foreach ($namespace->getAliasDeclarations() as $aliasDeclaration) {
                $stmts[] = self::buildUseStatementNode($aliasDeclaration['name'], $aliasDeclaration['alias']);
            }
            $stmts = array_merge($stmts, $this->getContainerStatements($namespace));
        }
        return $stmts;
    }

    protected function getContainerStatements(Container $container): array
    {
        $stmts = [];
        foreach ($container->getPreClassStatements() as $preInclude) {
            $stmts[] = $preInclude;
        }

        foreach ($container->getClasses() as $classObject) {
            $stmts[] = $this->buildClassNode($classObject);
        }

        foreach ($container->getFunctions() as $function) {
            // TODO: not yet implemented
        }

        foreach ($container->getPostClassStatements() as $postInclude) {
            $stmts[] = $postInclude;
        }
        return $stmts;
    }

    /**
     * @param Method $methodObject
     * @return ClassMethod
     */
    public function buildMethodNode(Method $methodObject): ClassMethod
    {
        $factory = new BuilderFactory();
        $methodNodeBuilder = $factory->method($methodObject->getName());
        $parameters = $methodObject->getParameters();
        if (count($parameters) > 0) {
            foreach ($parameters as $parameter) {
                $parameterNode = $this->buildParameterNode($parameter);
                $methodNodeBuilder->addParam($parameterNode);
            }
        }
        $returnType = $methodObject->getReturnType();
        if ($returnType !== null) {
            $methodNodeBuilder->setReturnType(new FullyQualified(ltrim($returnType, '\\')));
        }
        $methodNodeBuilder->addStmts($methodObject->getBodyStmts());

        $methodNode = $methodNodeBuilder->getNode();
        $methodNode->flags = $methodObject->getModifiers();
        $methodNode->setAttribute('startLine', $methodObject->getStartLine());
        $methodNode->setAttribute('endLine', $methodObject->getEndLine());
        $methodObject->updateParamTags();
        $this->addCommentAttributes($methodObject, $methodNode);
        return $methodNode;
    }

    public function buildParameterNode(MethodParameter $parameter): Param
    {
        $factory = new BuilderFactory();
        $paramNodeBuilder = $factory->param($parameter->getName());
        if ($parameter->hasTypeHint()) {
            $paramNodeBuilder->setTypeHint($parameter->getTypeHint());
        }
        if ($parameter->isPassedByReference()) {
            $paramNodeBuilder->makeByRef();
        }

        if (null !== $parameter->getDefaultValue()) {
            $paramNodeBuilder->setDefault($parameter->getDefaultValue());
        }
        $parameterNode = $paramNodeBuilder->getNode();
        $parameterNode->setAttribute('startLine', $parameter->getStartLine());
        $parameterNode->setAttribute('endLine', $parameter->getEndLine());
        return $parameterNode;
    }

    protected function addCommentAttributes(AbstractObject $object, Stmt $node): void
    {
        $commentAttributes = [];
        $comments = $object->getComments();
        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                $commentAttributes[] = new Comment($comment);
            }
        }
        if ($object->hasDescription() || $object->hasTags()) {
            $commentAttributes[] = new Doc($object->getDocComment());
        }
        $node->setAttribute('comments', $commentAttributes);
    }

    public function buildNamespaceNode(NamespaceObject $nameSpace): Namespace_
    {
        return new Namespace_(new Name($nameSpace->getName()));
    }

    public function buildPropertyNode(Property $property): Stmt\Property
    {
        $factory = new BuilderFactory();
        $propertyNodeBuilder = $factory->property($property->getName());

        $propertyNode = $propertyNodeBuilder->getNode();
        $propertyNode->flags = $property->getModifiers();

        foreach ($propertyNode->props as $subNode) {
            if ($subNode instanceof PropertyProperty) {
                if (null !== $property->getDefaultValueNode()) {
                    $subNode->setAttribute('default', $property->getDefaultValueNode());
                } else {
                    $subNode->setAttribute('default', self::buildNodeFromValue($property->getDefault()));
                }
            }
        }

        $this->addCommentAttributes($property, $propertyNode);
        $propertyNode->setAttribute('default', $property->getDefault());
        return $propertyNode;
    }

    public static function buildConstantNode(string $name, $value): Const_
    {
        return new Const_($name, self::buildNodeFromValue($value));
    }

    public static function buildClassConstantNode(string $name, $value): ClassConst
    {
        return new ClassConst([self::buildConstantNode($name, $value)]);
    }

    public static function buildUseStatementNode(string $name, ?string $alias = null): Use_
    {
        return new Use_(['uses' => new UseUse(self::buildNodeFromName($name), $alias)]);
    }

    /**
     * Normalizes a name: Converts plain string names to \PhpParser\Node_Name.
     *
     * @param Name|string $name The name to normalize
     *
     * @return Name The normalized name
     */
    public static function buildNodeFromName($name): Name
    {
        if ($name instanceof Name) {
            return $name;
        }

        return new Name($name);
    }

    /**
     * Normalizes a value: Converts nulls, booleans, integers,
     * floats, strings and arrays into their respective nodes
     *
     * @param mixed $value The value to normalize
     *
     * @return \PhpParser\Node\Expr The normalized value
     */
    protected static function buildNodeFromValue($value): Node
    {
        if ($value instanceof Node) {
            return $value;
        }

        if ($value === null) {
            return new ConstFetch(new Name('null'));
        }

        if (is_bool($value)) {
            return new ConstFetch(new Name($value ? 'true' : 'false'));
        }

        if (is_int($value)) {
            return new LNumber($value);
        }

        if (is_float($value)) {
            return new DNumber($value);
        }

        if (is_string($value)) {
            return new String_($value);
        }

        if (is_array($value)) {
            $items = [];
            $lastKey = -1;
            foreach ($value as $itemKey => $itemValue) {
                // for consecutive, numeric keys don't generate keys
                if (null !== $lastKey && ++$lastKey === $itemKey) {
                    $items[] = new ArrayItem(
                        self::buildNodeFromValue($itemValue)
                    );
                } else {
                    $lastKey = null;
                    $items[] = new ArrayItem(
                        self::buildNodeFromValue($itemValue),
                        self::buildNodeFromValue($itemKey)
                    );
                }
            }

            return new Array_($items);
        }

        throw new LogicException('Invalid value');
    }
}
