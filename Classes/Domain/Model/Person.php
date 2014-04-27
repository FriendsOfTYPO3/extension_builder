<?php
namespace EBT\ExtensionBuilder\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Christoph Döhne
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
 * A person participating in the project somehow (i.e. as a developer).
 */
class Person {

	/**
	 * TODO make that work
	 * This Array contains all valid values for the role of a person.
	 * Extend here and in the locallang (mlang_Tx_ExtensionBuilder_domain_model_person_[rolekey from array]) to add new Roles.
	 *
	 * @var string[]
	 * @transient
	 */
	protected static $ROLES = array('developer', 'product_manager');

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * TODO validation?
	 *
	 * @var string
	 * @see \EBT\ExtensionBuilder\Domain\Model\Person::ROLES
	 */
	protected $role = '';

	/**
	 * @var string
	 */
	protected $email = '';

	/**
	 * @var string
	 */
	protected $company = '';

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * @param string $role
	 * @return void
	 */
	public function setRole($role) {
		$this->role = $role;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getCompany() {
		return $this->company;
	}

	/**
	 * @param string $company
	 * @return void
	 */
	public function setCompany($company) {
		$this->company = $company;
	}

}
