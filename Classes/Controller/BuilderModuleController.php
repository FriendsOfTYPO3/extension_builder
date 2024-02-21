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
use EBT\ExtensionBuilder\Template\Components\Buttons\LinkButtonWithId;
use EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class BuilderModuleController extends ActionController
{
    private FileGenerator $fileGenerator;
    private IconFactory $iconFactory;
    private PageRenderer $pageRenderer;
    private ExtensionInstallationStatus $extensionInstallationStatus;
    private ExtensionSchemaBuilder $extensionSchemaBuilder;
    private ExtensionService $extensionService;
    private ModuleTemplateFactory $moduleTemplateFactory;
    private ExtensionValidator $extensionValidator;
    private ExtensionRepository $extensionRepository;

    public function __construct(
        FileGenerator $fileGenerator,
        IconFactory $iconFactory,
        PageRenderer $pageRenderer,
        ExtensionInstallationStatus $extensionInstallationStatus,
        ExtensionSchemaBuilder $extensionSchemaBuilder,
        ExtensionService $extensionService,
        ModuleTemplateFactory $moduleTemplateFactory,
        ExtensionValidator $extensionValidator,
        ExtensionRepository $extensionRepository,
    )
    {
        $this->fileGenerator = $fileGenerator;
        $this->iconFactory = $iconFactory;
        $this->pageRenderer = $pageRenderer;
        $this->extensionInstallationStatus = $extensionInstallationStatus;
        $this->extensionSchemaBuilder = $extensionSchemaBuilder;
        $this->extensionService = $extensionService;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->extensionValidator = $extensionValidator;
        $this->extensionRepository = $extensionRepository;
    }

    private ExtensionBuilderConfigurationManager $extensionBuilderConfigurationManager;
    private ModuleTemplate $moduleTemplate;


    /**
     * Settings from various sources:
     *
     * - Settings configured in module.extension_builder typoscript
     * - Module settings configured in the extension manager
     */
    protected array $extensionBuilderSettings = [];

    public function injectExtensionBuilderConfigurationManager(
        ExtensionBuilderConfigurationManager $configurationManager
    ): void {
        $this->extensionBuilderConfigurationManager = $configurationManager;
        $this->extensionBuilderSettings = $this->extensionBuilderConfigurationManager->getSettings();
    }

    /**
     * @return void
     */
    public function initializeAction(): void
    {
        $this->fileGenerator->setSettings($this->extensionBuilderSettings);
    }

    /**
     * Index action for this controller.
     *
     * This is the default action, showing some introduction but after the first
     * loading the user should immediately be redirected to the domainmodellingAction.
     */
    public function indexAction(): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Extension Builder');

        $this->addMainMenu('index');

        $this->view->assign('currentAction', $this->request->getControllerActionName());

        if (!$this->request->hasArgument('action')) {
            $userSettings = $this->getBackendUserAuthentication()->getModuleData('extensionbuilder');
            if (($userSettings['firstTime'] ?? 1) === 0) {
                return new ForwardResponse('domainmodelling');
            }
        }

        $this->moduleTemplate->setContent($this->view->render());

        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    public function domainmodellingAction(): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Extension Builder');
        $this->moduleTemplate->getDocHeaderComponent()->disable();
        $storagePaths = $this->extensionService->resolveStoragePaths();
        $storagePath = reset($storagePaths);

        $this->addAssets();

        $this->pageRenderer->addInlineSettingArray(
            'extensionBuilder',
            ['publicResourcesUrl' => PathUtility::getPublicResourceWebPath('EXT:extension_builder/Resources/Public')]
        );

        $this->setLocallangSettings();

        $initialWarnings = [];
        if (!$this->extensionService->isStoragePathConfigured()) {
            $initialWarnings[] = ExtensionService::COMPOSER_PATH_WARNING;
        }
        $this->view->assignMultiple([
            'initialWarnings' => $initialWarnings,
            'currentVersion' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getExtensionVersion($this->request->getControllerExtensionKey()),
            'backupDir' => $this->extensionBuilderSettings['extConf']['backupDir'],
            'outputDir' => $storagePath,
        ]);
        $this->pageRenderer->addInlineSetting(
            'extensionBuilder.publicResourceWebPath',
            'core',
            PathUtility::getPublicResourceWebPath('EXT:core/Resources/Public/')
        );
        $this->getBackendUserAuthentication()->pushModuleData('extensionbuilder', ['firstTime' => 0]);

        $this->moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Core\Package\Exception
     * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidFileException
     */
    public function helpAction() {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Extension Builder');

        $this->addAssets();

        $this->pageRenderer->addInlineSettingArray(
            'extensionBuilder',
            ['publicResourcesUrl' => PathUtility::getPublicResourceWebPath('EXT:extension_builder/Resources/Public')]
        );

        $this->setLocallangSettings();

        $initialWarnings = [];
        if (!$this->extensionService->isStoragePathConfigured()) {
            $initialWarnings[] = ExtensionService::COMPOSER_PATH_WARNING;
        }
        $this->view->assignMultiple([
            'initialWarnings' => $initialWarnings,
            'currentVersion' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getExtensionVersion($this->request->getControllerExtensionKey())
        ]);
        $this->pageRenderer->addInlineSetting(
            'extensionBuilder.publicResourceWebPath',
            'core',
            PathUtility::getPublicResourceWebPath('EXT:core/Resources/Public/')
        );
        $this->getBackendUserAuthentication()->pushModuleData('extensionbuilder', ['firstTime' => 0]);

        $this->moduleTemplate->setContent($this->view->render());

        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }


    protected function addMainMenu(string $currentAction): void
    {
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('ExtensionBuilderMainModuleMenu');

        $menu->addMenuItem(
            $menu->makeMenuItem()
                ->setTitle('Domain Modelling')
                ->setHref($this->uriBuilder->uriFor('domainmodelling'))
                ->setActive($currentAction === 'domainmodelling')
        );
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    protected function addLeftButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        // Add buttons for default domainmodelling page
        $slackButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('actions-brand-slack', Icon::SIZE_SMALL))
            ->setTitle('Get help on Slack')
            ->setShowLabelText(true)
            ->setId('slack-button')
            ->setHref('#');
        $buttonBar->addButton($slackButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

        $bugButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('actions-debug', Icon::SIZE_SMALL))
            ->setTitle('Report a bug')
            ->setShowLabelText(true)
            ->setId('bug-button')
            ->setHref('#');
        $buttonBar->addButton($bugButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

        $documentationButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('apps-toolbar-menu-opendocs', Icon::SIZE_SMALL))
            ->setTitle('Show documentation')
            ->setShowLabelText(true)
            ->setId('documentation-button')
            ->setHref('#');
        $buttonBar->addButton($documentationButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

        $sponsorButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('actions-link', Icon::SIZE_SMALL))
            ->setTitle('Sponsor this project')
            ->setShowLabelText(true)
            ->setId('sponsor-button')
            ->setHref('#');
        $buttonBar->addButton($sponsorButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
    }

    protected function addRightButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $advancedOptionsButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('actions-options', Icon::SIZE_SMALL))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:extension_builder/Resources/Private/Language/locallang.xlf:advancedOptions'))
            ->setId('toggleAdvancedOptions')
            ->setHref('#')
            ->setShowLabelText(true);
        $buttonBar->addButton($advancedOptionsButton, ButtonBar::BUTTON_POSITION_RIGHT, 1);

        // $helpButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
        //     ->setIcon($this->iconFactory->getIcon('module-help', Icon::SIZE_SMALL))
        //     ->setTitle($this->getLanguageService()->sL('LLL:EXT:extension_builder/Resources/Private/Language/locallang.xlf:showHelp'))
        //     ->setId('showHelp')
        //     // ->setHref($this->uriBuilder->uriFor('help'))
        //     ->setHref('#')
        //     ->setShowLabelText(true);

        // $buttonBar->addButton($helpButton, ButtonBar::BUTTON_POSITION_RIGHT, 2);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function addAssets(): void
    {
        // Load sources for react js app
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/Css/main.css');
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/Css/styles.css');

        // Load custom js
        $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/main.js');
        $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/85.js');
        $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/extensionbuilder.js');
    }

    /**
     * This method loads the locallang.xml file (default language), and
     * adds all keys found in it to the TYPO3.settings.extension_builder._LOCAL_LANG object
     * translated into the current language
     *
     * Dots in a key are replaced by a _
     *
     * Example:
     *        error.name becomes TYPO3.settings.extension_builder._LOCAL_LANG.error_name
     */
    protected function setLocallangSettings(): void
    {
        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
        $localizationArray = $languageFactory->getParsedData(
            'EXT:extension_builder/Resources/Private/Language/locallang.xlf',
            'default'
        );
        if (!empty($localizationArray['default']) && is_array($localizationArray['default'])) {
            foreach ($localizationArray['default'] as $key => $value) {
                $this->pageRenderer->addInlineSetting(
                    'extensionBuilder._LOCAL_LANG',
                    str_replace('.', '_', $key),
                    LocalizationUtility::translate($key, 'ExtensionBuilder')
                );
            }
        }
    }

    /**
     * Main entry point for the buttons in the Javascript frontend.
     *
     * @return ResponseInterface json encoded array
     * @throws JsonException
     */
    public function dispatchRpcAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->fileGenerator->setSettings($this->extensionBuilderSettings);

        $data = $request->getQueryParams()['input'] ?? null;
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode(['result' => json_encode($data)], JSON_THROW_ON_ERROR));
        // add status code to response
        // return $response;

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
            // $response = $response->withStatus(404);
        }

        // $response = $response->withStatus(200);
        return $this->jsonResponse(json_encode($response));
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

            $storagePaths = $this->extensionService->resolveStoragePaths();
            $storagePath = reset($storagePaths);
            if ($storagePath === false) {
                throw new \Exception('The storage path could not be detected.');
            }
            $extensionBuildConfiguration['storagePath'] = $storagePath;

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
        // TODO: check, if this is still needed
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
        $isComposerInstallerV4 = $this->extensionService->isComposerInstallerV4();

        if (!$extensionExistedBefore) {
            GeneralUtility::mkdir($extensionDirectory);
        }
        if ($usesComposerPath && !$isComposerInstallerV4 && !is_link($publicExtensionDirectory)) {
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
                    // this would result in a total overwrite so we create one and give a warning
                    $this->extensionBuilderConfigurationManager->createInitialSettingsFile(
                        $extension,
                        $this->extensionBuilderSettings['codeTemplateRootPaths']
                    );
                    $extensionPath = Environment::isComposerMode() ? 'packages/' : 'typo3conf/ext/';
                    return [
                        'warning' => sprintf(
                            '<span class="error">Roundtrip is enabled but no configuration file was found.</span><br />'
                            . 'This might happen if you use the extension builder the first time for this extension.<br />'
                            . 'A settings file was generated in<br />'
                            . '<b>%s/Configuration/ExtensionBuilder/settings.yaml.</b><br />'
                            . 'Configure the overwrite settings, then save again.',
                            $extensionPath . $extension->getExtensionKey()
                        )
                    ];
                }
                try {
                    RoundTrip::prepareExtensionForRoundtrip($extension);
                } catch (\Exception $e) {
                    throw $e;
                }
            } else {
                if (!isset($extensionSettings['ignoreWarnings'])
                    || !is_array($extensionSettings['ignoreWarnings'])
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
            $message = sprintf(
                '<p>The Extension was successfully saved in the directory:<br> <b>"%s"</b></p>%s',
                $extensionDirectory,
                $this->extensionInstallationStatus->getStatusMessage()
            );
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
            'success' => true,
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
            if (!isset($messagesPerErrorCode[$errorCode])) {
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
