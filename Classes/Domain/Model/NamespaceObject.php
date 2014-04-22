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
 * @author Nico de Haen
 * @package PhpParserApi
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class NamespaceObject extends Container{
	/**
	 * array with alias declarations
	 * each declaration is an array of type
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
	 * @return ClassObject\ClassObject
	 */
	public function getFirstClass() {
		$classes = $this->getClasses();
		return reset($classes);

	}

	public function addAliasDeclaration($aliasDeclaration) {
		$this->aliasDeclarations[] = $aliasDeclaration;
	}

	public function getAliasDeclarations() {
		return $this->aliasDeclarations;
	}

	/**
	 * @param array $preIncludes
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

	public function addPreInclude($preInclude) {
		$this->preIncludes[] = $preInclude;
	}

	public function setPostIncludes($postIncludes) {
		$this->postIncludes = $postIncludes;
	}

	public function getPostIncludes() {
		return $this->postIncludes;
	}


	public function addPostInclude($postInclude) {
		$this->postIncludes[] = $postInclude;
	}

}