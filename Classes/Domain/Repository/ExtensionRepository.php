<?php
namespace EBT\ExtensionBuilder\Domain\Repository;
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
*  All rights reserved
*
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
 * Repository for existing Extbase Extensions
 */
class ExtensionRepository implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager = NULL;

	/**
	 * @param \EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * loops through all extensions in typo3conf/ext/
	 * and searchs for a JSON file with extension builder configuration
	 * @return array
	 */
	public function findAll() {
		$result = array();
		$extensionDirectoryHandle = opendir(PATH_typo3conf . 'ext/');
		while (FALSE !== ($singleExtensionDirectory = readdir($extensionDirectoryHandle))) {
			if ($singleExtensionDirectory[0] == '.' || $singleExtensionDirectory[0] == '..' || !is_dir(PATH_typo3conf . 'ext/'.$singleExtensionDirectory)) {
				continue;
			}
			$extensionBuilderConfiguration = $this->configurationManager->getExtensionBuilderConfiguration($singleExtensionDirectory);
			if ($extensionBuilderConfiguration !== NULL) {
				$result[] = array(
					'name' => $singleExtensionDirectory,
					'working' => json_encode($extensionBuilderConfiguration)
				);
			}
		}
		closedir($extensionDirectoryHandle);

		return $result;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
	 */
	public function saveExtensionConfiguration(\EBT\ExtensionBuilder\Domain\Model\Extension $extension) {
		$extensionBuildConfiguration = $this->configurationManager->getConfigurationFromModeler();
		$extensionBuildConfiguration['log'] = array(
			'last_modified' => date('Y-m-d h:i'),
			'extension_builder_version' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getExtensionVersion('extension_builder'),
			'be_user' => $GLOBALS['BE_USER']->user['realName'] . ' (' . $GLOBALS['BE_USER']->user['uid'] . ')'
		);
		$encodeOptions = 0;
		// option JSON_PRETTY_PRINT is available since PHP 5.4.0
		if (defined('JSON_PRETTY_PRINT')) {
			$encodeOptions |= JSON_PRETTY_PRINT;
		}
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($extension->getExtensionDir() . \EBT\ExtensionBuilder\Configuration\ConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE, json_encode($extensionBuildConfiguration, $encodeOptions));
	}

}
