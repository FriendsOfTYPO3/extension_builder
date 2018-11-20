<?php
namespace EBT\ExtensionBuilder\Controller;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository;
use EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Service\FileGenerator;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller of the Extension Builder extension
 *
 * @category    Controller
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class BuilderModuleController extends ActionController
{
    /**
     * @var \EBT\ExtensionBuilder\Service\FileGenerator
     */
    protected $fileGenerator = null;

    /**
     * @var \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager
     */
    protected $extensionBuilderConfigurationManager = null;

    /**
     * @var \EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus
     */
    protected $extensionInstallationStatus = null;

    /**
     * @var \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder
     */
    protected $extensionSchemaBuilder = null;

    /**
     * @var \EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator
     */
    protected $extensionValidator = null;

    /**
     * @var \EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository
     */
    protected $extensionRepository = null;

    /**
     * Settings from various sources:
     *
     * - Settings configured in module.extension_builder typoscript
     * - Module settings configured in the extension manager
     *
     * @var array
     */
    protected $extensionBuilderSettings = [];

    /**
     * @param \EBT\ExtensionBuilder\Service\FileGenerator $fileGenerator
     * @return void
     */
    public function injectFileGenerator(FileGenerator $fileGenerator)
    {
        $this->fileGenerator = $fileGenerator;
    }

    /**
     * @param \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager $configurationManager
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function injectExtensionBuilderConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager)
    {
        $this->extensionBuilderConfigurationManager = $configurationManager;
        $this->extensionBuilderSettings = $this->extensionBuilderConfigurationManager->getSettings();
    }

    /**
     * @param \EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus $extensionInstallationStatus
     * @return void
     */
    public function injectExtensionInstallationStatus(ExtensionInstallationStatus $extensionInstallationStatus)
    {
        $this->extensionInstallationStatus = $extensionInstallationStatus;
    }

    /**
     * @param \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder $extensionSchemaBuilder
     * @return void
     */
    public function injectExtensionSchemaBuilder(ExtensionSchemaBuilder $extensionSchemaBuilder)
    {
        $this->extensionSchemaBuilder = $extensionSchemaBuilder;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator $extensionValidator
     * @return void
     */
    public function injectExtensionValidator(ExtensionValidator $extensionValidator)
    {
        $this->extensionValidator = $extensionValidator;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository $extensionRepository
     * @return void
     */
    public function injectExtensionRepository(ExtensionRepository $extensionRepository)
    {
        $this->extensionRepository = $extensionRepository;
    }

    /**
     * @return void
     */
    public function initializeAction()
    {
        $this->fileGenerator->setSettings($this->extensionBuilderSettings);
    }

    /**
     * Index action for this controller.
     *
     * This is the default action, showing some introduction but after the first
     * loading the user should immediately be redirected to the domainmodellingAction.
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function indexAction()
    {
        $this->view->assign('currentAction', $this->request->getControllerActionName());
        $this->view->assign('settings', $this->extensionBuilderConfigurationManager->getSettings());
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
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function domainmodellingAction()
    {
        $this->view->assign('extPath', PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('extension_builder')));
        $this->view->assign('settings', $this->extensionBuilderConfigurationManager->getSettings());
        $this->view->assign('currentAction', $this->request->getControllerActionName());
        $this->getBackendUserAuthentication()->pushModuleData('extensionbuilder', ['firstTime' => 0]);
    }

    /**
     * Main entry point for the buttons in the Javascript frontend.
     *
     * @return string json encoded array
     */
    public function dispatchRpcAction()
    {
        try {
            $this->extensionBuilderConfigurationManager->parseRequest();
            $subAction = $this->extensionBuilderConfigurationManager->getSubActionFromRequest();
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
                    $response = ['error' => 'Sub Action not found.'];
            }
        } catch (\Exception $e) {
            $response = ['error' => $e->getMessage()];
        }
        return json_encode($response);
    }

    /**
     * Generate the code files according to the transferred JSON configuration.
     *
     * @throws \Exception
     * @return array (status => message)
     */
    protected function rpcActionSave()
    {
        try {
            $extensionBuildConfiguration = $this->extensionBuilderConfigurationManager->getConfigurationFromModeler();
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
            if ($this->extensionBuilderSettings['extConf']['backupExtension'] === '1') {
                try {
                    RoundTrip::backupExtension($extension, $this->extensionBuilderSettings['extConf']['backupDir']);
                } catch (\Exception $e) {
                    throw $e;
                }
            }
            $extensionSettings = $this->extensionBuilderConfigurationManager->getExtensionSettings($extension->getExtensionKey());
            if ($this->extensionBuilderSettings['extConf']['enableRoundtrip'] === '1') {
                if (empty($extensionSettings)) {
                    // no config file in an existing extension!
                    // this would result in a	 total overwrite so we create one and give a warning
                    $this->extensionBuilderConfigurationManager->createInitialSettingsFile($extension, $this->extensionBuilderSettings['codeTemplateRootPaths']);
                    return ['warning' => "<span class='error'>Roundtrip is enabled but no configuration file was found.</span><br />This might happen if you use the extension builder the first time for this extension. <br />A settings file was generated in <br /><b>typo3conf/ext/" . $extension->getExtensionKey() . '/Configuration/ExtensionBuilder/settings.yaml.</b><br />Configure the overwrite settings, then save again.'];
                }
                try {
                    RoundTrip::prepareExtensionForRoundtrip($extension);
                } catch (\Exception $e) {
                    throw $e;
                }
            } else {
                if (!is_array($extensionSettings['ignoreWarnings']) || !in_array(ExtensionValidator::EXTENSION_DIR_EXISTS, $extensionSettings['ignoreWarnings'])) {
                    $confirmationRequired = $this->handleValidationWarnings([
                        new ExtensionException("This action will overwrite previously saved content!\n(Enable the roundtrip feature to avoid this warning).", ExtensionValidator::EXTENSION_DIR_EXISTS)
                    ]);
                    if (!empty($confirmationRequired)) {
                        return $confirmationRequired;
                    }
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
            $result = ['success' => $message];
            if ($this->extensionInstallationStatus->isDbUpdateNeeded()) {
                $result['confirmUpdate'] = true;
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
    protected function rpcActionList()
    {
        $extensions = $this->extensionRepository->findAll();
        return ['result' => $extensions, 'error' => null];
    }

    /**
     * This is a hack to handle confirm requests in the GUI.
     *
     * @param \Exception[] $warnings
     * @return array confirm (Question to confirm), confirmFieldName (is set to true if confirmed)
     */
    protected function handleValidationWarnings(array $warnings)
    {
        $messagesPerErrorcode = [];
        foreach ($warnings as $exception) {
            if (!is_array($messagesPerErrorcode[$exception->getCode()])) {
                $messagesPerErrorcode[$exception->getCode()] = [];
            }
            $messagesPerErrorcode[$exception->getCode()][] = nl2br(htmlspecialchars($exception->getMessage())) . ' (Error ' . $exception->getCode() . ')<br /><br />';
        }
        foreach ($messagesPerErrorcode as $errorCode => $messages) {
            if (!$this->extensionBuilderConfigurationManager->isConfirmed('allow' . $errorCode)) {
                if ($errorCode == ExtensionValidator::ERROR_PROPERTY_RESERVED_SQL_WORD) {
                    $confirmMessage = 'SQL reserved names were found for these properties:<br />' .
                        '<ol class="warnings"><li>' . implode('</li><li>', $messages) . '</li></ol>' .
                        'This will result in a different column name in the database.<br />' .
                        '<strong>Are you sure, you want to use them?</strong>';
                } else {
                    $confirmMessage = '<ol class="warnings"><li>' . implode('</li><li>', $messages) . '</li></ol>' .
                        '<strong>Do you want to save anyway?</strong><br /><br />';
                }
                return [
                    'confirm' => '<span style="color:red">Warning!</span></br>' . $confirmMessage,
                    'confirmFieldName' => 'allow' . $errorCode
                ];
            }
        }
        return [];
    }

    /**
     * @return array
     */
    protected function rpcActionPerformDbUpdate()
    {
        $params = $this->extensionBuilderConfigurationManager->getParamsFromRequest();
        return $this->extensionInstallationStatus->performDbUpdates($params);
    }

    /**
     * Returns the global BackendUserAuthentication object.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
