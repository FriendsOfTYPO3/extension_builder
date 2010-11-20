<?php
/***************************************************************
 *  Copyright notice
 *
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
 * @package ExtbaseKickstarter
 * @version $ID:$
 */

class Tx_ExtbaseKickstarter_Domain_Model_Plugin {

	/**
	 * @var array
	 */
	protected static $TYPES = array('list_type', 'CType');

	/**
	 * The plugin name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * The type
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * The plugin key
	 *
	 * @var string
	 */
	protected $key = '';

	/**
	 * Gets the Name
	 *
	 * @return string
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * Sets the Name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName ($name) {
		$this->name =$name;
	}

	/**
	 * Setter for type
	 *
	 * @param string $type
	 * @return void
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Getter for type
	 *
	 * @return string type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Setter for key
	 *
	 * @param string $key
	 * @return void
	 */
	public function setKey($key) {
		$this->key = preg_replace("/[^a-z0-9]/", '', strtolower($key));
	}

	/**
	 * Getter for key
	 *
	 * @return string key
	 */
	public function getKey() {
		return $this->key;
	}
}
?>
