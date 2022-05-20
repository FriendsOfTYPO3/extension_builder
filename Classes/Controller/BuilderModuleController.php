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
use Psr\Http\Message\ResponseInterface;
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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class BuilderModuleController extends ActionController
{
    private FileGenerator $fileGenerator;

    private ExtensionBuilderConfigurationManager $extensionBuilderConfigurationManager;

    private ExtensionInstallationStatus $extensionInstallationStatus;

    private ExtensionSchemaBuilder $extensionSchemaBuilder;

    private ExtensionService $extensionService;

    private ExtensionValidator $extensionValidator;

    private ExtensionRepository $extensionRepository;

    private ModuleTemplateFactory $moduleTemplateFactory;

    private ModuleTemplate $moduleTemplate;

    private PageRenderer $pageRenderer;

    private IconFactory $iconFactory;

    /**
     * Settings from various sources:
     *
     * - Settings configured in module.extension_builder typoscript
     * - Module settings configured in the extension manager
     */
    protected array $extensionBuilderSettings = [];

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

    public function injectModuleTemplateFactory(ModuleTemplateFactory $moduleTemplateFactory): void
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function injectPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    public function injectIconFactory(IconFactory $iconFactory): void
    {
        $this->iconFactory = $iconFactory;
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
        $this->moduleTemplate->setBodyTag('<body class="yui-skin-sam">');
        $this->moduleTemplate->setTitle('Extension Builder');

        $this->addMainMenu('domainmodelling');
        //$this->addStoragePathMenu();

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
            'initialWarnings' => $initialWarnings
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
                ->setTitle('Introduction')
                ->setHref($this->uriBuilder->uriFor('index'))
                ->setActive($currentAction === 'index')
        );
        $menu->addMenuItem(
            $menu->makeMenuItem()
                ->setTitle('Domain Modelling')
                ->setHref($this->uriBuilder->uriFor('domainmodelling'))
                ->setActive($currentAction === 'domainmodelling')
        );
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /*
     * This does not work as intended as the dropdown menu will only show a value if there are at least 2 entries
     * and additionally submit the value when changed which we don't want.
     *
    //protected function addStoragePathMenu(): void
    {
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('storagePath');
        $menu->setLabel('Storage Path:');

        $storagePaths = $this->extensionService->resolveStoragePaths();
        foreach ($storagePaths as $storagePath) {
            $menu->addMenuItem(
                $menu->makeMenuItem()
                    ->setTitle($storagePath)
                    ->setHref($storagePath)
            );
        }
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }*/

    protected function addLeftButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $loadButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('actions-system-list-open', Icon::SIZE_SMALL))
            ->setTitle('Open extension')
            ->setId('WiringEditor-loadButton-button')
            ->setHref('#');
        $buttonBar->addButton($loadButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

        $loadButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL))
            ->setTitle('New extension')
            ->setId('WiringEditor-newButton-button')
            ->setHref('#');
        $buttonBar->addButton($loadButton, ButtonBar::BUTTON_POSITION_LEFT, 2);

        $loadButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('actions-document-save', Icon::SIZE_SMALL))
            ->setTitle('Save extension')
            ->setId('WiringEditor-saveButton-button')
            ->setHref('#');
        $buttonBar->addButton($loadButton, ButtonBar::BUTTON_POSITION_LEFT, 3);
    }

    protected function addRightButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $this->registerAdvancedOptionsButtonToButtonBar($buttonBar, ButtonBar::BUTTON_POSITION_RIGHT, 2);
        $this->registerOpenInNewWindowButtonToButtonBar($buttonBar, ButtonBar::BUTTON_POSITION_RIGHT, 3);
    }

    protected function registerOpenInNewWindowButtonToButtonBar(ButtonBar $buttonBar, string $position, int $group): void
    {
        $requestUri = $this->uriBuilder->uriFor('domainmodelling');

        $openInNewWindowButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setHref('#')
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.openInNewWindow'))
            ->setIcon($this->iconFactory->getIcon('actions-window-open', Icon::SIZE_SMALL))
            ->setDataAttributes([
                'dispatch-action' => 'TYPO3.WindowManager.localOpen',
                'dispatch-args' => GeneralUtility::jsonEncodeForHtmlAttribute([
                    $requestUri,
                    true, // switchFocus
                    'extension_builder', // windowName,
                    'width=1920,height=1080,status=0,menubar=0,scrollbars=1,resizable=1', // windowFeatures
                ])
            ])
            ->setId('opennewwindow');

        $buttonBar->addButton($openInNewWindowButton, $position, $group);
    }

    protected function registerAdvancedOptionsButtonToButtonBar(ButtonBar $buttonBar, string $position, int $group): void
    {
        $advancedOptionsButton = GeneralUtility::makeInstance(LinkButtonWithId::class)
            ->setIcon($this->iconFactory->getIcon('content-menu-pages', Icon::SIZE_SMALL))
            ->setTitle('<span class="simpleMode">Show</span><span class="advancedMode">Hide</span> advanced options.')
            ->setId('toggleAdvancedOptions')
            ->setHref('#')
            ->setShowLabelText(true);
        $buttonBar->addButton($advancedOptionsButton, $position, $group);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function addAssets(): void
    {
        // SECTION: JAVASCRIPT FILES
        // YUI Basis Files
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/utilities/utilities.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/resize/resize-min.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/layout/layout-min.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/container/container-min.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/json/json-min.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/button/button-min.js');

        // YUI-RPC
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui-rpc.js');

        // InputEx with wirable options
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/inputex.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Field.js');

        // extended fields for enabling unique ids
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/extended/ListField.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/extended/Group.js');

        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/util/inputex/WirableField-beta.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/Visus.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/StringField.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/Textarea.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/SelectField.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/EmailField.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/UrlField.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/CheckBox.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/InPlaceEdit.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/MenuField.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/js/fields/TypeField.js');

        // WireIt
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/WireIt.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/CanvasElement.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/Wire.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/Terminal.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/util/DD.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/util/DDResize.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/Container.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/ImageContainer.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/Layer.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/util/inputex/FormContainer-beta.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/LayerMap.js');

        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/js/WiringEditor.js');

        // Extbase Modelling definition
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/extbaseModeling.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/layout.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/extensionProperties.js');
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/modules/modelObject.js');

        // collapsible forms in relations
        $this->pageRenderer->addJsFile('EXT:extension_builder/Resources/Public/jsDomainModeling/modules/extendedModelObject.js');

        // SECTION: CSS Files
        // YUI CSS
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/reset-fonts-grids/reset-fonts-grids.css');
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/yui/assets/skins/sam/skin.css');

        // InputEx CSS
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/lib/inputex/css/inputEx.css');

        // WireIt CSS
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/css/WireIt.css');
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/jsDomainModeling/wireit/css/WireItEditor.css');

        // Custom CSS
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/jsDomainModeling/extbaseModeling.css');
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
     */
    public function dispatchRpcAction(): ResponseInterface
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
                        $this->extensionBuilderSettings['codeTemplateRootPaths.']
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
}
