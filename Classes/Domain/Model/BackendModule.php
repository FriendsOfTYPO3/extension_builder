<?php
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

/**
 * A plugin in the extension
 *
 * @version $ID:$
 */

class Tx_ExtensionBuilder_Domain_Model_BackendModule {


	/**
	 * The name of the module
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * The description of the module
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * The tab label
	 *
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
	 * The module key
	 *
	 * @var string
	 */
	protected $key = '';

	/**
	 * array with configuration arrays
	 * array('controller' => 'MyController', 'actions' => 'action1,action2')
	 *
	 * @var array
	 */
	protected $controllerActionCombinations;

	/**
	 * Gets the Name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the Name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * Gets the Description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the Description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Gets the tab label
	 *
	 * @return string
	 */
	public function getTabLabel() {
		return $this->tabLabel;
	}

	/**
	 * Sets the tab label
	 *
	 * @param string $tablLabel
	 * @return void
	 */
	public function setTabLabel($tabLabel) {
		$this->tabLabel = $tabLabel;
	}

	/**
	 * Setter for mainModule
	 *	 /**
	 * Setter for key
	 *
	 * @param string $key
	 * @return void
	 */
	public function setKey($key) {
		$this->key = strtolower($key);
	}

	/**
	 * Getter for key
	 *
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
	 * Getter for mainModule
	 *
	 */
	public function getMainModule() {
		return $this->mainModule;
	}

	/**
	 * @param array $controllerActionCombinations
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

?>
