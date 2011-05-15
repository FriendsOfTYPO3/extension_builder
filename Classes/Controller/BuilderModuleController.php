<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ingmar Schlecht <ingmar@typo3.org>
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
 * Backend Module of the Extension Builder extension
 *
 * @category    Controller
 * @package     TYPO3
 * @subpackage  tx_extensionbuilder
 * @author      Ingmar Schlecht <ingmar@typo3.org>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_ExtensionBuilder_Controller_BuilderModuleController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Holds reference to the template class
	 *
	 * @var template
	 */
	protected $doc;

	/**
	 * Holds reference to t3lib_SCbase
	 *
	 * @var t3lib_SCbase
	 */
	protected $scBase;


	/**
	 * @var Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder
	 */
	protected $extensionSchemaBuilder;


	/**
	 * @var Tx_ExtensionBuilder_Service_CodeGenerator
	 */
	protected $codeGenerator;


	/**
	 * @param Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder $extensionSchemaBuilder
	 * @return void
	 */
	public function injectExtensionSchemaBuilder(Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder $extensionSchemaBuilder) {
		$this->extensionSchemaBuilder = $extensionSchemaBuilder;
	}

	/**
	 * @param Tx_ExtensionBuilder_Service_CodeGenerator $codeGenerator
	 * @return void
	 */
	public function injectCodeGenerator(Tx_ExtensionBuilder_Service_CodeGenerator $codeGenerator) {
		$this->codeGenerator = $codeGenerator;
	}

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
		$typoscript = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = Tx_ExtensionBuilder_Utility_ConfigurationManager::getSettings($typoscript);
	}
	
	
	/**
	 * Index action for this controller.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		if(floatval(t3lib_extMgm::getExtensionVersion('extbase')) < 1.3){
			die('The Extension Builder requires at least Extbase/Fluid Version 1.3. Sorry!');
		}
		t3lib_div::devlog('Settings','extension_builder',0,$this->settings);
		// if the user has seen the introduction the domain modeler becomes the default view
		if(!$this->request->hasArgument('action')){
			$userSettings = $GLOBALS['BE_USER']->getModuleData('extensionbuilder');
			if($userSettings['firstTime']===0){
				$this->forward('domainmodelling');
			}
		}
	}

	public function domainmodellingAction() {
		$GLOBALS['BE_USER']->pushModuleData('extensionbuilder',array('firstTime'=>0));
	}

	/**
	 * Main entry point for the buttons in the frontend
	 * @return string
	 * @todo rename this action
	 */
	public function generateCodeAction() {
		$jsonString = file_get_contents('php://input');
		$request = json_decode($jsonString, true);
		switch ($request['method']) {

			case 'saveWiring':
				$extensionConfigurationJSON = json_decode($request['params']['working'], true);
				$extensionBuildConfiguration = Tx_ExtensionBuilder_Utility_ConfigurationManager::fixExtensionBuilderJSON($extensionConfigurationJSON);
				//t3lib_div::devlog('JSON:','extension_builder',0,$extensionBuildConfiguration);
				try {
					$extensionSchema = $this->extensionSchemaBuilder->build($extensionBuildConfiguration);
				}
				catch(Exception $e){
					return json_encode(array('error' => $e->getMessage()));
				}

				// Validate the extension
				$extensionValidator = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator');
				try {
					$extensionValidator->isValid($extensionSchema);
				} catch (Exception $e) {
					return json_encode(array('error' => $e->getMessage()));
				}

				$extensionDirectory = $extensionSchema->getExtensionDir();

				if(!is_dir($extensionDirectory)){
					t3lib_div::mkdir($extensionDirectory);
				}
				else {
					if($this->settings['extConf']['backupExtension'] == 1){
						try {
							Tx_ExtensionBuilder_Service_RoundTrip::backupExtension($extensionSchema,$this->settings['extConf']['backupDir']);
						}
						catch(Exception $e){
							return json_encode(array('error' => $e->getMessage()));
						}
					}
					$extensionSettings =  Tx_ExtensionBuilder_Utility_ConfigurationManager::getExtensionSettings($extensionSchema->getExtensionKey());
					if($this->settings['extConf']['enableRoundtrip'] == 1){
						if(empty($extensionSettings)){
							// no config file in an existing extension!
							// this would result in a total overwrite so we create one and give a warning
							Tx_ExtensionBuilder_Utility_ConfigurationManager::createInitialSettingsFile($extensionSchema,$this->settings['codeTemplateRootPath']);
							return json_encode(array('warning' => "<span class='error'>Roundtrip is enabled but no configuration file was found.</span><br />This might happen if you use the extension builder the first time for this extension. <br />A settings file was generated in <br /><b>typo3conf/ext/".$extensionSchema->getExtensionKey()."/Configuration/ExtensionBuilder/settings.yaml.</b><br />Configure the overwrite settings, then save again."));
						}
						try {
							Tx_ExtensionBuilder_Service_RoundTrip::prepareExtensionForRoundtrip($extensionSchema);
						} catch (Exception $e) {
							return json_encode(array('error' => $e->getMessage()));
						}
					}
				}

				$this->codeGenerator->injectSettings($this->settings);
				$buildResult = $this->codeGenerator->build($extensionSchema);

				$extensionBuildConfiguration['log'] = array(
					'last_modified'=>date('Y-m-d h:i'),
					'extension_builder_version'=>t3lib_extMgm::getExtensionVersion('extension_builder'),
					'be_user'=>$GLOBALS['BE_USER']->user['realName'].' ('.$GLOBALS['BE_USER']->user['uid'].')'
				);
				t3lib_div::writeFile($extensionDirectory . 'ExtensionBuilder.json', json_encode($extensionBuildConfiguration));

				if ($buildResult === 'success') {
					$message = 'The Extension was saved';
					if($this->dbUpdateNeeded($extensionSchema->getExtensionKey())){
						$message .= '<br /><br />Please update the database in the Extension Manager!';
					}
					return json_encode(array('success' => $message));
				} else {
					return json_encode(array('error' => $buildResult));
				}

			break;
			case 'listWirings':
				$result = $this->getWirings();

				$response = array ('id' => $request['id'],'result' => $result,'error' => NULL);
				header('content-type: text/javascript');
				echo json_encode($response);
				exit();
		}
	}

	protected function getWirings() {
		$result = array();

		$extensionDirectoryHandle = opendir(PATH_typo3conf . 'ext/');
		while (false !== ($singleExtensionDirectory = readdir($extensionDirectoryHandle))) {
			if ($singleExtensionDirectory[0] == '.'){
				continue;
			}
			$oldJsonFile =  PATH_typo3conf . 'ext/' . $singleExtensionDirectory . '/kickstarter.json';
			$jsonFile =  PATH_typo3conf . 'ext/' . $singleExtensionDirectory . '/ExtensionBuilder.json';
			if (file_exists($oldJsonFile)) {
				rename($oldJsonFile, $jsonFile);
			}

			if (file_exists($jsonFile)) {

				if($this->settings['extConf']['enableRoundtrip']){
					// generate unique IDs
					$extensionConfigurationJSON = json_decode(file_get_contents($jsonFile),true);
					$extensionConfigurationJSON = Tx_ExtensionBuilder_Utility_ConfigurationManager::fixExtensionBuilderJSON($extensionConfigurationJSON);
					$extensionConfigurationJSON['properties']['originalExtensionKey'] = $singleExtensionDirectory;
					t3lib_div::writeFile($jsonFile, json_encode($extensionConfigurationJSON));
				}

				$result[] = array(
					'name' => $singleExtensionDirectory,
					'working' => file_get_contents($jsonFile)
				);
			}
		}
		closedir($extensionDirectoryHandle);

		return $result;
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		$this->scBase->MOD_MENU = Array (
			'function' => Array (
				'1' => 'Menu 1',
				'2' => 'Menu 2',
				'3' => 'Menu 3',
			)
		);
		$this->scBase->menuConfig();
	}




	/**
	 * TODO: Is there a real API for this?
	 * TODO: SHould better be moved to where??
	 * @param string $extKey
	 * @return boolean
	 */
	protected function dbUpdateNeeded($extKey){
		if(t3lib_extMgm::isLoaded($extKey) && class_exists('tx_em_Install')){
			$installTool = t3lib_div::makeInstance('tx_em_Install');
			$updateNeeded = $installTool->checkDBupdates($extKey, array('type'=>'L','files'=>array('ext_tables.sql')),1);
			if(!empty($updateNeeded['structure']['diff']['extra'])){
				return true;
			}
		}
		return false;
	}


}

?>
