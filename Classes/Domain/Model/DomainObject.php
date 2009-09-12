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
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 *
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
class Tx_ExtbaseKickstarter_Domain_Model_DomainObject {

	/**
	 * Name of the domain object
	 * @var string
	 */
	protected $name;

	/**
	 * Description of the domain object
	 * @var string
	 */
	protected $description;

	/**
	 * If TRUE, this is an aggregate root.
	 * @var boolean
	 */
	protected $aggregateRoot;

	/**
	 * If TRUE, this is an entity. If false, it is a ValueObject
	 * @var boolean
	 */
	protected $entity;

	protected $properties = array();
	
	/**
	 * Set name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Set name
	 * @param string Name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * Get description
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Set description
	 * @param string Description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getAggregateRoot() {
		return $this->aggregateRoot;
	}

	public function setAggregateRoot($aggregateRoot) {
		$this->aggregateRoot = (boolean)$aggregateRoot;
	}

	public function getEntity() {
		return $this->entity;
	}

	public function setEntity($entity) {
		$this->entity = (boolean)$entity;
	}

	public function addProperty(Tx_ExtbaseKickstarter_Domain_Model_AbstractProperty $property) {
		$this->properties[] = $property;
	}

	public function getProperties() {
		return $this->properties;
	}

	public function getBaseClass() {
		if ($this->entity) {
			return 'Tx_Extbase_DomainObject_AbstractEntity';
		} else {
			return 'Tx_Extbase_DomainObject_AbstractValueObject';
		}
	}


}

?>