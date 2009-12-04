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
 * Schema for a whole extension
 *
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
class Tx_ExtbaseKickstarter_Domain_Model_Extension {

	/**
	 * The extension key
	 * @var string
	 */
	protected $extensionKey;

	/**
	 * Extension's name
	 * @var string
	 */
	protected $name;

	/**
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * The extension's state. One of the STATE_* constants.
	 * @var integer
	 */
	protected $state = 0;

	const STATE_DEVELOPMENT = 0;
	const STATE_ALPHA = 1;
	const STATE_BETA = 2;
	const STATE_STABLE = 3;

	/**
	 * All domain objects
	 * @var array<Tx_ExtbaseKickstarter_Domain_Model_DomainObject>
	 */
	protected $domainObjects = array();
	
	/**
	 * The Perons working on the Extension
	 * @var array<Tx_ExtbaseKickstarter_Domain_Model_Person>
	 */
	protected $persons = array();

	/**
	 *
	 * @return string
	 */
	public function getExtensionKey() {
		return $this->extensionKey;
	}

	/**
	 *
	 * @param string $extensionKey
	 */
	public function setExtensionKey($extensionKey) {
		$this->extensionKey = $extensionKey;
	}

	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 *
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 *
	 * @return integer
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 *
	 * @param integer $state
	 */
	public function setState($state) {
		$this->state = $state;
	}

	/**
	 *
	 * @return array<Tx_ExtbaseKickstarter_Domain_Model_DomainObject>
	 */
	public function getDomainObjects() {
		return $this->domainObjects;
	}

	/**
	 * Add a domain object to the extension. Creates the reverse link as well.
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject 
	 */
	public function addDomainObject(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		$domainObject->setExtension($this);
		$this->domainObjects[$domainObject->getName()] = $domainObject;
	}

	public function getDomainObjectByName($domainObjectName) {
		if (isset($this->domainObjects[$domainObjectName])) {
			return $this->domainObjects[$domainObjectName];
		}
		return NULL;
	}

	public function getShorthandForTypoScript() {
		return 'tx_' . strtolower(Tx_Extbase_Utility_Extension::convertLowerUnderscoreToUpperCamelCase($this->getExtensionKey()));
	}
	
	/**
	 * Returns the Persons
	 * 
	 * @return array<Tx_ExtbaseKickstarter_Domain_Model_Person>
	 */
	public function getPersons() {
		return $this->persons;
	}
	/**
	 * Sets the Persons
	 * 
	 * @param array<Tx_ExtbaseKickstarter_Domain_Model_Person> $persons
	 * @return void
	 */
	public function setPersons($persons) {
		$this->persons=$persons;
	}
	/**
	 * Adds a Person to the end of the current Set of Persons.
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_Person $person
	 * @return void
	 */
	public function addPerson($person) {
		$this->persons[]=$person;
	}
	
}
?>