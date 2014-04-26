<?php
namespace EBT\ExtensionBuilder\Parser;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
 *  All rights reserved
 *
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @author Nico de Haen
 */

class NodeFactory implements \TYPO3\CMS\Core\SingletonInterface{

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
	 * @param bool $skipStatements
	 * @return \PHPParser_Node_Stmt_Class
	 */
	public function buildClassNode($classObject, $skipStatements = FALSE) {
		$factory = new \PHPParser_BuilderFactory;

		$classNodeBuilder = $factory->class($classObject->getName());
		if ($classObject->getParentClassName()) {
			$classNodeBuilder->extend(self::buildNodeFromName($classObject->getParentClassName()));
		}
		$interfaceNames = $classObject->getInterfaceNames();
		if (count($interfaceNames) > 0) {
			call_user_func_array( array($classNodeBuilder, 'implement'), $interfaceNames);
		}

		if (!$skipStatements) {
			$stmts = array();

			$properties = array();
			$methods = array();

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
	public function getFileStatements(\EBT\ExtensionBuilder\Domain\Model\File $fileObject) {

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
	protected function getContainerStatements(\EBT\ExtensionBuilder\Domain\Model\Container $container) {
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
	 * @return \PHPParser_Node_Stmt_ClassMethod
	 */
	public function buildMethodNode(\EBT\ExtensionBuilder\Domain\Model\ClassObject\Method $methodObject) {
		$factory = new \PHPParser_BuilderFactory;
		$methodNodeBuilder = $factory->method($methodObject->getName());
		$parameters = $methodObject->getParameters();
		if (count($parameters) > 0) {
			foreach($parameters as $parameter) {
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
	 * @return \PHPParser_Node_Param
	 */
	public function buildParameterNode(\EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter $parameter) {
		$factory = new \PHPParser_BuilderFactory;
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
	 * @param \PHPParser_Node_Stmt $node
	 */
	protected function addCommentAttributes(\EBT\ExtensionBuilder\Domain\Model\AbstractObject $object, \PHPParser_Node_Stmt $node) {
		$commentAttributes = array();
		$comments = $object->getComments();
		if (count($comments) > 0) {
			foreach($comments as $comment) {
				$commentAttributes[] = new \PHPParser_Comment($comment);
			}
		}
		if ($object->hasDescription() || $object->hasTags()) {
			$commentAttributes[] = new \PHPParser_Comment_Doc($object->getDocComment());
		}
		$node->setAttribute('comments', $commentAttributes);
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\NamespaceObject $nameSpace
	 */
	public function buildNamespaceNode(\EBT\ExtensionBuilder\Domain\Model\NamespaceObject $nameSpace) {
		return new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name($nameSpace->getName()));
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property $property
	 * @return \PHPParser_Node_Stmt_Property
	 */
	public function buildPropertyNode(\EBT\ExtensionBuilder\Domain\Model\ClassObject\Property $property) {
		$factory = new \PHPParser_BuilderFactory;
		$propertyNodeBuilder = $factory->property($property->getName());

		$propertyNode = $propertyNodeBuilder->getNode();
		$propertyNode->type = $property->getModifiers();

		foreach($propertyNode->props as $subNode) {
			if ($subNode instanceof \PHPParser_Node_Stmt_PropertyProperty) {
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
	 * @return \PHPParser_Node_Const
	 */
	public static function buildConstantNode($name, $value) {
		$constantNode = new \PHPParser_Node_Const($name, self::buildNodeFromValue($value));
		return $constantNode;
	}

	/**
	 * @static
	 * @param string $name
	 * @param mixed $value
	 * @return \PHPParser_Node_Const
	 */
	public static function buildClassConstantNode($name, $value) {
		$constantNode = new \PHPParser_Node_Stmt_ClassConst(array(self::buildConstantNode($name, $value)));
		return $constantNode;
	}

	/**
	 * @static
	 * @param string $name
	 * @param string $alias
	 * @return \PHPParser_Node_Stmt_Use
	 */
	public static function buildUseStatementNode($name, $alias) {
		$useStatementNode = new \PHPParser_Node_Stmt_Use(array('uses' => new \PHPParser_Node_Stmt_UseUse(self::buildNodeFromName($name), $alias)));
		return $useStatementNode;
	}


	/**
	 * Normalizes a name: Converts plain string names to \PHPParser_Node_Name.
	 *
	 * @param \PHPParser_Node_Name|string $name The name to normalize
	 *
	 * @return \PHPParser_Node_Name The normalized name
	 */
	static public function buildNodeFromName($name) {
		if ($name instanceof \PHPParser_Node_Name) {
			return $name;
		} else {
			return new \PHPParser_Node_Name($name);
		}
	}

    /**
     * Normalizes a value: Converts nulls, booleans, integers,
     * floats, strings and arrays into their respective nodes
     *
     * @param mixed $value The value to normalize
     *
     * @return \PHPParser_Node_Expr The normalized value
     */
    protected function buildNodeFromValue($value) {
        if ($value instanceof \PHPParser_Node) {
            return $value;
        } elseif (is_null($value)) {
            return new \PHPParser_Node_Expr_ConstFetch(
                new \PHPParser_Node_Name('NULL')
            );
        } elseif (is_bool($value)) {
            return new \PHPParser_Node_Expr_ConstFetch(
                new \PHPParser_Node_Name($value ? 'TRUE' : 'FALSE')
            );
        } elseif (is_int($value)) {
            return new \PHPParser_Node_Scalar_LNumber($value);
        } elseif (is_float($value)) {
            return new \PHPParser_Node_Scalar_DNumber($value);
        } elseif (is_string($value)) {
            return new \PHPParser_Node_Scalar_String($value);
        } elseif (is_array($value)) {
            $items = array();
            $lastKey = -1;
            foreach ($value as $itemKey => $itemValue) {
                // for consecutive, numeric keys don't generate keys
                if (null !== $lastKey && ++$lastKey === $itemKey) {
                    $items[] = new \PHPParser_Node_Expr_ArrayItem(
                        self::buildNodeFromValue($itemValue)
                    );
                } else {
                    $lastKey = null;
                    $items[] = new \PHPParser_Node_Expr_ArrayItem(
                        self::buildNodeFromValue($itemValue),
                        self::buildNodeFromValue($itemKey)
                    );
                }
            }

            return new \PHPParser_Node_Expr_Array($items);
        } else {
            throw new \LogicException('Invalid value');
        }
    }

}
