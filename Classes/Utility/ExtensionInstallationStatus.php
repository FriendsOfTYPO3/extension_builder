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
	 * @var \TYPO3\CMS\Install\Install
	 */
	protected $installTool;

	public function __construct() {
		$this->installTool = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
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

		if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($this->extension->getExtensionKey())) {
			$statusMessage .= '<p>Your Extension is not installed yet.</p>';
		}

		return $statusMessage;
	}

	/**
	 * @param string $extKey
	 * @return boolean
	 */
	protected function dbUpdateNeeded() {
		// TODO
		$sqlFile = $this->extension->getExtensionDir().'ext_tables.sql';
		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($this->extension->getExtensionKey()) && file_exists($sqlFile)) {
			$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
			$installToolSqlParser = $this->objectManager->get('TYPO3\\CMS\\Install\\Sql\\SchemaMigrator');
			$sqlContent = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($sqlFile);
			$GLOBALS['typo3CacheManager']->setCacheConfigurations($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']);
			$sqlContent .= \TYPO3\CMS\Core\Cache\Cache::getDatabaseTableDefinitions();
			$fieldDefinitionsFromFile = $installToolSqlParser->getFieldDefinitions_fileContent($sqlContent);
			if (count($fieldDefinitionsFromFile)) {
				$fieldDefinitionsFromCurrentDatabase = $installToolSqlParser->getFieldDefinitions_database();
				$diff = $installToolSqlParser->getDatabaseExtra($fieldDefinitionsFromFile, $fieldDefinitionsFromCurrentDatabase);
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('diff','extension_builder',0,$diff);
				if(!empty($diff['extra']) || !empty($diff['diff']) || !empty($diff['diff_currentValues'])) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}
