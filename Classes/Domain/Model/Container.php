<?php
namespace EBT\ExtensionBuilder\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen
 *  All rights reserved
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
use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;

/**
 * Provides methods that are common to Class, File and Namespace objects
 */
class Container extends AbstractObject {

	/**
	 * associative array constName => constValue
	 *
	 * @var array
	 */
	protected $constants = array();

	/**
	 * @var array
	 */
	protected $preIncludes = array();

	/**
	 * @var array
	 */
	protected $postIncludes = array();

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\FunctionObject[]
	 */
	protected $functions = array();

	/**
	 * Contains all statements that occurred before the first class statement.
	 *
	 * @var array
	 */
	protected $preClassStatements = array();

	/**
	 * Contains all statements that occurred after the first class statement they
	 * will be rewritten after the last class!
	 *
	 * @var array
	 */
	protected $postClassStatements = array();

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
	 */
	protected $classes = array();

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function getFirstClass() {
		$classes = $this->getClasses();
		return reset($classes);
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $class
	 * @return void
	 */
	public function addClass(ClassObject $class) {
		$this->classes[] = $class;
	}

	/**
	 * @param array \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
	 * @return void
	 */
	public function setClasses($classes) {
		$this->classes = $classes;
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
	 */
	public function getClasses() {
		return $this->classes;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function setConstant($name, $value) {
		$this->constants[$name] = $value;
	}

	/**
	 * @return string constants
	 */
	public function getConstants() {
		return $this->constants;
	}

	/**
	 * @param $constantName
	 * @return mixed
	 */
	public function getConstant($constantName) {
		if (isset($this->constants[$constantName])) {
			return $this->constants[$constantName];
		} else {
			return NULL;
		}
	}

	/**
	 * @param string $constantName
	 * @return boolean TRUE if successfully removed
	 */
	public function removeConstant($constantName) {
		if (isset($this->constants[$constantName])) {
			unset($this->constants[$constantName]);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param $postInclude
	 * @return void
	 */
	public function addPostInclude($postInclude) {
		$this->postIncludes[] = $postInclude;
	}

	/**
	 * @return array
	 */
	public function getPostIncludes() {
		return $this->postIncludes;
	}

	/**
	 * @param $preInclude
	 * @return void
	 */
	public function addPreInclude($preInclude) {
		$this->preIncludes[] = $preInclude;
	}

	/**
	 * @return array
	 */
	public function getPreIncludes() {
		return $this->preIncludes;
	}

	/**
	 * @param array FunctionObject[]
	 * @return void
	 */
	public function setFunctions(array $functions) {
		$this->functions = $functions;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\FunctionObject $function
	 * @return void
	 */
	public function addFunction(FunctionObject $function) {
		$this->functions[$function->getName()] = $function;
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject[]
	 */
	public function getFunctions() {
		return $this->functions;
	}

	/**
	 * @param string $name
	 * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject
	 */
	public function getFunction($name) {
		if (isset($this->functions[$name])) {
			return $this->functions[$name];
		} else {
			return NULL;
		}
	}

	/**
	 * @param array $postClassStatements
	 * @return void
	 */
	public function addPostClassStatements($postClassStatements) {
		$this->postClassStatements[] = $postClassStatements;
	}

	/**
	 * @return array
	 */
	public function getPostClassStatements() {
		return $this->postClassStatements;
	}

	/**
	 * @param array $preClassStatements
	 * @return void
	 */
	public function addPreClassStatements($preClassStatements) {
		$this->preClassStatements[] = $preClassStatements;
	}

	/**
	 * @return array
	 */
	public function getPreClassStatements() {
		return $this->preClassStatements;
	}

}
