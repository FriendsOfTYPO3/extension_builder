<?php
namespace EBT\ExtensionBuilder\Parser\Visitor;
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
use \EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use \PhpParser\Node;

/**
* provides methods to import a class object and methods and properties
*
* @author Nico de Haen
*/
class FileVisitor extends \PhpParser\NodeVisitorAbstract implements FileVisitorInterface{
	/**
	 * @var array
	 */
	protected $properties = array();

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	protected $currentClassObject = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\NamespaceObject
	 */
	protected $currentNamespace = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\Container
	 */
	protected $currentContainer = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\File
	 */
	protected $fileObject = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Parser\ClassFactoryInterface
	 */
	protected $classFactory = NULL;

	/**
	 * @var bool
	 */
	protected $onFirstLevel = TRUE;

	/**
	 * currently not used, might be useful for filtering etc.
	 * it keeps a reference to the current "first level" node
	 *
	 * @var array
	 */
	protected $contextStack = array();

	/**
	 * @var \PhpParser\Node
	 */
	protected $lastNode = NULL;

	public function getFileObject() {
		return $this->fileObject;
	}

	/**
	 *
	 *
	 * @param \PhpParser\Node $node
	 */
	public function enterNode(\PhpParser\Node $node) {
		$this->contextStack[] = $node;
		if ($node instanceof Node\Stmt\Namespace_) {
			$this->currentNamespace = $this->classFactory->buildNamespaceObject($node);
			$this->currentContainer = $this->currentNamespace;
		} elseif ($node instanceof Node\Stmt\Class_) {
			$this->currentClassObject = $this->classFactory->buildClassObject($node);
			$this->currentContainer = $this->currentClassObject;
		}
	}

	/**
	 * @param \PhpParser\Node $node
	 */
	public function leaveNode(\PhpParser\Node $node){
		array_pop($this->contextStack);
		if ($this->isContainerNode(end($this->contextStack)) || count($this->contextStack) === 0) {
			// we are on the first level
			if ($node instanceof Node\Stmt\Class_) {
				if (count($this->contextStack) > 0) {
					if (end($this->contextStack)->getType() == 'Stmt_Namespace') {
						$currentNamespaceName = NodeConverter::getValueFromNode(end($this->contextStack));
						$this->currentClassObject->setNamespaceName($currentNamespaceName);
						$this->currentNamespace->addClass($this->currentClassObject);
					}
				} else {
					$this->fileObject->addClass($this->currentClassObject);
					$this->currentClassObject = NULL;
					$this->currentContainer = $this->fileObject;
				}
			} elseif ($node instanceof Node\Stmt\Namespace_) {
				if (NULL !== $this->currentNamespace) {
					$this->fileObject->addNamespace($this->currentNamespace);
					$this->currentNamespace = NULL;
					$this->currentContainer = $this->fileObject;
				} else {
					//TODO: find how this could happen
				}
			} elseif ($node instanceof Node\Stmt\Use_) {
				$this->currentContainer->addAliasDeclaration(
					NodeConverter::convertUseAliasStatementNodeToArray($node)
				);
			} elseif ($node instanceof Node\Stmt\ClassConst) {
				$constants = NodeConverter::convertClassConstantNodeToArray($node);
				foreach($constants as $constant) {
					$this->currentContainer->setConstant($constant['name'],$constant['value']);
				}
			} elseif ($node instanceof Node\Stmt\ClassMethod) {
				$this->onFirstLevel = TRUE;
				$method = $this->classFactory->buildClassMethodObject($node);
				$this->currentClassObject->addMethod($method);
			} elseif ($node instanceof Node\Stmt\Property) {
				$property = $this->classFactory->buildPropertyObject($node);
				$this->currentClassObject->addProperty($property);
			} elseif ($node instanceof Node\Stmt\Function_) {
				$this->onFirstLevel = TRUE;
				$function = $this->classFactory->buildFunctionObject($node);
				$this->currentContainer->addFunction($function);
			} elseif (!$node instanceof Node\Name) {
				// any other nodes (except the name node of the current container node)
				// go into statements container
				if ($this->currentContainer->getFirstClass() === FALSE) {
					$this->currentContainer->addPreClassStatements($node);
				} else {
					$this->currentContainer->addPostClassStatements($node);
				}
			}
		}
	}

	/**
	 * @param array $nodes
	 */
	public function beforeTraverse(array $nodes){
		$this->fileObject =  new \EBT\ExtensionBuilder\Domain\Model\File;
		$this->currentContainer = $this->fileObject;
	}


	/**
	 * @param \EBT\ExtensionBuilder\Parser\ClassFactoryInterface $classFactory
	 */
	public function setClassFactory($classFactory) {
		$this->classFactory = $classFactory;
	}

	protected function isContainerNode($node) {
		return ($node instanceof Node\Stmt\Namespace_ || $node instanceof Node\Stmt\Class_);
	}

	protected function addLastNode() {
		if ($this->lastNode === NULL) {
			return;
		}
		if ($this->currentContainer->getFirstClass() === FALSE) {
			$this->currentContainer->addPreClassStatements($this->lastNode);
		} else {
			$this->currentContainer->addPostClassStatements($this->lastNode);
		}
		$this->lastNode = NULL;
	}

}
