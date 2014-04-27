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

class NamespaceObject extends Container {

	/**
	 * array with alias declarations
	 *
	 * Each declaration is an array of the following type:
	 * array(name => alias)
	 *
	 * @var string[]
	 */
	protected $aliasDeclarations = array();

	/**
	 * @param string $name
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function getFirstClass() {
		$classes = $this->getClasses();
		return reset($classes);
	}

	/**
	 * @param string $aliasDeclaration
	 * @return void
	 */
	public function addAliasDeclaration($aliasDeclaration) {
		$this->aliasDeclarations[] = $aliasDeclaration;
	}

	/**
	 * @return string[]
	 */
	public function getAliasDeclarations() {
		return $this->aliasDeclarations;
	}

	/**
	 * @param array $preIncludes
	 * @return void
	 */
	public function setPreIncludes($preIncludes) {
		$this->preIncludes = $preIncludes;
	}

	/**
	 * @return array
	 */
	public function getPreIncludes() {
		return $this->preIncludes;
	}

	/**
	 * @param array $preInclude
	 * @return void
	 */
	public function addPreInclude($preInclude) {
		$this->preIncludes[] = $preInclude;
	}

	/**
	 * @param array $postIncludes
	 * @return void
	 */
	public function setPostIncludes($postIncludes) {
		$this->postIncludes = $postIncludes;
	}

	/**
	 * @return array
	 */
	public function getPostIncludes() {
		return $this->postIncludes;
	}

	/**
	 * @param array $postInclude
	 * @return void
	 */
	public function addPostInclude($postInclude) {
		$this->postIncludes[] = $postInclude;
	}

}
