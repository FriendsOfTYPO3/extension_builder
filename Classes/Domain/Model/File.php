<?php
namespace EBT\ExtensionBuilder\Domain\Model;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 */
class File extends Container {

	protected $filePathAndName = '';

	/**
	 * @var NamespaceObject[]
	 */
	protected $namespaces = array();


	/**
	 * @var array all statements
	 */
	protected $stmts = array();

	/**
	 * @var \PHPParser_Node_Stmt[]
	 */
	protected $aliasDeclarations = array();

	/**
	 * @var array of FunctionObject
	 */
	protected $functions = array();

	/**
	 * @var string
	 */
	protected $comment;

	/**
	 */
	public function __clone() {
		$clonedClasses = array();
		foreach($this->classes as $class) {
			$clonedClasses = clone($class);
		}
		$this->classes = $clonedClasses;
	}

	/**
	 * @param ClassObject\ClassObject
	 */
	public function addClass(ClassObject\ClassObject $class) {
		$this->classes[] = $class;
	}

	/**
	 * @param string $className
	 * @return ClassObject\ClassObject|NULL
	 */
	public function getClassByName($className) {
		foreach ($this->getClasses() as $class) {
			if ($class->getName() == $className) {
				return $class;
			}
		}
		return NULL;
	}

	/**
	 * @return ClassObject\ClassObject[]
	 */
	public function getClasses() {
		if (count($this->namespaces) > 0) {
			return reset($this->namespaces)->getClasses();
		} else {
			return $this->classes;
		}
	}

	/**
	 * @return ClassObject\ClassObject
	 */
	public function getFirstClass() {
		if ($this->hasNamespaces()) {
			return reset($this->namespaces)->getFirstClass();
		}
		$classes = $this->getClasses();
		return reset($classes);

	}


	/**
	 * @param NamespaceObject
	 */
	public function addNamespace(NamespaceObject $namespace) {
		$this->namespaces[] = $namespace;
	}

	/**
	 * @return NamespaceObject[]
	 */
	public function getNamespaces() {
		return $this->namespaces;
	}

	/**
	 * get the first namespace of this file
	 * (only for convenience, most files only use one namespace)
	 * @return \EBT\ExtensionBuilder\Domain\Model\NamespaceObject
	 */
	public function getNamespace() {
		return current($this->namespaces);
	}

	/**
	 * @return bool
	 */
	public function hasNamespaces() {
		return (count($this->namespaces) > 0);
	}


	/**
	 * @param string $filePathAndName
	 */
	public function setFilePathAndName($filePathAndName) {
		$this->filePathAndName = $filePathAndName;
	}

	/**
	 * @return array
	 */
	public function getStmts() {
		$this->stmts = array();
		if ($this->hasNamespaces()) {
			foreach ($this->namespaces as $namespace) {
				$this->stmts[] = $namespace->getNode();
				foreach ($namespace->getAliasDeclarations() as $aliasDeclaration) {
					$this->stmts[] = $aliasDeclaration;
				}
				$this->addSubStatements($namespace);
			}
		} else {
			$this->addSubStatements($this);
		}
		return $this->stmts;
	}

	/**
	 * @param $parentObject either a file object or a namespace object
	 */
	protected function addSubStatements($parentObject) {

		foreach ($parentObject->getPreClassStatements() as $preInclude) {
			$this->stmts[] = $preInclude;
		}

		foreach ($parentObject->getClasses() as $class) {
			$this->stmts[] = $class->getNode();
		}

		foreach ($parentObject->getFunctions() as $function) {
			$this->stmts[] = $function->getNode();
		}

		foreach ($this->getPostClassStatements() as $postInclude) {
			$this->stmts[] = $postInclude;
		}
	}

	/**
	 * @param array $aliasDeclarations PHPParser_Node_Stmt
	 */
	public function addAliasDeclarations($aliasDeclarations) {
		$this->aliasDeclarations = $aliasDeclarations;
	}

	/**
	 * @return array PHPParser_Node_Stmt
	 */
	public function getAliasDeclarations() {
		return $this->aliasDeclarations;
	}

}

?>