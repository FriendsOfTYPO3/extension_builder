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
	/**
	 * @var string
	 */
	protected $filePathAndName = '';

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\NamespaceObject[]
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
	 * @var \EBT\ExtensionBuilder\Domain\Model\FunctionObject[]
	 */
	protected $functions = array();

	/**
	 * @var string
	 */
	protected $comment = '';

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
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $class
	 */
	public function addClass(ClassObject\ClassObject $class) {
		$this->classes[] = $class;
	}

	/**
	 * @param string $className
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject|NULL
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
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
	 */
	public function getClasses() {
		if (count($this->namespaces) > 0) {
			return reset($this->namespaces)->getClasses();
		} else {
			return $this->classes;
		}
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function getFirstClass() {
		if ($this->hasNamespaces()) {
			return reset($this->namespaces)->getFirstClass();
		}
		$classes = $this->getClasses();
		return reset($classes);

	}


	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\NamespaceObject $namespace
	 */
	public function addNamespace(NamespaceObject $namespace) {
		$this->namespaces[] = $namespace;
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\NamespaceObject[]
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
