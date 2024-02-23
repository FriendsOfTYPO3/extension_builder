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

namespace EBT\ExtensionBuilder\Service;

use Symfony\Component\Yaml\Yaml;
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Model\BackendModule;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Exception\FileNotFoundException;
use EBT\ExtensionBuilder\Exception\SyntaxError;
use Exception;
use RuntimeException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Creates (or updates) all the required files for an extension
 */
class FileGenerator
{
    protected ClassBuilder $classBuilder;
    protected RoundTrip $roundTripService;
    protected Printer $printerService;
    protected LocalizationService $localizationService;
    protected array $codeTemplateRootPaths = [];
    protected array $codeTemplatePartialPaths = [];
    protected ?Extension $extension = null;
    protected string $extensionDirectory = '';
    protected string $configurationDirectory = '';
    protected string $languageDirectory = '';
    protected string $privateResourcesDirectory = '';
    protected string $iconsDirectory = '';
    protected bool $roundTripEnabled = false;
    /**
     * @var string[]
     */
    public static array $defaultActions = [
        'createAction',
        'deleteAction',
        'editAction',
        'listAction',
        'newAction',
        'showAction',
        'updateAction'
    ];
    /**
     * all file types where a split token makes sense
     *
     * @var string[]
     */
    protected array $filesSupportingSplitToken = [
        'php', //ext_tables, localconf
        'sql',
        'txt', // Typoscript
        'ts', // Typoscript
        'typoscript', // Typoscript
    ];
    /**
     * A map of deprecated file extensions
     * @var string[][]
     */
    protected array $deprecatedFileExtensions = [
        'typoscript' => ['ts', 'txt'],
    ];
    protected array $settings = [];

    public function injectClassBuilder(ClassBuilder $classBuilder): void
    {
        $this->classBuilder = $classBuilder;
    }

    public function injectRoundTripService(RoundTrip $roundTripService): void
    {
        $this->roundTripService = $roundTripService;
    }

    public function injectPrinterService(Printer $printerService): void
    {
        $this->printerService = $printerService;
    }

    public function injectLocalizationService(LocalizationService $localizationService): void
    {
        $this->localizationService = $localizationService;
    }

    /**
     * called by controller
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * The entry point to the class
     *
     * @param Extension $extension
     *
     * @throws Exception
     * @throws ExtensionException
     */
    public function build(Extension $extension): void
    {
        $this->extension = $extension;
        $enableRoundtrip = (bool)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('extension_builder', 'enableRoundtrip');

        if ($enableRoundtrip === true) {
            $this->roundTripEnabled = true;
            $this->roundTripService->initialize($extension);
        }
        if (isset($this->settings['codeTemplateRootPaths'])) {
            $this->codeTemplateRootPaths = $this->settings['codeTemplateRootPaths'];
        } else {
            throw new Exception('No codeTemplateRootPath configured');
        }
        if (isset($this->settings['codeTemplatePartialPaths'])) {
            $this->codeTemplatePartialPaths = $this->settings['codeTemplatePartialPaths'];
        } else {
            throw new Exception('No codeTemplatePartialPaths configured');
        }
        // Base directory already exists at this point
        $this->extensionDirectory = $this->extension->getExtensionDir();
        if (!is_dir($this->extensionDirectory)) {
            GeneralUtility::mkdir($this->extensionDirectory);
        }

        if ($extension->getGenerateEditorConfig()) {
            $this->generateEditorConfig();
        }

        $this->generateComposerJson();

        GeneralUtility::mkdir_deep($this->extensionDirectory . 'Configuration');

        $this->configurationDirectory = $this->extensionDirectory . 'Configuration/';

        GeneralUtility::mkdir_deep($this->extensionDirectory . 'Resources/Private');

        $this->privateResourcesDirectory = $this->extensionDirectory . 'Resources/Private/';

        $this->generateYamlSettingsFile();

        $this->generateExtensionFiles();

        $this->generatePluginFiles();

        $this->generateIconsFile();

        $this->generateModulesFile();

        // Only execute, if there is one or more domain object
        if (count($extension->getDomainObjects()) > 0) {
            $this->generateServicesYamlFile();
        }

        $this->copyStaticFiles();

        $this->generateTCAFiles();

        $this->generateFlexFormsFiles();

        $this->generateExtbaseConfigClass();

        $this->generateTyposcriptFiles();

        $this->generateHtaccessFile();

        $this->generateLocallangFiles();

        $this->generateDomainObjectRelatedFiles();

        if ($extension->getGenerateDocumentationTemplate()) {
            $this->generateDocumentationFiles();
        }

        if ($extension->getGenerateEmptyGitRepository()) {
            $this->generateEmptyGitRepository();
            $this->generateGitIgnore();
            $this->generateGitAttributes();
        }
    }

    protected function generateYamlSettingsFile(): void
    {
        if (!file_exists($this->configurationDirectory . 'ExtensionBuilder/settings.yaml')) {
            GeneralUtility::mkdir($this->configurationDirectory . 'ExtensionBuilder');
            $fileContents = $this->generateYamlSettings();
            $targetFile = $this->configurationDirectory . 'ExtensionBuilder/settings.yaml';
            GeneralUtility::writeFile($targetFile, $fileContents);
        }
    }

    /**
     * @throws Exception
     */
    protected function generateExtensionFiles(): void
    {
        // Generate ext_emconf.php, ext_tables.* and TCA definition
        $extensionFiles = ['ext_emconf.php', 'ext_tables.php', 'ext_tables.sql'];
        foreach ($extensionFiles as $extensionFile) {
            try {
                $fileContents = $this->renderTemplate(
                    GeneralUtility::underscoredToLowerCamelCase($extensionFile) . 't',
                    [
                        'extension' => $this->extension
                    ]
                );
                if ($extensionFile === 'ext_tables.sql') {
                    // replace trailing comma in last line before table definition end to get valid SQL
                    $fileContents = preg_replace('/,(\\s*\\);)/', '$1', $fileContents);
                }
                $this->writeFile($this->extensionDirectory . $extensionFile, $fileContents);
            } catch (Exception $e) {
                throw new Exception('Could not write ' . $extensionFile . ', error: ' . $e->getMessage());
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function generatePluginFiles(): void
    {
        if (!$this->extension->hasPlugins()) {
            return;
        }
        try {
            $fileContents = $this->renderTemplate(
                GeneralUtility::underscoredToLowerCamelCase('ext_localconf.phpt'),
                [
                    'extension' => $this->extension
                ]
            );
            $this->writeFile($this->extensionDirectory . 'ext_localconf.php', $fileContents);
        } catch (Exception $e) {
            throw new Exception('Could not write ext_localconf.php. Error: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    protected function generateIconsFile(): void
    {
        if (!$this->extension->hasPlugins()) {
            return;
        }
        try {
            GeneralUtility::mkdir_deep($this->extensionDirectory . 'Configuration');

            $fileContents = $this->renderTemplate(
                'Configuration/Icons.phpt',
                [
                    'extension' => $this->extension
                ]
            );
            $this->writeFile($this->extensionDirectory . 'Configuration/Icons.php', $fileContents);
        } catch (Exception $e) {
            throw new Exception('Could not write Configuration/Icons.php. Error: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    protected function generateModulesFile(): void
    {
        if (!$this->extension->hasBackendModules()) {
            return;
        }
        try {
            GeneralUtility::mkdir_deep($this->extensionDirectory . 'Configuration/Backend');

            $fileContents = $this->renderTemplate(
                'Configuration/Backend/Modules.phpt',
                [
                    'extension' => $this->extension
                ]
            );
            $this->writeFile($this->extensionDirectory . 'Configuration/Backend/Modules.php', $fileContents);
        } catch (Exception $e) {
            throw new Exception('Could not write Configuration/Backend/Modules.php. Error: ' . $e->getMessage());
        }
    }

    protected function generateServicesYamlFile(): void
    {
        try {
            GeneralUtility::mkdir_deep($this->extensionDirectory . 'Configuration');

            $fileContents = $this->renderTemplate(
                'Configuration/Services.yamlt',
                [
                    'namespace' => $this->extension->getNamespaceName()
                ]
            );
            // $this->writeFile($this->extensionDirectory . 'Configuration/Services.yaml', $yamlContent);
            $this->writeFile($this->extensionDirectory . 'Configuration/Services.yaml', $fileContents);
        } catch (Exception $e) {
            throw new Exception('Could not write Configuration/Services.yaml. Error: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    protected function generateTCAFiles(): void
    {
        try {
            GeneralUtility::mkdir_deep($this->extensionDirectory . 'Configuration/TCA');

            $domainObjects = $this->extension->getDomainObjects();

            foreach ($domainObjects as $domainObject) {
                if (!$domainObject->getMapToTable()) {
                    $fileContents = $this->generateTCA($domainObject);
                    $this->writeFile(
                        $this->configurationDirectory . 'TCA/' . $domainObject->getDatabaseTableName() . '.php',
                        $fileContents
                    );
                }
            }
            $domainObjectsNeedingOverrides = [];
            foreach ($this->extension->getDomainObjectsInHierarchicalOrder() as $domainObject) {
                if ($domainObject->needsTcaOverride()) {
                    if (!isset($domainObjectsNeedingOverrides[$domainObject->getDatabaseTableName()])) {
                        $domainObjectsNeedingOverrides[$domainObject->getDatabaseTableName()] = [];
                    }
                    $domainObjectsNeedingOverrides[$domainObject->getDatabaseTableName()][] = $domainObject;
                }
            }
            GeneralUtility::mkdir_deep($this->extensionDirectory . 'Configuration/TCA/Overrides');
            $tablesNeedingTypeFields = $this->extension->getTablesForTypeFieldDefinitions();
            foreach ($domainObjectsNeedingOverrides as $tableName => $domainObjects) {
                $addRecordTypeField = in_array($tableName, $tablesNeedingTypeFields, true);
                $fileContents = $this->generateTCAOverride($domainObjects, $addRecordTypeField);
                $this->writeFile(
                    $this->configurationDirectory . 'TCA/Overrides/' . $tableName . '.php',
                    $fileContents
                );
            }

            $fileContents = $this->generateTCAOverrideSysTemplate();
            $this->writeFile(
                $this->configurationDirectory . 'TCA/Overrides/sys_template.php',
                $fileContents
            );

            if ($this->extension->hasPlugins()) {
                // write tt_content.php
                $fileContents = $this->generateTCAOverrideTtContent();
                $this->writeFile(
                    $this->configurationDirectory . 'TCA/Overrides/tt_content.php',
                    $fileContents
                );
            }
        } catch (Exception $e) {
            throw new Exception('Could not generate TCA files, error: ' . $e->getMessage() . $e->getFile());
        }
    }

    /**
     * @throws Exception
     */
    protected function generateLocallangFiles(): void
    {
        // Generate locallang*.xlf files
        try {
            GeneralUtility::mkdir_deep($this->privateResourcesDirectory . 'Language');
            $this->languageDirectory = $this->privateResourcesDirectory . 'Language/';

            $fileContents = $this->generateLocallangFileContent('.xlf');
            $this->writeFile($this->languageDirectory . 'locallang.xlf', $fileContents);

            $fileContents = $this->generateLocallangFileContent('_db.xlf');
            $this->writeFile($this->languageDirectory . 'locallang_db.xlf', $fileContents);

            if ($this->extension->hasBackendModules()) {
                foreach ($this->extension->getBackendModules() as $backendModule) {
                    $fileContents = $this->generateLocallangFileContent('_mod', 'backendModule', $backendModule);
                    $this->writeFile(
                        $this->languageDirectory . 'locallang_' . $backendModule->getKey() . '.xlf',
                        $fileContents
                    );
                }
            }
            foreach ($this->extension->getDomainObjects() as $domainObject) {
                $fileContents = $this->generateLocallangFileContent('_csh_' . $domainObject->getDatabaseTableName() . '.xlf', 'domainObject', $domainObject);
                $this->writeFile(
                    $this->languageDirectory . 'locallang_csh_' . $domainObject->getDatabaseTableName() . '.xlf',
                    $fileContents
                );
            }
        } catch (Exception $e) {
            throw new Exception('Could not generate locallang files, error: ' . $e->getMessage());
        }
    }

    /**
     * @param string $templateSubFolder
     *
     * @throws Exception
     */
    protected function generateTemplateFiles(string $templateSubFolder = ''): void
    {
        $templateRootFolder = 'Resources/Private/' . $templateSubFolder;
        $absoluteTemplateRootFolder = $this->extensionDirectory . $templateRootFolder;

        $hasTemplates = false;
        foreach ($this->extension->getDomainObjects() as $domainObject) {
            /** @var DomainObject $domainObject */
            // Do not generate anything if $domainObject is not an
            // Entity or has no actions defined
            if (!$domainObject->getEntity() || (count($domainObject->getActions()) === 0)) {
                continue;
            }
            $domainTemplateDirectory = $absoluteTemplateRootFolder . 'Templates/' . $domainObject->getName() . '/';
            foreach ($domainObject->getActions() as $action) {
                /** @var Action $action */
                if (
                    $action->isCustomAction()
                    || (
                        $action->getNeedsTemplate()
                        && $this->templateExists($templateRootFolder . 'Templates/' . $action->getName() . '.htmlt')
                    )
                ) {
                    $hasTemplates = true;
                    $this->mkdir_deep(
                        $this->extensionDirectory,
                        $templateRootFolder . 'Templates/' . $domainObject->getName()
                    );
                    $fileContents = $this->generateDomainTemplate(
                        $templateRootFolder . 'Templates/',
                        $domainObject,
                        $action
                    );
                    $this->writeFile($domainTemplateDirectory . ucfirst($action->getName()) . '.html', $fileContents);
                    // generate partials for form fields
                    if ($action->getNeedsForm()) {
                        $this->mkdir_deep($absoluteTemplateRootFolder, 'Partials');
                        $partialDirectory = $absoluteTemplateRootFolder . 'Partials/';
                        $this->mkdir_deep($partialDirectory, $domainObject->getName());
                        $formFieldsPartial = $partialDirectory . $domainObject->getName() . '/FormFields.html';
                        $fileContents = $this->generateDomainFormFieldsPartial(
                            $templateRootFolder . 'Partials/',
                            $domainObject
                        );
                        $this->writeFile($formFieldsPartial, $fileContents);
                        if (!file_exists($partialDirectory . 'FormErrors.html')) {
                            $this->writeFile(
                                $partialDirectory . 'FormErrors.html',
                                $this->generateFormErrorsPartial($templateRootFolder . 'Partials/')
                            );
                        }
                    }
                    // generate partials for properties
                    if ($action->getNeedsPropertyPartial()) {
                        $this->mkdir_deep($absoluteTemplateRootFolder, 'Partials');
                        $partialDirectory = $absoluteTemplateRootFolder . 'Partials/';
                        $this->mkdir_deep($partialDirectory, $domainObject->getName());
                        $propertiesPartial = $partialDirectory . $domainObject->getName() . '/Properties.html';
                        $fileContents = $this->generateDomainPropertiesPartial(
                            $templateRootFolder . 'Partials/',
                            $domainObject
                        );
                        $this->writeFile($propertiesPartial, $fileContents);
                    }
                }
            }
        }
        if ($hasTemplates) {
            // Generate Layouts directory
            $this->mkdir_deep($absoluteTemplateRootFolder, 'Layouts');
            $layoutsDirectory = $absoluteTemplateRootFolder . 'Layouts/';
            $this->writeFile(
                $layoutsDirectory . 'Default.html',
                $this->generateLayout($templateRootFolder . 'Layouts/')
            );
        }
    }

    /**
     * get template file according to configured template root paths
     *
     * @param string $fileName
     *
     * @return string
     * @throws Exception
     */
    protected function getTemplatePath(string $fileName): string
    {
        foreach (array_reverse($this->codeTemplateRootPaths) as $rootPath) {
            $path = GeneralUtility::getFileAbsFileName($rootPath);
            if (file_exists($path . $fileName)) {
                return $path . $fileName;
            }
        }
        throw new Exception('template not found: ' . $fileName);
    }

    protected function templateExists(string $fileName): bool
    {
        try {
            $this->getTemplatePath($fileName);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    protected function generateTyposcriptFiles(): void
    {
        if ($this->extension->hasPlugins() || $this->extension->hasBackendModules()) {
            // Generate TypoScript setup
            try {
                $this->mkdir_deep($this->extensionDirectory, 'Configuration/TypoScript');
                $typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
                $fileContents = $this->generateTyposcriptSetup();
                $this->writeFile($typoscriptDirectory . 'setup.typoscript', $fileContents);
            } catch (Exception $e) {
                throw new Exception('Could not generate typoscript setup, error: ' . $e->getMessage());
            }

            // Generate TypoScript constants
            try {
                $typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
                $fileContents = $this->generateTyposcriptConstants();
                $this->writeFile($typoscriptDirectory . 'constants.typoscript', $fileContents);
            } catch (Exception $e) {
                throw new Exception('Could not generate typoscript constants, error: ' . $e->getMessage());
            }

            // Generate Static TypoScript
            try {
                if ($this->extension->getDomainObjectsThatNeedMappingStatements()) {
                    $fileContents = $this->generateStaticTyposcript();
                    $this->writeFile($this->extensionDirectory . 'ext_typoscript_setup.typoscript', $fileContents);
                }
            } catch (Exception $e) {
                throw new Exception('Could not generate static typoscript, error: ' . $e->getMessage());
            }
        }
    }

    /**
     * generate all domainObject related
     * files like PHP class files, templates etc.
     *
     * @throws Exception
     * @throws FileNotFoundException
     */
    protected function generateDomainObjectRelatedFiles(): void
    {
        if (count($this->extension->getDomainObjects()) > 0) {
            $this->classBuilder->initialize($this->extension);
            // Generate Domain Model
            try {
                $domainModelDirectory = 'Classes/Domain/Model/';
                $this->mkdir_deep($this->extensionDirectory, $domainModelDirectory);

                $domainRepositoryDirectory = 'Classes/Domain/Repository/';
                $this->mkdir_deep($this->extensionDirectory, $domainRepositoryDirectory);

                foreach ($this->extension->getDomainObjects() as $domainObject) {
                    $destinationFile = $domainModelDirectory . $domainObject->getName() . '.php';

                    $fileContents = $this->generateDomainObjectCode($domainObject);
                    $fileContents = preg_replace('#^[ \t]+$#m', '', $fileContents);
                    $this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
                    $this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);

                    if ($domainObject->isAggregateRoot()) {
                        $iconFileName = 'aggregate_root.gif';
                    } elseif ($domainObject->isEntity()) {
                        $iconFileName = 'entity.gif';
                    } else {
                        $iconFileName = 'value_object.gif';
                    }
                    $this->upload_copy_move(
                        ExtensionManagementUtility::extPath('extension_builder')
                        . 'Resources/Private/Icons/' . $iconFileName,
                        $this->iconsDirectory . $domainObject->getDatabaseTableName() . '.gif'
                    );

                    if ($domainObject->isAggregateRoot()) {
                        $destinationFile = $domainRepositoryDirectory . $domainObject->getName() . 'Repository.php';
                        $fileContents = $this->generateDomainRepositoryCode($domainObject);
                        $fileContents = preg_replace('#^[ \t]+$#m', '', $fileContents);
                        $this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
                        $this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);
                    }
                }
            } catch (Exception $e) {
                throw new Exception('Could not generate domain model, error: ' . $e->getMessage());
            }

            // Generate Action Controller
            try {
                $this->mkdir_deep($this->extensionDirectory, 'Classes/Controller');
                $controllerDirectory = 'Classes/Controller/';
                foreach ($this->extension->getDomainObjectsForWhichAControllerShouldBeBuilt() as $domainObject) {
                    $destinationFile = $controllerDirectory . $domainObject->getName() . 'Controller.php';
                    $fileContents = $this->generateActionControllerCode($domainObject);
                    $fileContents = preg_replace('#^[ \t]+$#m', '', $fileContents);
                    $this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
                    $this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);
                }
            } catch (Exception $e) {
                throw new Exception('Could not generate action controller, error: ' . $e->getMessage());
            }

            // Generate Domain Templates
            try {
                if ($this->extension->hasPlugins()) {
                    $this->generateTemplateFiles();
                }
                if ($this->extension->hasBackendModules()) {
                    $this->generateTemplateFiles('Backend/');
                }
            } catch (Exception $e) {
                throw new Exception(
                    sprintf(
                        'Could not generate domain templates, error: %s in %s line %s',
                        $e->getMessage(),
                        $e->getFile(),
                        $e->getLine()
                    )
                );
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function generateHtaccessFile(): void
    {
        // Generate Private Resources .htaccess
        try {
            $fileContents = $this->generatePrivateResourcesHtaccess();
            $this->writeFile($this->privateResourcesDirectory . '.htaccess', $fileContents);
        } catch (Exception $e) {
            throw new Exception('Could not create private resources folder, error: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    protected function copyStaticFiles(): void
    {
        $publicResourcesDirectory = $this->extensionDirectory . 'Resources/Public/';
        $this->iconsDirectory = $publicResourcesDirectory . 'Icons/';

        try {
            $this->mkdir_deep($this->extensionDirectory, 'Resources/Public');
            $this->mkdir_deep($publicResourcesDirectory, 'Icons');
        } catch (Exception $e) {
            throw new Exception('Could not create public resources folder, error: ' . $e->getMessage());
        }

        try {
            $this->upload_copy_move(
                $this->getTemplatePath('Resources/Public/Icons/user_extension.svg'),
                $this->iconsDirectory . 'Extension.svg'
            );

            $needsRelationIcon = false;
            foreach ($this->extension->getDomainObjects() as $domainObject) {
                if ($domainObject->hasRelations()) {
                    $needsRelationIcon = true;
                }
            }
            if ($needsRelationIcon) {
                $this->upload_copy_move(
                    ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/Icons/relation.gif',
                    $this->iconsDirectory . 'relation.gif'
                );
            }
            if ($this->extension->hasBackendModules()) {
                foreach ($this->extension->getBackendModules() as $backendModule) {
                    $this->upload_copy_move(
                        $this->getTemplatePath('Resources/Public/Icons/user_extension.svg'),
                        $this->iconsDirectory . 'user_mod_' . $backendModule->getKey() . '.svg'
                    );
                }
            }
            if ($this->extension->hasPlugins()) {
                foreach ($this->extension->getPlugins() as $plugin) {
                    $this->upload_copy_move(
                        $this->getTemplatePath('Resources/Public/Icons/user_extension.svg'),
                        $this->iconsDirectory . 'user_plugin_' . $plugin->getKey() . '.svg'
                    );
                }
            }
        } catch (Exception $e) {
            throw new Exception('Could not copy/move icon, error: ' . $e->getMessage());
        }
    }

    /**
     * generate the folder structure for reST documentation
     *
     * @throws Exception
     */
    protected function generateDocumentationFiles(): void
    {
        $this->mkdir_deep($this->extensionDirectory, 'Documentation');
        $docFiles = [];
        $docFiles = GeneralUtility::getAllFilesAndFoldersInPath(
            $docFiles,
            ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/CodeTemplates/Extbase/Documentation/',
            '',
            true,
            5,
            '.*(rstt|ymlt)'
        );
        foreach ($docFiles as $docFile) {
            if (is_dir($docFile)) {
                $this->mkdir_deep(
                    $this->extensionDirectory,
                    'Documentation/' . str_replace($this->getTemplatePath('Documentation/'), '', $docFile)
                );
            } elseif (strpos($docFile, '.rstt') === false && strpos($docFile, '.ymlt') === false) {
                $this->upload_copy_move(
                    $docFile,
                    str_replace(
                        ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/CodeTemplates/Extbase/',
                        $this->extensionDirectory,
                        $docFile
                    )
                );
            }
        }
        $fileContents = $this->renderTemplate('Documentation/Index.rstt', ['extension' => $this->extension]);
        $this->writeFile($this->extensionDirectory . 'Documentation/Index.rst', $fileContents);
        $fileContents = $this->renderTemplate('Documentation/Settings.cfgt', ['extension' => $this->extension]);
        $this->writeFile($this->extensionDirectory . 'Documentation/Settings.cfg', $fileContents);
    }

    protected function generateEmptyGitRepository(): void
    {
        $targetDirectory = $this->extensionDirectory . '.git';
        if (is_file($targetDirectory) || is_dir($targetDirectory) || is_link($targetDirectory)) {
            return;
        }
        $sourceDirectory = ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/CodeTemplates/Git/';
        foreach (['objects/info', 'objects/pack', 'refs/heads', 'refs/tags'] as $item) {
            $this->mkdir_deep($targetDirectory . '/' . $item, '');
        }
        foreach (['config', 'description', 'HEAD', 'info/exclude'] as $item) {
            $this->upload_copy_move($sourceDirectory . $item, $targetDirectory . '/' . $item);
        }
    }

    /**
     * Render a template with variables
     *
     * @param string $filePath
     * @param array $variables
     *
     * @return string|null
     * @throws Exception
     */
    public function renderTemplate(string $filePath, array $variables): ?string
    {
        $variables['settings'] = $this->settings;

        $standAloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standAloneView->setLayoutRootPaths($this->codeTemplateRootPaths);
        $standAloneView->setPartialRootPaths($this->codeTemplatePartialPaths);
        $standAloneView->setFormat('txt');
        $standAloneView->setTemplatePathAndFilename($this->getTemplatePath($filePath));
        $standAloneView->assignMultiple($variables);
        $renderedContent = $standAloneView->render();
        // remove all double empty lines (coming from fluid)
        return preg_replace('/^\\s*\\n[\\t ]*$/m', '', $renderedContent);
    }

    /**
     * Generates the code for the controller class
     * Either from actionController template or from class partial
     *
     * @param DomainObject $domainObject
     *
     * @return string
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function generateActionControllerCode(DomainObject $domainObject): string
    {
        $frontendControllerTemplateFilePath = $this->getTemplatePath('Classes/Controller/FrontendController.phpt');
        $backendControllerTemplateFilePath = $this->getTemplatePath('Classes/Controller/BackendController.phpt');

        $scope = $domainObject->getControllerScope();
        $existingClassFileObject = null;
        if ($this->roundTripEnabled) {
            $existingClassFileObject = $this->roundTripService->getControllerClassFile($domainObject);
        }
        $controllerClassFileObject = $this->classBuilder->generateControllerClassFileObject(
            $domainObject,
            $domainObject->getControllerScope() === 'Frontend' ? $frontendControllerTemplateFilePath : $backendControllerTemplateFilePath,
            $existingClassFileObject
        );
        // returns a class object if an existing class was found
        if ($controllerClassFileObject instanceof File) {
            $this->addLicenseHeader($controllerClassFileObject->getFirstClass());
            return $this->writeClassFile($controllerClassFileObject);
        }

        throw new Exception('Class file for controller could not be generated');
    }

    /**
     * Generates the code for the domain model class
     * Either from domainObject template or from class partial
     *
     * @param DomainObject $domainObject
     *
     * @return string
     * @throws FileNotFoundException
     * @throws SyntaxError
     * @throws Exception
     */
    public function generateDomainObjectCode(DomainObject $domainObject): string
    {
        $modelTemplateClassPath = $this->getTemplatePath('Classes/Domain/Model/Model.phpt');
        $existingClassFileObject = null;
        if ($this->roundTripEnabled) {
            $existingClassFileObject = $this->roundTripService->getDomainModelClassFile($domainObject);
        }
        $modelClassFileObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $modelTemplateClassPath,
            $existingClassFileObject
        );
        if ($modelClassFileObject) {
            $this->addLicenseHeader($modelClassFileObject->getFirstClass());
            return $this->writeClassFile($modelClassFileObject);
        }

        throw new Exception('Class file for domain object could not be generated');
    }

    /**
     * Generates the code for the repository class
     * Either from domainRepository template or from class partial
     *
     * @param DomainObject $domainObject
     *
     * @return string
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function generateDomainRepositoryCode(DomainObject $domainObject): string
    {
        $repositoryTemplateClassPath = $this->getTemplatePath('Classes/Domain/Repository/Repository.phpt');
        $existingClassFileObject = null;
        if ($this->roundTripEnabled) {
            $existingClassFileObject = $this->roundTripService->getRepositoryClassFile($domainObject);
        }
        $repositoryClassFileObject = $this->classBuilder->generateRepositoryClassFileObject(
            $domainObject,
            $repositoryTemplateClassPath,
            $existingClassFileObject
        );
        if ($repositoryClassFileObject) {
            $this->addLicenseHeader($repositoryClassFileObject->getFirstClass());
            return $this->writeClassFile($repositoryClassFileObject);
        }

        throw new Exception('Class file for repository could not be generated');
    }

    /**
     * @throws Exception
     */
    public function generateExtbaseConfigClass(): void
    {
        try {
            if ($this->extension->getDomainObjectsThatNeedMappingStatements()) {
                if (!is_dir($this->configurationDirectory . 'Extbase/Persistence/')) {
                    if (!mkdir($concurrentDirectory = $this->configurationDirectory . 'Extbase/Persistence/', 0775, true)
                       && !is_dir($concurrentDirectory)
                   ) {
                        throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                    }
                }
                $fileContents = $this->renderTemplate('Configuration/Extbase/Persistence/Classes.phpt', [
                   'extension' => $this->extension
               ]);
                $this->writeFile($this->configurationDirectory . 'Extbase/Persistence/Classes.php', $fileContents);
            }
        } catch (Exception $e) {
            throw new Exception('Could not generate Extbase Persistence Class Configuration, error: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function generateGitIgnore(): void
    {
        if (!file_exists($this->extensionDirectory . '.gitignore')) {
            // Generate .gitignore
            try {
                $fileContents = $this->renderTemplate('gitignore.t', []);
                $this->writeFile($this->extension->getExtensionDir() . '.gitignore', $fileContents);
            } catch (Exception $e) {
                throw new Exception('Could not create file, error: ' . $e->getMessage());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function generateGitAttributes(): void
    {
        if (!file_exists($this->extensionDirectory . '.gitattributes')) {
            // Generate .gitattributes
            try {
                $fileContents = $this->renderTemplate('gitattributes.t', []);
                $this->writeFile($this->extension->getExtensionDir() . '.gitattributes', $fileContents);
            } catch (Exception $e) {
                throw new Exception('Could not create file, error: ' . $e->getMessage());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function generateEditorConfig(): void
    {
        if (!file_exists($this->extensionDirectory . '.editorconfig')) {
            // Generate .editorconfig
            try {
                $fileContents = $this->renderTemplate('editorconfig.t', []);
                $this->writeFile($this->extension->getExtensionDir() . '.editorconfig', $fileContents);
            } catch (Exception $e) {
                throw new Exception('Could not create file, error: ' . $e->getMessage());
            }
        }
    }

    /**
     * create a basic composer file (only if none exists)
     *
     * @throws Exception
     */
    public function generateComposerJson(): void
    {
        if (!file_exists($this->extensionDirectory . 'composer.json')) {
            $composerInfo = $this->extension->getComposerInfo();
            $this->writeFile(
                $this->extension->getExtensionDir() . 'composer.json',
                json_encode($composerInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
        }
    }

    /**
     * generate a docComment for class files. Add a license header if none found
     *
     * @param ClassObject $classObject
     *
     * @return void;
     * @throws Exception
     */
    protected function addLicenseHeader(ClassObject $classObject): void
    {
        $comments = $classObject->getComments();
        $needsLicenseHeader = true;
        foreach ($comments as $comment) {
            if (strpos($comment, 'license') !== false) {
                $needsLicenseHeader = false;
            }
        }
        if (strpos($classObject->getDescription(), 'license') !== false) {
            $needsLicenseHeader = false;
        }
        $extensionSettings = $this->extension->getSettings();
        if ($needsLicenseHeader && empty($extensionSettings['skipDocComment'])) {
            $licenseHeader = $this->renderTemplate('Partials/Classes/licenseHeader.phpt', [
                'extension' => $this->extension
            ]);
            $classObject->addComment($licenseHeader);
        }
    }

    /**
     * Generates the content of an Action template
     * For some Actions default templates are provided,
     * other Action templates will just be created emtpy
     *
     * @param string $templateRootFolder
     * @param DomainObject $domainObject
     * @param Action $action
     *
     * @return string|null The generated Template code (might be empty)
     * @throws Exception
     */
    public function generateDomainTemplate(string $templateRootFolder, DomainObject $domainObject, Action $action): ?string
    {
        $fileName = $action->isCustomAction() ? 'custom' : $action->getName();
        return $this->renderTemplate($templateRootFolder . $fileName . '.htmlt', [
            'domainObject' => $domainObject,
            'action' => $action,
            'extension' => $this->extension
        ]);
    }

    /**
     * @param string $templateRootFolder
     * @param DomainObject $domainObject
     *
     * @return string|null
     * @throws Exception
     */
    public function generateDomainFormFieldsPartial(string $templateRootFolder, DomainObject $domainObject): ?string
    {
        return $this->renderTemplate($templateRootFolder . 'formFields.htmlt', [
            'extension' => $this->extension,
            'domainObject' => $domainObject
        ]);
    }

    /**
     * @param string $templateRootFolder
     * @param DomainObject $domainObject
     *
     * @return string|null
     * @throws Exception
     */
    public function generateDomainPropertiesPartial(string $templateRootFolder, DomainObject $domainObject): ?string
    {
        return $this->renderTemplate($templateRootFolder . 'properties.htmlt', [
            'extension' => $this->extension,
            'domainObject' => $domainObject
        ]);
    }

    /**
     * @param string $templateRootFolder
     *
     * @return string|null
     * @throws Exception
     */
    public function generateFormErrorsPartial(string $templateRootFolder): ?string
    {
        return $this->renderTemplate($templateRootFolder . 'formErrors.htmlt', [
            'extension' => $this->extension
        ]);
    }

    /**
     * @param string $templateRootFolder
     *
     * @return string|null
     * @throws Exception
     */
    public function generateLayout(string $templateRootFolder): ?string
    {
        return $this->renderTemplate($templateRootFolder . 'default.htmlt', [
            'extension' => $this->extension
        ]);
    }

    /**
     * @param string $fileNameSuffix (_db, _csh, _mod)
     * @param string $variableName
     * @param DomainObject|BackendModule $variable
     *
     * @return string|null
     * @throws Exception
     */
    protected function generateLocallangFileContent(string $fileNameSuffix = '', string $variableName = '', $variable = null): ?string
    {
        $targetFile = 'Resources/Private/Language/locallang' . $fileNameSuffix;

        $variableArray = [
            'extension' => $this->extension,
            'fileName' => 'EXT:' . $this->extension->getExtensionKey() . '/' . $targetFile,
        ];
        if ($variableName !== '') {
            $variableArray[$variableName] = $variable;
        }

        $languageLabels = $this->getLanguageLabelsForVariable($variableName, $variable, $fileNameSuffix);

        if ($this->fileShouldBeMerged($targetFile)) {
            $filenameToLookFor = $this->extensionDirectory . $targetFile;

            if (@file_exists($filenameToLookFor)) {
                $existingLabels = $this->localizationService->getLabelArrayFromFile($filenameToLookFor, 'default');
                if (is_array($existingLabels)) {
                    ArrayUtility::mergeRecursiveWithOverrule($languageLabels, $existingLabels);
                }
            }
        }
        if (empty($languageLabels)) {
            return '';
        }
        $variableArray['labelArray'] = $languageLabels;

        // TODO: sort by name like LFEditor
        return $this->renderTemplate('Resources/Private/Language/locallang.xlft', $variableArray);
    }

    protected function getLanguageLabelsForVariable(string $variableName, $variable, string $fileNameSuffix): array
    {
        if ($variableName === 'domainObject') {
            return $this->localizationService->prepareLabelArrayForContextHelp($variable);
        }

        if ($variableName === 'backendModule') {
            return $this->localizationService->prepareLabelArrayForBackendModule($variable);
        }

        return $this->localizationService->prepareLabelArray(
            $this->extension,
            'locallang' . $fileNameSuffix
        );
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function generatePrivateResourcesHtaccess(): ?string
    {
        return $this->renderTemplate('Resources/Private/htaccess.t', []);
    }

    /**
     * @param DomainObject $domainObject
     *
     * @return string|null
     * @throws Exception
     */
    public function generateTCA(DomainObject $domainObject): ?string
    {
        return $this->renderTemplate('Configuration/TCA/tableName.phpt', [
            'extension' => $this->extension,
            'domainObject' => $domainObject
        ]);
    }

    /**
     * Overrides are needed for single table inheritance
     *
     * @param array $domainObjects
     * @param bool $addRecordTypeField
     *
     * @return mixed
     * @throws Exception
     */
    public function generateTCAOverride(array $domainObjects, bool $addRecordTypeField): ?string
    {
        return $this->renderTemplate('Configuration/TCA/Overrides/tableName.phpt', [
            'extension' => $this->extension,
            'rootDomainObject' => reset($domainObjects),
            'domainObjects' => $domainObjects,
            'addRecordTypeField' => $addRecordTypeField
        ]);
    }

    /**
     * Add TCA configuration for tt_content
     *
     * @return mixed
     * @throws Exception
     */
    public function generateTCAOverrideTtContent(): ?string
    {
        return $this->renderTemplate('Configuration/TCA/Overrides/tt_content.phpt', [
            'extension' => $this->extension,
        ]);
    }

    /**
     * Generates the content of each FlexForm File
     *
     * @return mixed
     * @throws Exception
     */
    public function generateFlexFormsFiles(): void
    {
        if($this->extension->hasPlugins()) {
            $this->mkdir_deep($this->extensionDirectory, 'Configuration/FlexForms');
        } else {
            // no plugins, no FlexForms
            return;
        }

        foreach ($this->extension->getPlugins() as $plugin) {
            // check if file already exists
            if (file_exists($this->extensionDirectory . 'Configuration/FlexForms/flexform_' . $plugin->getKey() . '.xml')) {
                continue;
            }

            $fileContents = $this->renderTemplate('Configuration/FlexForms/plugin_flexform.phpt', [
                'extension' => $this->extension,
                'plugin' => $plugin
            ]);
            $this->writeFile(
                $this->extensionDirectory . 'Configuration/FlexForms/flexform_' . $plugin->getKey() . '.xml',
                $fileContents
            );
        }
    }

    /**
     * Add TCA configuration for sys_template
     *
     * @return string|null
     * @throws Exception
     */
    public function generateTCAOverrideSysTemplate(): ?string
    {
        return $this->renderTemplate('Configuration/TCA/Overrides/sys_template.phpt', [
            'extension' => $this->extension,
        ]);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function generateYamlSettings(): ?string
    {
        return $this->renderTemplate('Configuration/ExtensionBuilder/settings.yamlt', [
            'extension' => $this->extension
        ]);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function generateTyposcriptSetup(): ?string
    {
        return $this->renderTemplate('Configuration/TypoScript/setup.typoscriptt', [
            'extension' => $this->extension
        ]);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function generateTyposcriptConstants(): ?string
    {
        return $this->renderTemplate('Configuration/TypoScript/constants.typoscriptt', [
            'extension' => $this->extension
        ]);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function generateStaticTyposcript(): ?string
    {
        return $this->renderTemplate('ext_typoscript_setup.txtt', [
            'extension' => $this->extension
        ]);
    }

    /**
     * @param string $extensionDirectory
     * @param string $classType
     * @param bool $createDirIfNotExist
     *
     * @return string
     * @throws Exception
     */
    public static function getFolderForClassFile(string $extensionDirectory, string $classType, bool $createDirIfNotExist = true): string
    {
        $classPath = '';
        switch ($classType) {
            case 'Model':
                $classPath = 'Classes/Domain/Model/';
                break;

            case 'Controller':
                $classPath = 'Classes/Controller/';
                break;

            case 'Repository':
                $classPath = 'Classes/Domain/Repository/';
                break;
        }
        if (!empty($classPath)) {
            if ($createDirIfNotExist && !is_dir($extensionDirectory . $classPath)) {
                GeneralUtility::mkdir_deep($extensionDirectory . $classPath);
            }
            if ($createDirIfNotExist && !is_dir($extensionDirectory . $classPath)) {
                throw new Exception('folder could not be created:' . $extensionDirectory . $classPath);
            }
            return $extensionDirectory . $classPath;
        }

        throw new Exception('Unexpected classPath:' . $classPath);
    }

    /**
     * passes the declareStrictTypes flag from settings
     * as argument to printer Service
     *
     * @param File $classFileObject
     * @return string
     */
    protected function writeClassFile(File $classFileObject): string
    {
        $extensionSettings = $this->extension->getSettings();
        $declareStrictTypes = $extensionSettings['declareStrictTypes'] ?? true;
        return $this->printerService->renderFileObject($classFileObject, $declareStrictTypes);
    }

    /**
     * wrapper for GeneralUtility::writeFile
     * checks for overwrite settings
     *
     * path and filename of the targetFile, relative to extension dir:
     * @param string $targetFile
     * @param string $fileContents
     * @throws Exception
     */
    protected function writeFile(string $targetFile, string $fileContents): void
    {
        if ($this->roundTripEnabled) {
            $overWriteMode = RoundTrip::getOverWriteSettingForPath(
                $targetFile,
                $this->extension
            );
            if ($overWriteMode == RoundTrip::OVERWRITE_SETTINGS_SKIP) {
                // skip file creation
                return;
            }

            $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // special handling for TypoScript files, that used to use "ts" as extension and now use "typoscript":
            // if a ".typoscript" file SHOULD be written but a corresponding ".ts" file with the same name already
            // exists, write to THAT file in order to avoid breaking users' extensions.
            if (array_key_exists($fileExtension, $this->deprecatedFileExtensions)) {
                foreach ($this->deprecatedFileExtensions[$fileExtension] as $possibleExistingExtension) {
                    $possibleAlternateTarget = str_replace(
                        '.' . $fileExtension,
                        '.' . $possibleExistingExtension,
                        $targetFile
                    );
                    if (file_exists($possibleAlternateTarget)) {
                        $targetFile = $possibleAlternateTarget;
                        break;
                    }
                }
            }

            if ($overWriteMode == RoundTrip::OVERWRITE_SETTINGS_MERGE && strpos($targetFile, 'Classes') === false) {
                // classes are merged by the class builder
                if ($fileExtension == 'html') {
                    //TODO: We need some kind of protocol to be displayed after code generation
                    return;
                }

                if (in_array($fileExtension, $this->filesSupportingSplitToken)) {
                    $fileContents = $this->insertSplitToken($targetFile, $fileContents);
                }
            } elseif (file_exists($targetFile) && $overWriteMode == RoundTrip::OVERWRITE_SETTINGS_KEEP) {
                // keep the existing file
                return;
            }
        }

        if (empty($fileContents)) {
            return;
        }
        $success = GeneralUtility::writeFile($targetFile, $fileContents, true);
        if (!$success) {
            throw new Exception('File ' . $targetFile . ' could not be created!');
        }
    }

    /**
     * @param $destinationFile
     *
     * @return bool
     * @throws Exception
     */
    protected function fileShouldBeMerged($destinationFile): bool
    {
        $overwriteSettings = RoundTrip::getOverWriteSettingForPath($destinationFile, $this->extension);
        return $this->roundTripEnabled && $overwriteSettings > 0;
    }

    /**
     * Inserts the token into the file content
     * and preserves everything below the token
     */
    protected function insertSplitToken(string $targetFile, string $fileContents): string
    {
        $customFileContent = '';
        if (file_exists($targetFile)) {
            // merge the files means append everything behind the split token
            $existingFileContent = file_get_contents($targetFile);

            $fileParts = explode(RoundTrip::SPLIT_TOKEN, $existingFileContent);
            if (count($fileParts) === 2) {
                $customFileContent = str_replace('?>', '', $fileParts[1]);
            }
        }

        $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($fileExtension === 'php') {
            $fileContents = str_replace('?>', '', $fileContents);
            $fileContents .= RoundTrip::SPLIT_TOKEN;
        } else {
            $fileContents .= LF . RoundTrip::SPLIT_TOKEN;
        }

        $fileContents .= rtrim($customFileContent);

        return $fileContents;
    }

    /**
     * wrapper for GeneralUtility::writeFile
     * checks for overwrite settings
     *
     * @param string $sourceFile
     * @param string $targetFile the path and filename of the targetFile
     *
     * @throws Exception
     */
    protected function upload_copy_move(string $sourceFile, string $targetFile): void
    {
        $overWriteMode = RoundTrip::getOverWriteSettingForPath($targetFile, $this->extension);
        if ($overWriteMode === -1) {
            // skip creation
            return;
        }
        if (!file_exists($targetFile) || ($this->roundTripEnabled && $overWriteMode < 2)) {
            GeneralUtility::upload_copy_move($sourceFile, $targetFile);
        }
    }

    /**
     * wrapper for GeneralUtility::mkdir_deep
     * checks for overwrite settings
     *
     * @param string $directory base path
     * @param string $deepDirectory
     *
     * @throws Exception
     */
    protected function mkdir_deep(string $directory, string $deepDirectory): void
    {
        if (!$this->roundTripEnabled) {
            GeneralUtility::mkdir_deep($directory . $deepDirectory);
            return;
        }

        $subDirectories = explode('/', $deepDirectory);
        $tmpBasePath = $directory;
        foreach ($subDirectories as $subDirectory) {
            $overWriteMode = RoundTrip::getOverWriteSettingForPath(
                $tmpBasePath . $subDirectory,
                $this->extension
            );
            //throw new \Exception($directory . $subDirectory . '/' . $overWriteMode);
            if ($overWriteMode === -1) {
                // skip creation
                return;
            }
            if (!is_dir($deepDirectory) || ($this->roundTripEnabled && $overWriteMode < 2)) {
                GeneralUtility::mkdir_deep($tmpBasePath . $subDirectory);
            }
            $tmpBasePath .= $subDirectory . '/';
        }
    }
}
