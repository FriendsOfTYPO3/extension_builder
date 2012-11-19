<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Ingmar Schlecht <ingmar@typo3.org>
 *  (c) 2011 Nico de Haen <mail@ndh-websolutions.de>
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
 * Controller of the Extension Builder extension
 *
 * @category    Controller
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class Tx_ExtensionBuilder_Controller_BuilderModuleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

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
	 * @var Tx_ExtensionBuilder_Domain_Repository_ExtensionRepository
	 */
	protected $extensionRepository;

	/**
	 * settings from various sources:
	 * settings configured in module.extension_builder typoscript
	 * Module settings configured in the extension manager
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
	 * @param Tx_ExtensionBuilder_Domain_Repository_ExtensionRepository $extensionRepository
	 * @return void
	 */
	public function injectExtensionRepository(Tx_ExtensionBuilder_Domain_Repository_ExtensionRepository $extensionRepository) {
		$this->extensionRepository = $extensionRepository;
	}

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->codeGenerator->setSettings($this->settings);
	}

	/**
	 * Index action for this controller.
	 * This is the default action, showing some introduction
	 * but after the first loading the user should
	 * immediately be redirected to the domainmodellingAction
	 *
	 * @return void
	 */
	public function indexAction() {
		if (!$this->request->hasArgument('action')) {
			$userSettings = $GLOBALS['BE_USER']->getModuleData('extensionbuilder');
			if ($userSettings['firstTime'] === 0) {
				$this->forward('domainmodelling');
			}
		}
	}

	/**
	 *
	 * loads the Domainmodelling template
	 * Nothing more to do here, since the next action is invoked
	 * by the Javascript interface
	 *
	 * @return void
	 */
	public function domainmodellingAction() {
		$GLOBALS['BE_USER']->pushModuleData('extensionbuilder', array('firstTime' => 0));
	}

	/**
	 * Main entry point for the buttons in the Javascript frontend
	 * @return string json encoded array
	 */
	public function dispatchRpcAction() {
		try {
			$this->configurationManager->parseRequest();
			$subAction = $this->configurationManager->getSubActionFromRequest();
			if (empty($subAction)) {
				throw new Exception('No Sub Action!');
			}
			switch ($subAction) {
				case 'saveWiring':
					$response = $this->rpcAction_save();
					break;
				case 'listWirings':
					$response = $this->rpcAction_list();
					break;
				default:
					$response = array('error' => 'Sub Action not found.');
			}
		} catch (Exception $e) {
			$response = array('error' => $e->getMessage());
		}
		return json_encode($response);
	}


	/**
	 * Generate the code files according to the transferred JSON configuration
	 *
	 * @throws Exception
	 * @return array (status => message)
	 */
	protected function rpcAction_save() {
		try {
			$extensionBuildConfiguration = $this->configurationManager->getConfigurationFromModeler();
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Modeler Configuration', 'extension_builder', 0, $extensionBuildConfiguration);
			$validationConfigurationResult = $this->extensionValidator->validateConfigurationFormat($extensionBuildConfiguration);
			if (!empty($validationConfigurationResult['warnings'])) {
				$confirmationRequired = $this->handleValidationWarnings($validationConfigurationResult['warnings']);
				if (!empty($confirmationRequired)) {
					return $confirmationRequired;
				}
			}
			$extension = $this->extensionSchemaBuilder->build($extensionBuildConfiguration);
		}
		catch (Exception $e) {
			throw $e;
		}

		// Validate the extension
		$validationResult = $this->extensionValidator->isValid($extension);
		if (!empty($validationResult['errors'])) {
			$errorMessage = '';
			foreach ($validationResult['errors'] as $exception) {
				$errorMessage .= '<br />' . $exception->getMessage();
			}
			throw new Exception($errorMessage);
		}
		if (!empty($validationResult['warnings'])) {
			$confirmationRequired = $this->handleValidationWarnings($validationResult['warnings']);
			if (!empty($confirmationRequired)) {
				return $confirmationRequired;
			}
		}


		$extensionDirectory = $extension->getExtensionDir();

		if (!is_dir($extensionDirectory)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir($extensionDirectory);
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

		$this->extensionRepository->saveExtensionConfiguration($extension);

		return $result;
	}


	/**
	 * Shows a list with available extensions (if they have an ExtensionBuilder.json file)
	 * @return array
	 */
	protected function rpcAction_list() {
		$extensions = $this->extensionRepository->findAll();
		return array('result' => $extensions, 'error' => NULL);
	}

	/**
	 * This is a hack to handle confirm requests in the GUI
	 * @param $warnings
	 * @return array confirm (Question to confirm), confirmFieldName (is set to TRUE if confirmed)
	 */
	protected function handleValidationWarnings($warnings) {
		$messagesPerErrorcode = array();
		foreach ($warnings as $exception) {
			if (!is_array($messagesPerErrorcode[$exception->getCode()])) {
				$messagesPerErrorcode[$exception->getCode()] = array();
			}
			$messagesPerErrorcode[$exception->getCode()][] = $exception->getMessage();
		}
		foreach ($messagesPerErrorcode as $errorCode => $messages) {
			if (!$this->configurationManager->isConfirmed('allow' . $errorCode)) {
				if ($errorCode == Tx_ExtensionBuilder_Domain_Validator_ExtensionValidator::ERROR_PROPERTY_RESERVED_SQL_WORD) {
					$confirmMessage = 'SQL reserved names were found for these properties:<br />' .
							'<ol class="warnings"><li>' . implode('</li><li>', $messages) . '</li></ol>' .
							'This will result in a different column name in the database.<br />' .
							'<strong>Are you sure, you want to use them?</strong>';

				} else {
					$confirmMessage = '<ol class="warnings"><li>' . implode('</li><li>', $messages) . '</li></ol>' .
							'<strong>Do you want to save anyway?</strong>';
				}
				return array(
					'confirm' => '<span style="color:red">Warning!</span></br>' . $confirmMessage,
					'confirmFieldName' => 'allow' . $errorCode);
			}
		}
		return array();
	}

}

?>
