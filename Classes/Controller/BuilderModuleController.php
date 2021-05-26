<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Controller;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository;
use EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Service\ExtensionService;
use EBT\ExtensionBuilder\Service\FileGenerator;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;

/**
 * Controller of the Extension Builder extension
 *
 * @category    Controller
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class BuilderModuleController extends ActionController
{
    /**
     * @var FileGenerator
     */
    protected $fileGenerator;

    /**
     * @var ExtensionBuilderConfigurationManager
     */
    protected $extensionBuilderConfigurationManager;

    /**
     * @var ExtensionInstallationStatus
     */
    protected $extensionInstallationStatus;

    /**
     * @var ExtensionSchemaBuilder
     */
    protected $extensionSchemaBuilder;

    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * @var ExtensionValidator
     */
    protected $extensionValidator;

    /**
     * @var ExtensionRepository
     */
    protected $extensionRepository;

    /**
     * Settings from various sources:
     *
     * - Settings configured in module.extension_builder typoscript
     * - Module settings configured in the extension manager
     *
     * @var array
     */
    protected $extensionBuilderSettings = [];

    public function injectFileGenerator(FileGenerator $fileGenerator): void
    {
        $this->fileGenerator = $fileGenerator;
    }

    public function injectExtensionBuilderConfigurationManager(
        ExtensionBuilderConfigurationManager $configurationManager
    ): void {
        $this->extensionBuilderConfigurationManager = $configurationManager;
        $this->extensionBuilderSettings = $this->extensionBuilderConfigurationManager->getSettings();
    }

    public function injectExtensionInstallationStatus(ExtensionInstallationStatus $extensionInstallationStatus): void
    {
        $this->extensionInstallationStatus = $extensionInstallationStatus;
    }

    public function injectExtensionSchemaBuilder(ExtensionSchemaBuilder $extensionSchemaBuilder): void
    {
        $this->extensionSchemaBuilder = $extensionSchemaBuilder;
    }

    public function injectExtensionService(ExtensionService $extensionService): void
    {
        $this->extensionService = $extensionService;
    }

    public function injectExtensionValidator(ExtensionValidator $extensionValidator): void
    {
        $this->extensionValidator = $extensionValidator;
    }

    public function injectExtensionRepository(ExtensionRepository $extensionRepository): void
    {
        $this->extensionRepository = $extensionRepository;
    }

    public function initializeAction(): void
    {
        $this->fileGenerator->setSettings($this->extensionBuilderSettings);
    }

    /**
     * Index action for this controller.
     *
     * This is the default action, showing some introduction but after the first
     * loading the user should immediately be redirected to the domainmodellingAction.
     *
     * @throws InvalidConfigurationTypeException
     * @throws StopActionException
     */
    public function indexAction(): void
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
     * @throws InvalidConfigurationTypeException
     */
    public function domainmodellingAction(): void
    {
        $initialWarnings = [];
        if (!$this->extensionService->isStoragePathConfigured()) {
            $initialWarnings[] = ExtensionService::COMPOSER_PATH_WARNING;
        }
        $this->view->assignMultiple([
            'extPath' => PathUtility::stripPathSitePrefix(ExtensionManagementUtility::extPath('extension_builder')),
            'settings' => $this->extensionBuilderConfigurationManager->getSettings(),
            'currentAction' => $this->request->getControllerActionName(),
            'storagePaths' => $this->extensionService->resolveStoragePaths(),
            'initialWarnings' => $initialWarnings
        ]);
        $this->getBackendUserAuthentication()->pushModuleData('extensionbuilder', ['firstTime' => 0]);
    }

    /**
     * Main entry point for the buttons in the Javascript frontend.
     *
     * @return string json encoded array
     */
    public function dispatchRpcAction(): string
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
     * @return array (status => message)
     * @throws \Exception
     */
    protected function rpcActionSave(): array
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
                foreach ($validationConfigurationResult['errors'] as $exception) {
                    /** @var \Exception $exception */
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
            /** @var \Exception $exception */
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
        $publicExtensionDirectory = Environment::getExtensionsPath() . '/' . $extension->getExtensionKey();
        $usesComposerPath = $this->extensionService->isComposerStoragePath($extensionDirectory);
        $extensionExistedBefore = is_dir($extensionDirectory);

        if (!$extensionExistedBefore) {
            GeneralUtility::mkdir($extensionDirectory);
        }
        if ($usesComposerPath && !is_link($publicExtensionDirectory)) {
            symlink(
                PathUtility::getRelativePath(dirname($publicExtensionDirectory), $extensionDirectory),
                $publicExtensionDirectory
            );
        }

        if ($extensionExistedBefore) {
            if ($this->extensionBuilderSettings['extConf']['backupExtension'] === '1') {
                try {
                    RoundTrip::backupExtension($extension, $this->extensionBuilderSettings['extConf']['backupDir']);
                } catch (\Exception $e) {
                    throw $e;
                }
            }
            $extensionSettings = $this->extensionBuilderConfigurationManager->getExtensionSettings($extension->getExtensionKey(), $extension->getStoragePath());
            if ($this->extensionBuilderSettings['extConf']['enableRoundtrip'] === '1') {
                if (empty($extensionSettings)) {
                    // no config file in an existing extension!
                    // this would result in a	 total overwrite so we create one and give a warning
                    $this->extensionBuilderConfigurationManager->createInitialSettingsFile(
                        $extension,
                        $this->extensionBuilderSettings['codeTemplateRootPaths.']
                    );
                    $extensionPath = Environment::isComposerMode() ? 'packages/' : 'typo3conf/ext/';
                    return ['warning' => "<span class='error'>Roundtrip is enabled but no configuration file was found.</span><br />This might happen if you use the extension builder the first time for this extension. <br />A settings file was generated in <br /><b>" . $extensionPath . $extension->getExtensionKey() . '/Configuration/ExtensionBuilder/settings.yaml.</b><br />Configure the overwrite settings, then save again.'];
                }
                try {
                    RoundTrip::prepareExtensionForRoundtrip($extension);
                } catch (\Exception $e) {
                    throw $e;
                }
            } else {
                if (!is_array($extensionSettings['ignoreWarnings'])
                    || !in_array(ExtensionValidator::EXTENSION_DIR_EXISTS, $extensionSettings['ignoreWarnings'])
                ) {
                    $confirmationRequired = $this->handleValidationWarnings([
                        new ExtensionException(
                            "This action will overwrite previously saved content!\n(Enable the roundtrip feature to avoid this warning).",
                            ExtensionValidator::EXTENSION_DIR_EXISTS
                        )
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
            $this->extensionInstallationStatus->setUsesComposerPath($usesComposerPath);
            $message = '<p>The Extension was saved</p>' . $this->extensionInstallationStatus->getStatusMessage();
            $result = ['success' => $message, 'usesComposerPath' => $usesComposerPath];
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
    protected function rpcActionList(): array
    {
        $extensions = $this->extensionRepository->findAll();
        return [
            'result' => $extensions,
            'error' => null
        ];
    }

    /**
     * This is a hack to handle confirm requests in the GUI.
     *
     * @param \Exception[] $warnings
     * @return array confirm (Question to confirm), confirmFieldName (is set to true if confirmed)
     */
    protected function handleValidationWarnings(array $warnings): array
    {
        $messagesPerErrorCode = [];
        foreach ($warnings as $exception) {
            $errorCode = $exception->getCode();
            if (!is_array($messagesPerErrorCode[$errorCode])) {
                $messagesPerErrorCode[$errorCode] = [];
            }
            $messagesPerErrorCode[$errorCode][] = nl2br(htmlspecialchars($exception->getMessage())) . ' (Error ' . $errorCode . ')<br /><br />';
        }
        foreach ($messagesPerErrorCode as $errorCode => $messages) {
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

    protected function rpcActionPerformDbUpdate(): array
    {
        $params = $this->extensionBuilderConfigurationManager->getParamsFromRequest();
        return $this->extensionInstallationStatus->performDbUpdates($params);
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
