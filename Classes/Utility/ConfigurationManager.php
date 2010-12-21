<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Nico de Haen
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
 * Load settings from yaml file and from TYPO3_CONF_VARS extConf
 *
 * @package Extbase
 * @subpackage Utility
 */
class Tx_ExtbaseKickstarter_Utility_ConfigurationManager {
	
	public static $settingsDir = 'Configuration/Kickstarter/';
	
	public static function getKickstarterSettings(){
		$settings = array();
		if(!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extbase_kickstarter'])){
			$settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['extbase_kickstarter']);
		}
		return $settings;
	}
	
	/**
	 *
	 * @param string $extensionKey
	 * @return array settings
	 */
	public static function getExtensionSettings($extensionKey){
		$settings = array();
		$settingsFile = self::getSettingsFile($extensionKey);
		if (file_exists($settingsFile)) {
			$yamlParser = new Tx_ExtbaseKickstarter_Utility_SpycYAMLParser();
			$settings = $yamlParser->YAMLLoadString(file_get_contents($settingsFile));
		}
		else t3lib_div::devlog('No settings found: '.$settingsFile,'extbase_kickstarter',2);

		return $settings;
	}
	
	/**
	 * get the file name and path of the settings file
	 * @param string $extensionKey
	 * @return string path
	 */
	public static function getSettingsFile($extensionKey){
		$extensionDir = PATH_typo3conf.'ext/'.$extensionKey.'/';
		$settingsFile =  $extensionDir.self::$settingsDir . 'settings.yaml';
		return $settingsFile;
	}

	
	static public function createInitialSettingsFile($extension){
		t3lib_div::mkdir_deep($extension->getExtensionDir(),self::$settingsDir);
		$settings = file_get_contents(t3lib_extMgm::extPath('extbase_kickstarter').'Resources/Private/CodeTemplates/Configuration/Kickstarter/settings.yamlt');
		$settings = str_replace('{extension.extensionKey}',$extension->getExtensionKey(),$settings);
		$settings = str_replace('<f:format.date>now</f:format.date>',date('Y-m-d H:i'),$settings);
		t3lib_div::writeFile($extension->getExtensionDir().self::$settingsDir.'settings.yaml', $settings);
	}

	
}

?>