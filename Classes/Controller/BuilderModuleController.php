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

        // $this->addMainMenu('domainmodelling');
        // $this->addMainMenu('testaction');
        $this->addCurrentExtensionPath();

        $this->addLeftButtons();
        $this->addRightButtons();

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

    /**
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Core\Package\Exception
     * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidFileException
     */
    public function helpAction() {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('Extension Builder');

        // $this->addMainMenu('domainmodelling');
        // $this->addMainMenu('testaction');
        $this->addCurrentExtensionPath();

        $this->addLeftButtons('help');
        // $this->addRightButtons();

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

    /*
     *
     */
    // TODO: Show the current Extension name beside the buttons in the button bar
    protected function addCurrentExtensionPath(): void
    {
        // TODO
    }

    protected function addLeftButtons(string $action = 'domainmodelling'): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        if($action == 'domainmodelling') {
            // Add buttons for default domainmodelling page
            $loadButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
                ->setIcon($this->iconFactory->getIcon('actions-folder', Icon::SIZE_SMALL))
                ->setTitle('Open extension')
                ->setShowLabelText(true)
                ->setId('loadExtension-button')
                ->setHref('#');
            $buttonBar->addButton($loadButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

            $loadButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
                ->setIcon($this->iconFactory->getIcon('actions-template-new', Icon::SIZE_SMALL))
                ->setTitle('New extension')
                ->setShowLabelText(true)
                ->setId('newExtension-button')
                ->setHref('#');
            $buttonBar->addButton($loadButton, ButtonBar::BUTTON_POSITION_LEFT, 2);

            $loadButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
                ->setIcon($this->iconFactory->getIcon('actions-save', Icon::SIZE_SMALL))
                ->setTitle('Save extension')
                ->setShowLabelText(true)
                ->setId('saveExtension-button')
                ->setHref('#');
            $buttonBar->addButton($loadButton, ButtonBar::BUTTON_POSITION_LEFT, 3);
        } else if ($action === 'help') {
            // Add buttons for help page
            $loadButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
                ->setIcon($this->iconFactory->getIcon('actions-view-go-back', Icon::SIZE_SMALL))
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.goBack'))
                ->setShowLabelText(true)
                ->setHref($this->uriBuilder->uriFor('domainmodelling'));
            $buttonBar->addButton($loadButton, ButtonBar::BUTTON_POSITION_LEFT, 3);
        }

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

        $helpButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('module-help', Icon::SIZE_SMALL))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:extension_builder/Resources/Private/Language/locallang.xlf:showHelp'))
            ->setId('showHelp')
            // ->setHref($this->uriBuilder->uriFor('help'))
            ->setHref('#')
            ->setShowLabelText(true);

        $buttonBar->addButton($helpButton, ButtonBar::BUTTON_POSITION_RIGHT, 2);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function addAssets(): void
    {

        // Load sources for react js app
        // $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/Sources/App.js');
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/Css/main.css');
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/Css/extensionbuilder.css');

        // Load ReactJS -> not needed at the moment because it is shipped inside the bundled JS
        // $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/Contrib/react.js');
        // $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/Contrib/react-dom.js');

        // Load custom js
        $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/main.js');
        $this->pageRenderer->loadJavaScriptModule('@friendsoftypo3/extension-builder/496.chunk.js');
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


        /*

        if ($input === null) {
            throw new \InvalidArgumentException('Please provide a number', 1580585107);
        }

        $result = $input ** 2;

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(json_encode(['result' => $result], JSON_THROW_ON_ERROR));
        // add status code to response
        $response = $response->withStatus(500);
        return $response;
         */
        // return Response with success message
        // return $this->jsonResponse(json_encode(['error' => 'Action dispatched.'], 300));
// our previous computation
        // $response = $this->responseFactory->createResponse()->withHeader('Content-Type', 'application/json; charset=utf-8');
        // $response->getBody()->write(json_encode(['result' => 'test'], JSON_THROW_ON_ERROR));
        // return $response;


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
            $message = sprintf(
                '<p>The Extension was successfully saved in the directory: "%s"</p>%s',
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

    // public function ajaxTestingAction(ServerRequestInterface $request): ResponseInterface
    // {
    //     $input = $request->getQueryParams()['input'] ?? null;
    //     if ($input === null) {
    //         throw new \InvalidArgumentException('Please provide a number', 1580585107);
    //     }
//
    //     $result = $input ** 2;
//
    //     $response = $this->responseFactory->createResponse()
    //         ->withHeader('Content-Type', 'application/json; charset=utf-8');
    //     $response->getBody()->write(json_encode(['result' => $result], JSON_THROW_ON_ERROR));
    //     // add status code to response
    //     $response = $response->withStatus(500);
    //     return $response;
    // }
}
