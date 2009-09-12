<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ingmar Schlecht
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
 * Base class for all properties in the object schema.
 *
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
abstract class Tx_ExtbaseKickstarter_Domain_Model_AbstractProperty {
	/**
	 * Name of the property
	 * @var string
	 */
	protected $name;
	
	/**
	 * Description of property
	 * @var string
	 */
	protected $description;
	
	/**
	 * Whether the property is required
	 * @var boolean
	 */
	protected $required;
	
	/**
	 * Get property name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Set property name
	 * @param string $name Property name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get property description
	 * @return string Property description
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Set property description
	 * @param string $description Property description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	//abstract public function getTcaDefinition();

	/**
	 * Template Method which should return the type hinting information
	 * being used in PHPDoc Comments
	 * @return string
	 */
	abstract public function getTypeForComment();

	/**
	 * Template method which should return the type hint being used as PHP
	 * arguments
	 * @return string
	 */
	abstract public function getTypeHint();

	//abstract public function getLocallangEntry()

	//abstract public function getSqlTypeDefinition();
}
?>