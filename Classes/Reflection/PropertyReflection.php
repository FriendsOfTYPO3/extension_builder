<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
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

/**
 * Extended version of the ReflectionProperty
 */
class Tx_ExtensionBuilder_Reflection_PropertyReflection extends TYPO3\CMS\Extbase\Reflection\PropertyReflection {

	/**
	 * the line number where this property is declared in the class file
	 * @var int
	 */
	protected $lineNumber;

	/**
	 * @var string description as found in docComment
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $rawComment;

	/**
	 * @var boolean
	 */
	protected $default;

	/**
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * getter for line number
	 * @return int line number
	 */
	public function getLineNumber() {

		return $this->lineNumber;
	}

	/**
	 * setter for line number
	 * @return int line number
	 */
	public function setLineNumber($lineNumber) {

		return $this->lineNumber = $lineNumber;
	}

	/**
	 * wrapper for Tx_Extbase_Reflection_PropertyReflection::getTagsValues()
	 * @return array $tags
	 */
	public function getTags() {
		// getTagsValues does not return an array with tag values only
		// as the name says, but an associative array with tagName=>tagValue
		return $this->getTagsValues();
	}

	/**
	 * getter for description
	 * @return string description
	 */
	public function getDescription() {
		if (empty($this->description)) {
			$this->description = $this->getDocCommentParser()->getDescription();
		}
		return $this->description;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isDefault() {
		return $this->default;
	}

	/**
	 *
	 * @param boolean $default
	 */
	public function setDefault($default) {
		$this->default = $default;
	}

	/**
	 *
	 * @return boolean
	 */
	public function getDefault() {
		return $this->default;
	}

	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Setter for value
	 *
	 * @param mixed
	 */
	public function setValue($value) {
		$this->value = $value;
	}


}

?>