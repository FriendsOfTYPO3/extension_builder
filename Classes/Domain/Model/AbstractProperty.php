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
	 * The domain object this property belongs to.
	 * @var Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	protected $domainObject;

	/**
	 * DO NOT CALL DIRECTLY! This is being called by addProperty() automatically.
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject the domain object this property belongs to
	 */
	public function setDomainObject(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		$this->domainObject = $domainObject;
	}

	/**
	 *
	 * @return Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	public function getDomainObject() {
		return $this->domainObject;
	}


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
	
	
	public function getRequired() {
		return $this->required;
	}

	public function setRequired($required) {
		$this->required = $required;
	}

	public function getValidateAnnotation() {
		if ($this->required) {
			return '@validate NotEmpty';
		}
		return '';
	}


	//abstract public function getLocallangEntry()

	abstract public function getSqlDefinition();
}
?>