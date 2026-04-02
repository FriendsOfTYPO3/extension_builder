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
use Exception;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class BuilderModuleController extends ActionController
{
    private ModuleTemplate $moduleTemplate;

    /**
     * Settings from various sources:
     *
     * - Settings configured in module.extension_builder typoscript
     * - Module settings configured in the extension manager
     */
    protected array $extensionBuilderSettings = [];

    public function __construct(
        private readonly FileGenerator $fileGenerator,
        private readonly ExtensionBuilderConfigurationManager $extensionBuilderConfigurationManager,
        private readonly ExtensionInstallationStatus $extensionInstallationStatus,
        private readonly ExtensionSchemaBuilder $extensionSchemaBuilder,
        private readonly ExtensionService $extensionService,
        private readonly ExtensionValidator $extensionValidator,
        private readonly ExtensionRepository $extensionRepository,
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly PageRenderer $pageRenderer,
        private readonly IconFactory $iconFactory,
        private readonly LanguageServiceFactory $languageServiceFactory,
        private readonly BackendUriBuilder $backendUriBuilder,
        private readonly LocalizationFactory $localizationFactory,
    ) {
        $this->extensionBuilderSettings = $extensionBuilderConfigurationManager->getSettings();
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
            $userSettings = $GLOBALS['BE_USER']->getModuleData('extensionbuilder');
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

        $this->addMainMenu('domainmodelling');
        //$this->addStoragePathMenu();

        $this->addLeftButtons();
        $this->addRightButtons();

        $this->pageRenderer->loadJavaScriptModule('@ebt/extension-builder/domain-modeling.js');
        $this->pageRenderer->addCssFile('EXT:extension_builder/Resources/Public/JavaScript/domain-modeling.css');

        $this->setLocallangSettings();

        $initialWarnings = [];
        if (!$this->extensionService->isStoragePathConfigured()) {
            $initialWarnings[] = ExtensionService::COMPOSER_PATH_WARNING;
        }
        $dispatchRpcUrl = (string)$this->backendUriBuilder->buildUriFromRoute('tools_extensionbuilder.BuilderModule_dispatchRpc');

        $this->view->assign('dispatchRpcUrl', $dispatchRpcUrl);
        $this->view->assign('initialWarnings', $initialWarnings);
        $GLOBALS['BE_USER']->pushModuleData('extensionbuilder', ['firstTime' => 0]);

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

        $openButton = (new LinkButtonWithId())
            ->setIcon($this->iconFactory->getIcon('actions-system-list-open', \TYPO3\CMS\Core\Imaging\IconSize::SMALL))
            ->setTitle('Open extension')
            ->setShowLabelText(true)
            ->setId('WiringEditor-loadButton-button')
            ->setHref('#');
        $buttonBar->addButton($openButton, ButtonBar::BUTTON_POSITION_LEFT, 1);

        $newButton = (new LinkButtonWithId())
            ->setIcon($this->iconFactory->getIcon('actions-document-new', \TYPO3\CMS\Core\Imaging\IconSize::SMALL))
            ->setTitle('New extension')
            ->setShowLabelText(true)
            ->setId('WiringEditor-newButton-button')
            ->setHref('#');
        $buttonBar->addButton($newButton, ButtonBar::BUTTON_POSITION_LEFT, 2);

        $saveButton = (new LinkButtonWithId())
            ->setIcon($this->iconFactory->getIcon('actions-document-save', \TYPO3\CMS\Core\Imaging\IconSize::SMALL))
            ->setTitle('Save extension')
            ->setShowLabelText(true)
            ->setId('WiringEditor-saveButton-button')
            ->setHref('#');
        $buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 3);
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

        $openInNewWindowButton = (new LinkButtonWithId())
            ->setHref('#')
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.openInNewWindow'))
            ->setIcon($this->iconFactory->getIcon('actions-window-open', \TYPO3\CMS\Core\Imaging\IconSize::SMALL))
            ->setDataAttributes([
                'dispatch-action' => 'TYPO3.WindowManager.localOpen',
                'dispatch-args' => GeneralUtility::jsonEncodeForHtmlAttribute([
                    $requestUri,
                    true, // switchFocus
                    'extension_builder', // windowName,
                    'width=1920,height=1080,status=0,menubar=0,scrollbars=1,resizable=1', // windowFeatures
                ]),
            ])
            ->setId('opennewwindow');

        $buttonBar->addButton($openInNewWindowButton, $position, $group);
    }

    protected function registerAdvancedOptionsButtonToButtonBar(ButtonBar $buttonBar, string $position, int $group): void
    {
        $advancedOptionsButton = (new LinkButtonWithId())
            ->setIcon($this->iconFactory->getIcon('content-menu-pages', \TYPO3\CMS\Core\Imaging\IconSize::SMALL))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:extension_builder/Resources/Private/Language/locallang.xlf:advancedOptions'))
            ->setId('toggleAdvancedOptions')
            ->setHref('#')
            ->setShowLabelText(true);
        $buttonBar->addButton($advancedOptionsButton, $position, $group);
    }

    protected function getLanguageService(): LanguageService
    {
        return $this->languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER']);
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
        $localizationArray = $this->localizationFactory->getParsedData(
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
                throw new Exception('No Sub Action!');
            }
            switch ($subAction) {
                case 'saveWiring':
                    $response = $this->rpcActionSave();
                    break;
                case 'listWirings':
                    $response = $this->rpcActionList();
                    break;
                default:
                    $response = ['error' => 'Sub Action not found.'];
            }
        } catch (\Throwable $e) {
            $response = ['error' => $e->getMessage()];
        }
        return $this->jsonResponse(json_encode($response));
    }

    /**
     * Generate the code files according to the transferred JSON configuration.
     *
     * @return array (status => message)
     * @throws Exception
     */
    protected function rpcActionSave(): array
    {
        try {
            $extensionBuildConfiguration = $this->extensionBuilderConfigurationManager->getConfigurationFromModeler();

            $storagePaths = $this->extensionService->resolveStoragePaths();
            $storagePath = reset($storagePaths);
            if ($storagePath === false) {
                throw new Exception('The storage path could not be detected.');
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
                return ['errors' => array_map(fn($e) => $e->getMessage(), $validationConfigurationResult['errors'])];
            }
            $extension = $this->extensionSchemaBuilder->build($extensionBuildConfiguration);
        } catch (Exception $e) {
            throw $e;
        }

        // Validate the extension
        $validationResult = $this->extensionValidator->validateExtension($extension);
        if (!empty($validationResult['errors'])) {
            return ['errors' => array_map(fn($e) => $e->getMessage(), $validationResult['errors'])];
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
                } catch (Exception $e) {
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
                        'warning' => LocalizationUtility::translate(
                            'notification.roundtrip_warning',
                            'ExtensionBuilder',
                            [$extensionPath . $extension->getExtensionKey()]
                        ) ?? sprintf(
                            'Roundtrip is enabled but no configuration file was found. A settings file was generated in %s/Configuration/ExtensionBuilder/settings.yaml. Configure the overwrite settings, then save again.',
                            $extensionPath . $extension->getExtensionKey()
                        ),
                    ];
                }
                try {
                    RoundTrip::prepareExtensionForRoundtrip($extension);
                } catch (Exception $e) {
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
                        ),
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
            $result = [
                'success' => LocalizationUtility::translate('notification.saved', 'ExtensionBuilder', [$extensionDirectory])
                    ?? sprintf('Extension saved in: %s', $extensionDirectory),
                'installationHints' => $this->extensionInstallationStatus->getStatusMessages(),
            ];
        } catch (Exception $e) {
            throw $e;
        }

        $this->extensionRepository->saveExtensionConfiguration($extension, $GLOBALS['BE_USER']);

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
            'error' => null,
        ];
    }

    /**
     * This is a hack to handle confirm requests in the GUI.
     *
     * @param Exception[] $warnings
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
            $messagesPerErrorCode[$errorCode][] = $exception->getMessage() . ' (Error ' . $errorCode . ')';
        }
        foreach ($messagesPerErrorCode as $errorCode => $messages) {
            if (!$this->extensionBuilderConfigurationManager->isConfirmed('allow' . $errorCode)) {
                if ($errorCode == ExtensionValidator::ERROR_PROPERTY_RESERVED_SQL_WORD) {
                    $confirmMessage = (LocalizationUtility::translate('notification.confirm_sql_word', 'ExtensionBuilder')
                        ?? 'SQL reserved names were found. This will result in a different column name. Are you sure?')
                        . "\n" . implode("\n", $messages);
                } else {
                    $confirmMessage = implode("\n", $messages) . "\n"
                        . (LocalizationUtility::translate('notification.confirm_overwrite', 'ExtensionBuilder')
                            ?? 'Do you want to save anyway?');
                }
                return [
                    'confirm' => $confirmMessage,
                    'confirmFieldName' => 'allow' . $errorCode,
                ];
            }
        }
        return [];
    }

}
