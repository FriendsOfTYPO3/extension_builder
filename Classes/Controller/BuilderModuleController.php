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
 * @category	Controller
 * @package	 TYPO3
 * @subpackage  tx_extensionbuilder
 * @author	  Ingmar Schlecht <ingmar@typo3.org>
 * @license	 http://www.gnu.org/copyleft/gpl.html
 */
class Tx_ExtensionBuilder_Controller_BuilderModuleController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_ExtensionBuilder_Service_CodeGenerator
	 */
	protected $codeGenerator;

	/**
	 * @var Tx_ExtensionBuilder_Configuration_ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @var Tx_ExtensionBuilder_Utility_ExtensionInstallationStatus
	 */
	protected $extensionInstallationStatus;

	/**
	 * @var Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder
	 */
	protected $extensionSchemaBuilder;

	/**
	 * @var Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator
	 */
	protected $extensionValidator;

	/**
	 * @var array settings
	 */
	protected $settings;

	/**
	 * @param Tx_ExtensionBuilder_Service_CodeGenerator $codeGenerator
	 * @return void
	 */
	public function injectCodeGenerator(Tx_ExtensionBuilder_Service_CodeGenerator $codeGenerator) {
		$this->codeGenerator = $codeGenerator;
	}

	/**
	 * @param Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->settings = $this->configurationManager->getSettings();
	}

	/**
	 * @param Tx_ExtensionBuilder_Utility_ExtensionInstallationStatus $extensionInstallationStatus
	 * @return void
	 */
	public function injectExtensionInstallationStatus(Tx_ExtensionBuilder_Utility_ExtensionInstallationStatus $extensionInstallationStatus) {
		$this->extensionInstallationStatus = $extensionInstallationStatus;
	}

	/**
	 * @param Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder $extensionSchemaBuilder
	 * @return void
	 */
	public function injectExtensionSchemaBuilder(Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder $extensionSchemaBuilder) {
		$this->extensionSchemaBuilder = $extensionSchemaBuilder;
	}

	/**
	 * @param Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator $extensionValidator
	 * @return void
	 */
	public function injectExtensionValidator(Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator $extensionValidator) {
		$this->extensionValidator = $extensionValidator;
	}

	/**
	 * @return void
	 */
	public function initializeAction() {

		$this->codeGenerator->setSettings($this->settings);

		if (floatval(t3lib_extMgm::getExtensionVersion('extbase')) < 1.3) {
			die('The Extension Builder requires at least Extbase/Fluid Version 1.3. Sorry!');
		}

	}

	/**
	 * Index action for this controller.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		if (!$this->request->hasArgument('action')) {
			$userSettings = $GLOBALS['BE_USER']->getModuleData('extensionbuilder');
			if ($userSettings['firstTime'] === 0) {
				$this->forward('domainmodelling');
			}
		}
	}

	public function domainmodellingAction() {
		$GLOBALS['BE_USER']->pushModuleData('extensionbuilder', array('firstTime' => 0));
	}

	/**
	 * Main entry point for the buttons in the frontend
	 * @return string
	 */
	public function generateCodeAction() {
		try {
			$subAction = $this->generateCodeAction_getSubAction();
			switch ($subAction) {
				case 'saveWiring':
					$response = $this->generateCodeAction_saveWiring();
					break;
				case 'listWirings':
					$response = $this->generateCodeAction_listWirings();
					break;
				default:
					$response = array('error' => 'Sub Action not found.');
			}
		} catch (Exception $e) {
			$response = array('error' => $e->getMessage());
		}
		return json_encode($response);
	}

	protected function generateCodeAction_getSubAction() {
		$this->configurationManager->parseRequest();
		$subAction = $this->configurationManager->getSubActionFromRequest();
		if (empty($subAction)) {
			throw new Exception('No Sub Action!');
		}
		return $subAction;
	}

	protected function generateCodeAction_saveWiring() {
		try {
			$extensionBuildConfiguration = $this->configurationManager->getConfigurationFromModeler();
			$extension = $this->extensionSchemaBuilder->build($extensionBuildConfiguration);
		}
		catch (Exception $e) {
			throw $e;
		}

		// Validate the extension
		$validationResult = $this->extensionValidator->isValid($extension);
		if(!empty($validationResult['errors'])){
			$errorMessage = '';
			foreach($validationResult['errors'] as $exception){
				$errorMessage .= '<br />'.$exception->getMessage();
			}
			throw new Exception($errorMessage);
		}
		if(!empty($validationResult['warnings'])){
			$confirmationRequired = $this->handleValidationWarnings($validationResult['warnings']);
			if(!empty($confirmationRequired)){
				return $confirmationRequired;
			}
		}


		$extensionDirectory = $extension->getExtensionDir();

		if (!is_dir($extensionDirectory)) {
			t3lib_div::mkdir($extensionDirectory);
		} else {
			if ($this->settings['extConf']['backupExtension'] == 1) {
				try {
					Tx_ExtensionBuilder_Service_RoundTrip::backupExtension($extension, $this->settings['extConf']['backupDir']);
				}
				catch (Exception $e) {
					throw $e;
				}
			}
			$extensionSettings = $this->configurationManager->getExtensionSettings($extension->getExtensionKey());
			if ($this->settings['extConf']['enableRoundtrip'] == 1) {
				if (empty($extensionSettings)) {
					// no config file in an existing extension!
					// this would result in a total overwrite so we create one and give a warning
					$this->configurationManager->createInitialSettingsFile($extension, $this->settings['codeTemplateRootPath']);
					return array('warning' => "<span class='error'>Roundtrip is enabled but no configuration file was found.</span><br />This might happen if you use the extension builder the first time for this extension. <br />A settings file was generated in <br /><b>typo3conf/ext/" . $extension->getExtensionKey() . "/Configuration/ExtensionBuilder/settings.yaml.</b><br />Configure the overwrite settings, then save again.");
				}
				try {
					Tx_ExtensionBuilder_Service_RoundTrip::prepareExtensionForRoundtrip($extension);
				} catch (Exception $e) {
					throw $e;
				}
			}
		}
		try {
			$this->codeGenerator->build($extension);
			$this->extensionInstallationStatus->setExtension($extension);
			$message = '<p>The Extension was saved</p>' . $this->extensionInstallationStatus->getStatusMessage();
			if ($extension->getNeedsUploadFolder()) {
				$message .= '<br />Notice: File upload is not yet implemented.';
			}
			$result = array('success' => $message);
		} catch (Exception $e) {
			throw $e;
		}

		$this->generateCodeAction_saveWiring_writeExtensionBuilderConfig($extension);

		return $result;
	}

	/**
	 * Save the ExtensionBuilder config file
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 * @return void
	 */
	protected function generateCodeAction_saveWiring_writeExtensionBuilderConfig($extension) {
		$extensionBuildConfiguration = $this->configurationManager->getConfigurationFromModeler();
		$extensionBuildConfiguration['log'] = array(
			'last_modified' => date('Y-m-d h:i'),
			'extension_builder_version' => t3lib_extMgm::getExtensionVersion('extension_builder'),
			'be_user' => $GLOBALS['BE_USER']->user['realName'] . ' (' . $GLOBALS['BE_USER']->user['uid'] . ')'
		);
		t3lib_div::writeFile($extension->getExtensionDir() . Tx_ExtensionBuilder_Configuration_ConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE, json_encode($extensionBuildConfiguration));
	}

	/**
	 * Shows a list with available extensions (if they have an ExtensionBuilde.json file)
	 * @return array
	 */
	protected function generateCodeAction_listWirings() {
		$result = array();
		$extensionDirectoryHandle = opendir(PATH_typo3conf . 'ext/');
		while (false !== ($singleExtensionDirectory = readdir($extensionDirectoryHandle))) {
			if ($singleExtensionDirectory[0] == '.') {
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

		return array('result' => $result, 'error' => NULL);
	}

	/**
	 * This is a hack to handle confirm requests in the GUI
	 * @param $warnings
	 * @return array confirm (Question to confirm), confirmFieldName (is set to true if confirmed)
	 */
	protected function handleValidationWarnings($warnings) {
		$sqlReservedPropertyNames = array();
		foreach($warnings as $exception){
			if ($exception->getCode() == Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator::EXTENSION_DIR_EXISTS) {
				if (!$this->configurationManager->isConfirmed('allowExistingExtensionKey')) {
					return array(
						'confirm' => 'There is already an extension with this extension key.<br />Are you shure, you want to write into that extension directory?',
						'confirmFieldName' => 'allowExistingExtensionKey');
				}
			}
			if ($exception->getCode() == Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator::ERROR_PROPERTY_RESERVED_SQL_WORD) {
				$sqlReservedPropertyNames[] = $exception->getMessage();
			}
		}
		if(!empty($sqlReservedPropertyNames)){
			if (!$this->configurationManager->isConfirmed('allowReservedSQLWords')) {
				$confirmMessage =  "SQL reserved names were found for these properties:<br />" .
					implode("<br />",$sqlReservedPropertyNames) .
						 "<br />This will result in a different column name in the database.<br />" .
						"Are you shure, you want to use them?";
				return array(
					'confirm' => $confirmMessage,
					'confirmFieldName' => 'allowReservedSQLWords');
			}
		}
		return array();
	}

}

?>
