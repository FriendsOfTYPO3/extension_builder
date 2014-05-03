<?php
namespace EBT\ExtensionBuilder\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 * (c) 2010 Nico de Haen
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

class Plugin {

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $type = '';

	/**
	 * @var string
	 */
	protected $key = '';

	/**
	 * array('controller' => 'MyController', 'actions' => 'action1,action2')
	 *
	 * @var string[]
	 */
	protected $controllerActionCombinations = array();

	/**
	 * array('controller' => 'MyController', 'actions' => 'action1,action2')
	 *
	 * @var string[]
	 */
	protected $noncacheableControllerActions = array();

	/**
	 * @var string[]
	 */
	protected $switchableControllerActions = array();

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
	 * @param string $type
	 * @return void
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $key
	 * @return void
	 */
	public function setKey($key) {
		$this->key = strtolower($key);
	}

	/**
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @param array $controllerActionCombinations
	 * @return void
	 */
	public function setControllerActionCombinations(array $controllerActionCombinations) {
		$this->controllerActionCombinations = $controllerActionCombinations;
	}

	/**
	 * Used in fluid templates for localconf.php
	 * if controllerActionCombinations are empty we have to
	 * return NULL to enable test in condition
	 *
	 * @return array|NULL
	 */
	public function getControllerActionCombinations() {
		if (empty($this->controllerActionCombinations)) {
			return NULL;
		}
		return $this->controllerActionCombinations;
	}

	/**
	 * @param array $noncacheableControllerActions
	 * @return void
	 */
	public function setNoncacheableControllerActions(array $noncacheableControllerActions) {
		$this->noncacheableControllerActions = $noncacheableControllerActions;
	}

	/**
	 * @return array
	 */
	public function getNoncacheableControllerActions() {
		if (empty($this->noncacheableControllerActions)) {
			return NULL;
		}
		return $this->noncacheableControllerActions;
	}

	/**
	 * @param array $switchableControllerActions
	 * @return void
	 */
	public function setSwitchableControllerActions($switchableControllerActions) {
		$this->switchableControllerActions = $switchableControllerActions;
	}

	/**
	 * @return boolean
	 */
	public function getSwitchableControllerActions() {
		return $this->switchableControllerActions;
	}

}
