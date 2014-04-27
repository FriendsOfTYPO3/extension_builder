<?php
namespace EBT\ExtensionBuilder\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nico de Haen
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

class BackendModule {

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $description = '';

	/**
	 * @var string
	 */
	protected $tabLabel = '';

	/**
	 * The mainModule of the module (default is 'web')
	 *
	 * @var string
	 */
	protected $mainModule = 'web';

	/**
	 * @var string
	 */
	protected $key = '';

	/**
	 * array with configuration arrays
	 *
	 * array('controller' => 'MyController', 'actions' => 'action1,action2')
	 *
	 * @var string[]
	 */
	protected $controllerActionCombinations = array();

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
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getTabLabel() {
		return $this->tabLabel;
	}

	/**
	 * @param string $tabLabel
	 * @return void
	 */
	public function setTabLabel($tabLabel) {
		$this->tabLabel = $tabLabel;
	}

	/**
	 * @param string $key
	 * @return void
	 */
	public function setKey($key) {
		$this->key = strtolower($key);
	}

	/**
	 * @return string key
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * @param $mainModule
	 * @return void
	 */
	public function setMainModule($mainModule) {
		$this->mainModule = $mainModule;
	}

	/**
	 * @return string
	 */
	public function getMainModule() {
		return $this->mainModule;
	}

	/**
	 * @param array $controllerActionCombinations
	 * @return void
	 */
	public function setControllerActionCombinations(array $controllerActionCombinations) {
		$this->controllerActionCombinations = $controllerActionCombinations;
	}

	/**
	 * @return array
	 */
	public function getControllerActionCombinations() {
		return $this->controllerActionCombinations;
	}

}
