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

class NodeFactory implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
     * @param bool $skipStatements
     * @return \PhpParser\Node\Stmt\Class_
     */
    public function buildClassNode($classObject, $skipStatements = false)
    {
        $factory = new \PhpParser\BuilderFactory;

        $classNodeBuilder = $factory->class($classObject->getName());
        if ($classObject->getParentClassName()) {
            $classNodeBuilder->extend(self::buildNodeFromName($classObject->getParentClassName()));
        }
        $interfaceNames = $classObject->getInterfaceNames();
        if (count($interfaceNames) > 0) {
            call_user_func_array(array($classNodeBuilder, 'implement'), $interfaceNames);
        }

        if (!$skipStatements) {
            $stmts = array();

            $properties = array();
            $methods = array();

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

        $classNode->type = $classObject->getModifiers();

        $this->addCommentAttributes($classObject, $classNode);

        return $classNode;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\File $fileObject
     * @return array
     */
    public function getFileStatements(\EBT\ExtensionBuilder\Domain\Model\File $fileObject)
    {
        $stmts = array();
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
    protected function getContainerStatements(\EBT\ExtensionBuilder\Domain\Model\Container $container)
    {
        $stmts = array();
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
    public function buildMethodNode(\EBT\ExtensionBuilder\Domain\Model\ClassObject\Method $methodObject)
    {
        $factory = new \PhpParser\BuilderFactory;
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
        $methodNode->type = $methodObject->getModifiers();
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
    public function buildParameterNode(\EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter $parameter)
    {
        $factory = new \PhpParser\BuilderFactory;
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
    protected function addCommentAttributes(\EBT\ExtensionBuilder\Domain\Model\AbstractObject $object, \PhpParser\Node\Stmt $node)
    {
        $commentAttributes = array();
        $comments = $object->getComments();
        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                $commentAttributes[] = new \PhpParser\Comment($comment);
            }
        }
        if ($object->hasDescription() || $object->hasTags()) {
            $commentAttributes[] = new \PhpParser\Comment\Doc($object->getDocComment());
        }
        $node->setAttribute('comments', $commentAttributes);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\NamespaceObject $nameSpace
     */
    public function buildNamespaceNode(\EBT\ExtensionBuilder\Domain\Model\NamespaceObject $nameSpace)
    {
        return new \PhpParser\Node\Stmt\Namespace_(new \PhpParser\Node\Name($nameSpace->getName()));
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property $property
     * @return \PhpParser\Node\Stmt\Property
     */
    public function buildPropertyNode(\EBT\ExtensionBuilder\Domain\Model\ClassObject\Property $property)
    {
        $factory = new \PhpParser\BuilderFactory;
        $propertyNodeBuilder = $factory->property($property->getName());

        $propertyNode = $propertyNodeBuilder->getNode();
        $propertyNode->type = $property->getModifiers();

        foreach ($propertyNode->props as $subNode) {
            if ($subNode instanceof \PhpParser\Node\Stmt\PropertyProperty) {
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
        $constantNode = new \PhpParser\Node\Const_($name, self::buildNodeFromValue($value));
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
        $constantNode = new \PhpParser\Node\Stmt\ClassConst(array(self::buildConstantNode($name, $value)));
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
        $useStatementNode = new \PhpParser\Node\Stmt\Use_(array('uses' => new \PhpParser\Node\Stmt\UseUse(self::buildNodeFromName($name), $alias)));
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
        if ($name instanceof \PhpParser\Node\Name) {
            return $name;
        } else {
            return new \PhpParser\Node\Name($name);
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
    protected function buildNodeFromValue($value)
    {
        if ($value instanceof \PhpParser\Node) {
            return $value;
        } elseif (is_null($value)) {
            return new \PhpParser\Node\Expr\ConstFetch(
                new \PhpParser\Node\Name('null')
            );
        } elseif (is_bool($value)) {
            return new \PhpParser\Node\Expr\ConstFetch(
                new \PhpParser\Node\Name($value ? 'true' : 'false')
            );
        } elseif (is_int($value)) {
            return new \PhpParser\Node\Scalar\LNumber($value);
        } elseif (is_float($value)) {
            return new \PhpParser\Node\Scalar\DNumber($value);
        } elseif (is_string($value)) {
            return new \PhpParser\Node\Scalar\String_($value);
        } elseif (is_array($value)) {
            $items = array();
            $lastKey = -1;
            foreach ($value as $itemKey => $itemValue) {
                // for consecutive, numeric keys don't generate keys
                if (null !== $lastKey && ++$lastKey === $itemKey) {
                    $items[] = new \PhpParser\Node\Expr\ArrayItem(
                        self::buildNodeFromValue($itemValue)
                    );
                } else {
                    $lastKey = null;
                    $items[] = new \PhpParser\Node\Expr\ArrayItem(
                        self::buildNodeFromValue($itemValue),
                        self::buildNodeFromValue($itemKey)
                    );
                }
            }

            return new \PhpParser\Node\Expr\Array_($items);
        } else {
            throw new \LogicException('Invalid value');
        }
    }
}
