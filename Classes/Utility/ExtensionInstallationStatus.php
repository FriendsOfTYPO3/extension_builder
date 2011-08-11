<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Sebastian Michaelsen
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

class Tx_ExtensionBuilder_Utility_ExtensionInstallationStatus {

	/**
	 * @var Tx_ExtensionBuilder_Domain_Model_Extension
	 */
	protected $extension;

	/**
	 * @var tx_em_Install
	 */
	protected $installTool;

	public function __construct() {
		if (t3lib_extMgm::isLoaded('install')) {
			$this->installTool = t3lib_div::makeInstance('tx_em_Install');
		}
	}

	/**
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 */
	public function setExtension($extension) {
		$this->extension = $extension;
	}

	public function getStatusMessage() {
		$statusMessage = '';

		if ($this->dbUpdateNeeded()) {
			$statusMessage .= '<p>Please update the database in the Extension Manager!</p>';
		}

		if (!t3lib_extMgm::isLoaded($this->extension->getExtensionKey())) {
			$statusMessage .= '<p>Your Extension is not installed yet.</p>';
		}

		return $statusMessage;
	}

	/**
	 * @param string $extKey
	 * @return boolean
	 */
	protected function dbUpdateNeeded() {
		if (t3lib_extMgm::isLoaded($this->extension->getExtensionKey()) && !empty($this->installTool)) {
			$updateNeeded = $this->installTool->checkDBupdates($this->extension->getExtensionKey(), array('type' => 'L', 'files' => array('ext_tables.sql')), 1);
			if (!empty($updateNeeded['structure']['diff']['extra'])) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
