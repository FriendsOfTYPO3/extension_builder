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
 * Backend Module of the Extbase Kickstarter extension
 *
 * @category    Controller
 * @package     TYPO3
 * @subpackage  tx_mvcextjssamples
 * @author      Ingmar Schlecht <ingmar@typo3.org>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_ExtbaseKickstarter_Controller_KickstarterModuleController extends Tx_Extbase_MVC_Controller_ActionController {

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
	 * @var Tx_ExtbaseKickstarter_Service_ExtensionSchemaBuilder
	 */
	protected $extensionSchemaBuilder;

	
	/**
	 * @var Tx_ExtbaseKickstarter_Service_CodeGenerator
	 */
	protected $codeGenerator;

	public function initializeAction() {
		if (!$this->extensionSchemaBuilder instanceof Tx_ExtbaseKickstarter_Service_ExtensionSchemaBuilder) {
			$this->injectExtensionSchemaBuilder(t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_ExtensionSchemaBuilder'));
			$this->injectCodeGenerator(t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_CodeGenerator'));
			$this->settings = $frameworkConfiguration['settings'];
			$this->settings['extConf'] = Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getKickstarterSettings();
		}
	}
	
	/**
	 * @param Tx_ExtbaseKickstarter_Service_ExtensionSchemaBuilder $extensionSchemaBuilder
	 * @return void
	 */
	public function injectExtensionSchemaBuilder(Tx_ExtbaseKickstarter_Service_ExtensionSchemaBuilder $extensionSchemaBuilder) {
		$this->extensionSchemaBuilder = $extensionSchemaBuilder;
	}

	/**
	 * @param Tx_ExtbaseKickstarter_Service_CodeGenerator $codeGenerator
	 * @return void
	 */
	public function injectCodeGenerator(Tx_ExtbaseKickstarter_Service_CodeGenerator $codeGenerator) {
		$this->codeGenerator = $codeGenerator;
	}

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->settings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
		$this->settings['extConf'] = Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getKickstarterSettings();
	}

	/**
	 * Index action for this controller.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
	// if the user has seen the introduction the domain modeller becomes the default view
		if(!$this->request->hasArgument('action')){
			$userSettings = $GLOBALS['BE_USER']->getModuleData('kickstarter');
			if($userSettings['firstTime']===0){
				$this->forward('domainmodelling');
			}
		}
	}

	public function domainmodellingAction() {
		$GLOBALS['BE_USER']->pushModuleData('kickstarter',array('firstTime'=>0));
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
				$extensionConfigurationFromJson = json_decode($request['params']['working'], true);
				$extensionConfigurationFromJson['modules'] = Tx_ExtbaseKickstarter_Utility_ModelImport::mapAdvancedMode($extensionConfigurationFromJson['modules']);
				$extensionConfigurationFromJson['modules'] = Tx_ExtbaseKickstarter_Utility_ModelImport::resetOutboundedPositions($extensionConfigurationFromJson['modules']);
				t3lib_div::devlog('JSON:','extbase_kickstarter',0,$extensionConfigurationFromJson);
				$extensionConfigurationFromJson = Tx_ExtbaseKickstarter_Utility_ModelImport::reArrangeRelations($extensionConfigurationFromJson);
				t3lib_div::devlog('JSON:','extbase_kickstarter',0,$extensionConfigurationFromJson);
				
				
				try {
					$extensionSchema = $this->extensionSchemaBuilder->build($extensionConfigurationFromJson);
				}
				catch(Exception $e){
					return json_encode(array('error' => $e->getMessage()));
				}

				// Validate the extension
				$extensionValidator = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Validator_ExtensionValidator');
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
							Tx_ExtbaseKickstarter_Service_RoundTrip::backupExtension($extensionSchema,$this->settings['extConf']['backupDir']);
						}
						catch(Exception $e){
							return json_encode(array('error' => $e->getMessage()));
						}
					}
					$extensionSettings =  Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getExtensionSettings($extensionSchema->getExtensionKey());
					if($this->settings['extConf']['enableRoundtrip'] == 1){
						if(empty($extensionSettings)){
							// no config file in an existing extension!
							// this would result in a total overwrite so we create one and give a warning
							Tx_ExtbaseKickstarter_Utility_ConfigurationManager::createInitialSettingsFile($extensionSchema);
							return json_encode(array('warning' => "<span class='error'>Roundtrip is enabled but no configuration file was found.</span><br />This might happen if you use the kickstarter the first time for this extension. <br />A settings file was generated in <br /><b>typo3conf/ext/".$extensionSchema->getExtensionKey()."/Configuration/Kickstarter/settings.yaml.</b><br />Configure the overwrite settings, then save again."));
						}
						try {
							Tx_ExtbaseKickstarter_Service_RoundTrip::prepareExtensionForRoundtrip($extensionSchema);
						} catch (Exception $e) {
							return json_encode(array('error' => $e->getMessage()));
						}
					}
				}
				
				
				$buildResult = $this->codeGenerator->build($extensionSchema);
				
				$extensionConfigurationFromJson['log'] = array(
					'last_modified'=>date('Y-m-d h:i'),
					'kickstarter_version'=>t3lib_extMgm::getExtensionVersion('extbase_kickstarter'),
					'be_user'=>$GLOBALS['BE_USER']->user['realName'].' ('.$GLOBALS['BE_USER']->user['uid'].')'
				);
				t3lib_div::writeFile($extensionDirectory . 'kickstarter.json', json_encode($extensionConfigurationFromJson));

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
			$jsonFile =  PATH_typo3conf . 'ext/' . $singleExtensionDirectory . '/kickstarter.json';
			if (file_exists($jsonFile)) {
				
				if($this->settings['extConf']['enableRoundtrip']){
					// generate unique IDs 
					$extensionConfigurationFromJson = json_decode(file_get_contents($jsonFile),true);
					$extensionConfigurationFromJson = Tx_ExtbaseKickstarter_Utility_ModelImport::getConfigurationFromKickstarterJson($extensionConfigurationFromJson);
					$extensionConfigurationFromJson['properties']['originalExtensionKey'] = $singleExtensionDirectory;
					t3lib_div::writeFile($jsonFile, json_encode($extensionConfigurationFromJson));
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
