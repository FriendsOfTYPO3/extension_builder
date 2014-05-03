<?php
namespace EBT\ExtensionBuilder\Controller;
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
use EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository;
use EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller of the Extension Builder extension
 *
 * @category    Controller
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class BuilderModuleController extends ActionController {
	/**
	 * @var \EBT\ExtensionBuilder\Service\FileGenerator
	 */
	protected $fileGenerator = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus
	 */
	protected $extensionInstallationStatus = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder
	 */
	protected $extensionSchemaBuilder = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator
	 */
	protected $extensionValidator = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository
	 */
	protected $extensionRepository = NULL;

	/**
	 * Settings from various sources:
	 *
	 * - Settings configured in module.extension_builder typoscript
	 * - Module settings configured in the extension manager
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @param \EBT\ExtensionBuilder\Service\FileGenerator $fileGenerator
	 * @return void
	 */
	public function injectFileGenerator(\EBT\ExtensionBuilder\Service\FileGenerator $fileGenerator) {
		$this->fileGenerator = $fileGenerator;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->settings = $this->configurationManager->getSettings();
	}

	/**
	 * @param \EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus $extensionInstallationStatus
	 * @return void
	 */
	public function injectExtensionInstallationStatus(ExtensionInstallationStatus $extensionInstallationStatus) {
		$this->extensionInstallationStatus = $extensionInstallationStatus;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder $extensionSchemaBuilder
	 * @return void
	 */
	public function injectExtensionSchemaBuilder(ExtensionSchemaBuilder $extensionSchemaBuilder) {
		$this->extensionSchemaBuilder = $extensionSchemaBuilder;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator $extensionValidator
	 * @return void
	 */
	public function injectExtensionValidator(ExtensionValidator $extensionValidator) {
		$this->extensionValidator = $extensionValidator;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository $extensionRepository
	 * @return void
	 */
	public function injectExtensionRepository(ExtensionRepository $extensionRepository) {
		$this->extensionRepository = $extensionRepository;
	}

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->fileGenerator->setSettings($this->settings);
	}

	/**
	 * Index action for this controller.
	 *
	 * This is the default action, showing some introduction but after the first
	 * loading the user should immediately be redirected to the domainmodellingAction.
	 *
	 * @return void
	 */
	public function indexAction() {
		if (!$this->request->hasArgument('action')) {
			$userSettings = $this->getBackendUserAuthentication()->getModuleData('extensionbuilder');
			if ($userSettings['firstTime'] === 0) {
				$this->forward('domainmodelling');
			}
		}
	}

	/**
	 * Loads the Domainmodelling template.
	 *
	 * Nothing more to do here, since the next action is invoked by the Javascript
	 * interface.
	 *
	 * @return void
	 */
	public function domainmodellingAction() {
		$this->getBackendUserAuthentication()->pushModuleData('extensionbuilder', array('firstTime' => 0));
	}

	/**
	 * Main entry point for the buttons in the Javascript frontend.
	 *
	 * @return string json encoded array
	 */
	public function dispatchRpcAction() {
		try {
			$this->configurationManager->parseRequest();
			$subAction = $this->configurationManager->getSubActionFromRequest();
			if (empty($subAction)) {
				throw new \Exception('No Sub Action!');
			}
			switch ($subAction) {
				case 'saveWiring':
					$response = $this->rpcActionSave();
					break;
				case 'listWirings':
					$response = $this->rpcActionList();
					break;
				case 'updateDb':
					$response = $this->rpcActionPerformDbUpdate();
					break;
				default:
					$response = array('error' => 'Sub Action not found.');
			}
		} catch (\Exception $e) {
			$response = array('error' => $e->getMessage());
		}
		return json_encode($response);
	}

	/**
	 * Generate the code files according to the transferred JSON configuration.
	 *
	 * @throws \Exception
	 * @return array (status => message)
	 */
	protected function rpcActionSave() {
		try {
			$extensionBuildConfiguration = $this->configurationManager->getConfigurationFromModeler();
			GeneralUtility::devlog('Modeler Configuration', 'extension_builder', 0, $extensionBuildConfiguration);
			$validationConfigurationResult = $this->extensionValidator->validateConfigurationFormat($extensionBuildConfiguration);
			if (!empty($validationConfigurationResult['warnings'])) {
				$confirmationRequired = $this->handleValidationWarnings($validationConfigurationResult['warnings']);
				if (!empty($confirmationRequired)) {
					return $confirmationRequired;
				}
			}
			if (!empty($validationConfigurationResult['errors'])) {
				$errorMessage = '';
				/** @var $exception \Exception */
				foreach ($validationConfigurationResult['errors'] as $exception) {
					$errorMessage .= '<br />' . $exception->getMessage();
				}
				throw new \Exception($errorMessage);
			}
			$extension = $this->extensionSchemaBuilder->build($extensionBuildConfiguration);
		} catch (\Exception $e) {
			throw $e;
		}

		// Validate the extension
		$validationResult = $this->extensionValidator->isValid($extension);
		if (!empty($validationResult['errors'])) {
			$errorMessage = '';
			/** @var $exception \Exception */
			foreach ($validationResult['errors'] as $exception) {
				$errorMessage .= '<br />' . $exception->getMessage();
			}
			throw new \Exception($errorMessage);
		}
		if (!empty($validationResult['warnings'])) {
			$confirmationRequired = $this->handleValidationWarnings($validationResult['warnings']);
			if (!empty($confirmationRequired)) {
				return $confirmationRequired;
			}
		}

		$extensionDirectory = $extension->getExtensionDir();

		if (!is_dir($extensionDirectory)) {
			GeneralUtility::mkdir($extensionDirectory);
		} else {
			if ($this->settings['extConf']['backupExtension'] == 1) {
				try {
					RoundTrip::backupExtension($extension, $this->settings['extConf']['backupDir']);
				} catch (\Exception $e) {
					throw $e;
				}
			}
			$extensionSettings = $this->configurationManager->getExtensionSettings($extension->getExtensionKey());
			if ($this->settings['extConf']['enableRoundtrip'] == 1) {
				if (empty($extensionSettings)) {
					// no config file in an existing extension!
					// this would result in a	 total overwrite so we create one and give a warning
					$this->configurationManager->createInitialSettingsFile($extension, $this->settings['codeTemplateRootPath']);
					return array('warning' => "<span class='error'>Roundtrip is enabled but no configuration file was found.</span><br />This might happen if you use the extension builder the first time for this extension. <br />A settings file was generated in <br /><b>typo3conf/ext/" . $extension->getExtensionKey() . "/Configuration/ExtensionBuilder/settings.yaml.</b><br />Configure the overwrite settings, then save again.");
				}
				try {
					RoundTrip::prepareExtensionForRoundtrip($extension);
				} catch (\Exception $e) {
					throw $e;
				}
			}
		}
		try {
			$this->fileGenerator->build($extension);
			$this->extensionInstallationStatus->setExtension($extension);
			$message = '<p>The Extension was saved</p>' . $this->extensionInstallationStatus->getStatusMessage();
			if ($extension->getNeedsUploadFolder()) {
				$message .= '<br />Notice: File upload is not yet implemented.';
			}
			$result = array('success' => $message);
			if ($this->extensionInstallationStatus->isDbUpdateNeeded()) {
				$result['confirmUpdate'] = TRUE;
			}
		} catch (\Exception $e) {
			throw $e;
		}

		$this->extensionRepository->saveExtensionConfiguration($extension);

		return $result;
	}

	/**
	 * Shows a list with available extensions (if they have an ExtensionBuilder.json
	 * file).
	 *
	 * @return array
	 */
	protected function rpcActionList() {
		$extensions = $this->extensionRepository->findAll();
		return array('result' => $extensions, 'error' => NULL);
	}

	/**
	 * This is a hack to handle confirm requests in the GUI.
	 *
	 * @param \Exception[] $warnings
	 * @return array confirm (Question to confirm), confirmFieldName (is set to TRUE if confirmed)
	 */
	protected function handleValidationWarnings(array $warnings) {
		$messagesPerErrorcode = array();
		foreach ($warnings as $exception) {
			if (!is_array($messagesPerErrorcode[$exception->getCode()])) {
				$messagesPerErrorcode[$exception->getCode()] = array();
			}
			$messagesPerErrorcode[$exception->getCode()][] = nl2br(htmlspecialchars($exception->getMessage())) . ' (Error ' . $exception->getCode() . ')<br /><br />';
		}
		foreach ($messagesPerErrorcode as $errorCode => $messages) {
			if (!$this->configurationManager->isConfirmed('allow' . $errorCode)) {
				if ($errorCode == ExtensionValidator::ERROR_PROPERTY_RESERVED_SQL_WORD) {
					$confirmMessage = 'SQL reserved names were found for these properties:<br />' .
							'<ol class="warnings"><li>' . implode('</li><li>', $messages) . '</li></ol>' .
							'This will result in a different column name in the database.<br />' .
							'<strong>Are you sure, you want to use them?</strong>';

				} else {
					$confirmMessage = '<ol class="warnings"><li>' . implode('</li><li>', $messages) . '</li></ol>' .
							'<strong>Do you want to save anyway?</strong><br /><br />';
				}
				return array(
					'confirm' => '<span style="color:red">Warning!</span></br>' . $confirmMessage,
					'confirmFieldName' => 'allow' . $errorCode
				);
			}
		}
		return array();
	}

	/**
	 * @return array
	 */
	protected function rpcActionPerformDbUpdate() {
		$params = $this->configurationManager->getParamsFromRequest();
		return $this->extensionInstallationStatus->performDbUpdates($params);
	}

	/**
	 * Returns the global BackendUserAuthentication object.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUserAuthentication() {
		return $GLOBALS['BE_USER'];
	}

}
