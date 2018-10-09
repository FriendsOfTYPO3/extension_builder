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

use EBT\ExtensionBuilder\Domain\Model\AbstractObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\Method;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\Property;
use EBT\ExtensionBuilder\Domain\Model\Container;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Domain\Model\NamespaceObject;
use PhpParser\BuilderFactory;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use TYPO3\CMS\Core\SingletonInterface;

class NodeFactory implements SingletonInterface
{
    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
     * @param bool $skipStatements
     * @return \PhpParser\Node\Stmt\Class_
     */
    public function buildClassNode($classObject, $skipStatements = false)
    {
        $factory = new BuilderFactory;

        $classNodeBuilder = $factory->class($classObject->getName());
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

            foreach ($classObject->getMethods() as $method) {
                $methods[$method->getName()] = $this->buildMethodNode($method);
            }

            foreach ($classObject->getProperties() as $property) {
                $properties[$property->getName()] = $this->buildPropertyNode($property);
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

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\File $fileObject
     * @return array
     */
    public function getFileStatements(File $fileObject)
    {
        $stmts = [];
        if ($fileObject->hasNamespaces()) {
            foreach ($fileObject->getNamespaces() as $namespace) {
                $stmts[] = $this->buildNamespaceNode($namespace);
                foreach ($namespace->getAliasDeclarations() as $aliasDeclaration) {
                    $stmts[] = $this->buildUseStatementNode($aliasDeclaration['name'], $aliasDeclaration['alias']);
                }
                $stmts = array_merge($stmts, $this->getContainerStatements($namespace));
            }
        } else {
            $stmts = array_merge($stmts, $this->getContainerStatements($fileObject));
        }
        return $stmts;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Container $container
     * @return array
     */
    protected function getContainerStatements(Container $container)
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
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method $methodObject
     * @return \PhpParser\Node\Stmt\ClassMethod
     */
    public function buildMethodNode(Method $methodObject)
    {
        $factory = new BuilderFactory;
        $methodNodeBuilder = $factory->method($methodObject->getName());
        $parameters = $methodObject->getParameters();
        if (count($parameters) > 0) {
            foreach ($parameters as $parameter) {
                $parameterNode = $this->buildParameterNode($parameter);
                $methodNodeBuilder->addParam($parameterNode);
            }
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

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter $parameter
     * @return \PhpParser\Node\Param
     */
    public function buildParameterNode(MethodParameter $parameter)
    {
        $factory = new BuilderFactory;
        $paramNodeBuilder = $factory->param($parameter->getName());
        if ($parameter->hasTypeHint()) {
            $paramNodeBuilder->setTypeHint($parameter->getTypeHint());
        }
        if ($parameter->isPassedByReference()) {
            $paramNodeBuilder->makeByRef();
        }

        if (!is_null($parameter->getDefaultValue())) {
            $paramNodeBuilder->setDefault($parameter->getDefaultValue());
        }
        $parameterNode = $paramNodeBuilder->getNode();
        $parameterNode->setAttribute('startLine', $parameter->getStartLine());
        $parameterNode->setAttribute('endLine', $parameter->getEndLine());
        return $parameterNode;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\AbstractObject $object
     * @param \PhpParser\Node\Stmt $node
     */
    protected function addCommentAttributes(AbstractObject $object, Stmt $node)
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

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\NamespaceObject $nameSpace
     *
     * @return \PhpParser\Node\Stmt\Namespace_
     */
    public function buildNamespaceNode(NamespaceObject $nameSpace)
    {
        return new Namespace_(new Name($nameSpace->getName()));
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property $property
     * @return \PhpParser\Node\Stmt\Property
     */
    public function buildPropertyNode(Property $property)
    {
        $factory = new BuilderFactory;
        $propertyNodeBuilder = $factory->property($property->getName());

        $propertyNode = $propertyNodeBuilder->getNode();
        $propertyNode->flags = $property->getModifiers();

        foreach ($propertyNode->props as $subNode) {
            if ($subNode instanceof PropertyProperty) {
                if (!is_null($property->getDefaultValueNode())) {
                    $subNode->default = $property->getDefaultValueNode();
                } else {
                    $subNode->default = $this->buildNodeFromValue($property->getDefault());
                }
            }
        }

        $this->addCommentAttributes($property, $propertyNode);
        $propertyNode->default = $property->getDefault();
        return $propertyNode;
    }

    //

    /**
     * @static
     * @param string $name
     * @param mixed $value
     * @return \PhpParser\Node\Const_
     */
    public static function buildConstantNode($name, $value)
    {
        $constantNode = new Const_($name, self::buildNodeFromValue($value));
        return $constantNode;
    }

    /**
     * @static
     * @param string $name
     * @param mixed $value
     * @return \PhpParser\Node\Stmt\ClassConst
     */
    public static function buildClassConstantNode($name, $value)
    {
        $constantNode = new ClassConst([self::buildConstantNode($name, $value)]);
        return $constantNode;
    }

    /**
     * @static
     * @param string $name
     * @param string $alias
     * @return \PhpParser\Node\Stmt\Use_
     */
    public static function buildUseStatementNode($name, $alias)
    {
        $useStatementNode = new Use_(['uses' => new UseUse(self::buildNodeFromName($name), $alias)]);
        return $useStatementNode;
    }

    /**
     * Normalizes a name: Converts plain string names to \PhpParser\Node_Name.
     *
     * @param \PhpParser\Node\Name|string $name The name to normalize
     *
     * @return \PhpParser\Node\Name The normalized name
     */
    public static function buildNodeFromName($name)
    {
        if ($name instanceof Name) {
            return $name;
        } else {
            return new Name($name);
        }
    }

    /**
     * Normalizes a value: Converts nulls, booleans, integers,
     * floats, strings and arrays into their respective nodes
     *
     * @param mixed $value The value to normalize
     *
     * @return \PhpParser\Node\Expr The normalized value
     */
    protected static function buildNodeFromValue($value)
    {
        if ($value instanceof Node) {
            return $value;
        } elseif (is_null($value)) {
            return new ConstFetch(
                new Name('null')
            );
        } elseif (is_bool($value)) {
            return new ConstFetch(
                new Name($value ? 'true' : 'false')
            );
        } elseif (is_int($value)) {
            return new LNumber($value);
        } elseif (is_float($value)) {
            return new DNumber($value);
        } elseif (is_string($value)) {
            return new String_($value);
        } elseif (is_array($value)) {
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
        } else {
            throw new \LogicException('Invalid value');
        }
    }
}
