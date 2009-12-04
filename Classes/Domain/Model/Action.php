<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Sebastian Gebhard <sebastian.gebhard@gmail.com>
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
 * An action defined for a domain object
 *
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
class Tx_ExtbaseKickstarter_Domain_Model_Action {

	/**
	 * The action's name
	 * @var string
	 */
	protected $name;
	
	/**
	 * The domain object this action belongs to.
	 * @var Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	protected $domainObject;

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
	 * DO NOT CALL DIRECTLY! This is being called by addAction() automatically.
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject the domain object this actions belongs to
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

}
?>
