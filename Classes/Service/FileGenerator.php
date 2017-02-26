<?php
namespace EBT\ExtensionBuilder\Service;

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

use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Creates (or updates) all the required files for an extension
 */
class FileGenerator
{
    /**
     * @var \EBT\ExtensionBuilder\Service\ClassBuilder
     * @inject
     *
     */
    protected $classBuilder = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\RoundTrip
     * @inject
     *
     */
    protected $roundTripService = null;
    /**
     * @var string
     */
    protected $codeTemplateRootPath = '';
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;
    /**
     * @var string
     */
    protected $extensionDirectory = '';
    /**
     * @var string
     */
    protected $configurationDirectory = '';
    /**
     * @var string
     */
    protected $languageDirectory = '';
    /**
     * @var string
     */
    protected $privateResourcesDirectory = '';
    /**
     * @var string
     */
    protected $iconsDirectory = '';
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager = null;
    /**
     * @var array
     */
    protected $overWriteSettings = array();
    /**
     * @var bool
     */
    protected $roundTripEnabled = false;
    /**
     * @var array
     */
    protected $settings = array();
    /**
     * @var \EBT\ExtensionBuilder\Service\Printer
     * @inject
     */
    protected $printerService = null;
    /**
     * @var string[]
     */
    public static $defaultActions = array(
        'createAction',
        'deleteAction',
        'editAction',
        'listAction',
        'newAction',
        'showAction',
        'updateAction'
    );
    /**
     * all file types where a split token makes sense
     *
     * @var string[]
     */
    protected $filesSupportingSplitToken = array(
        'php', //ext_tables, localconf
        'sql',
        'txt', // Typoscript
        'ts' // Typoscript
    );
    /**
     * @var \EBT\ExtensionBuilder\Service\LocalizationService
     * @inject
     *
     */
    protected $localizationService = null;

    /**
     * called by controller
     * @param array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * The entry point to the class
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     */
    public function build(\EBT\ExtensionBuilder\Domain\Model\Extension $extension)
    {
        $this->extension = $extension;
        if ($this->settings['extConf']['enableRoundtrip'] == 1) {
            $this->roundTripEnabled = true;
            $this->roundTripService->initialize($extension);
        }
        if (isset($this->settings['codeTemplateRootPath'])) {
            $this->codeTemplateRootPath = $this->settings['codeTemplateRootPath'];
        } else {
            throw new \Exception('No codeTemplateRootPath configured');
        }
        // Base directory already exists at this point
        $this->extensionDirectory = $this->extension->getExtensionDir();
        if (!is_dir($this->extensionDirectory)) {
            GeneralUtility::mkdir($this->extensionDirectory);
        }

        GeneralUtility::mkdir_deep($this->extensionDirectory, 'Configuration');

        $this->configurationDirectory = $this->extensionDirectory . 'Configuration/';

        GeneralUtility::mkdir_deep($this->extensionDirectory, 'Resources/Private');

        $this->privateResourcesDirectory = $this->extensionDirectory . 'Resources/Private/';

        $this->generateYamlSettingsFile();

        $this->generateExtensionFiles();

        $this->generatePluginFiles();

        $this->copyStaticFiles();

        $this->generateTCAFiles();

        $this->generateTyposcriptFiles();

        $this->generateHtaccessFile();

        $this->generateLocallangFiles();

        $this->generateDomainObjectRelatedFiles();

        if ($extension->getGenerateDocumentationTemplate()) {
            $this->generateDocumentationFiles();
        }
    }

    protected function generateYamlSettingsFile()
    {
        if (!file_exists($this->configurationDirectory . 'ExtensionBuilder/settings.yaml')) {
            GeneralUtility::mkdir($this->configurationDirectory . 'ExtensionBuilder');
            $fileContents = $this->generateYamlSettings();
            $targetFile = $this->configurationDirectory . 'ExtensionBuilder/settings.yaml';
            GeneralUtility::writeFile($targetFile, $fileContents);
        }
    }

    protected function generateExtensionFiles()
    {
        // Generate ext_emconf.php, ext_tables.* and TCA definition
        $extensionFiles = array('ext_emconf.php', 'ext_tables.php', 'ext_tables.sql');
        foreach ($extensionFiles as $extensionFile) {
            try {
                $fileContents = $this->renderTemplate(
                    GeneralUtility::underscoredToLowerCamelCase($extensionFile) . 't',
                    array(
                        'extension' => $this->extension
                    )
                );
                $this->writeFile($this->extensionDirectory . $extensionFile, $fileContents);
                GeneralUtility::devLog(
                    'Generated ' . $extensionFile,
                    'extension_builder',
                    0,
                    array('Content' => $fileContents)
                );
            } catch (\Exception $e) {
                throw new \Exception('Could not write ' . $extensionFile . ', error: ' . $e->getMessage());
            }
        }
    }

    protected function generatePluginFiles()
    {
        if ($this->extension->getPlugins()) {
            try {
                $fileContents = $this->renderTemplate(
                    GeneralUtility::underscoredToLowerCamelCase('ext_localconf.phpt'),
                    array('extension' => $this->extension)
                );
                $this->writeFile($this->extensionDirectory . 'ext_localconf.php', $fileContents);
                GeneralUtility::devLog(
                    'Generated ext_localconf.php',
                    'extension_builder',
                    0,
                    array('Content' => $fileContents)
                );
            } catch (\Exception $e) {
                throw new \Exception('Could not write ext_localconf.php. Error: ' . $e->getMessage());
            }
            $currentPluginKey = '';
            try {
                foreach ($this->extension->getPlugins() as $plugin) {
                    /**
                     * @var $plugin \EBT\ExtensionBuilder\Domain\Model\Plugin
                     */
                    if ($plugin->getSwitchableControllerActions()) {
                        if (!is_dir($this->extensionDirectory . 'Configuration/FlexForms')) {
                            $this->mkdir_deep($this->extensionDirectory, 'Configuration/FlexForms');
                        }
                        $currentPluginKey = $plugin->getKey();
                        $fileContents = $this->renderTemplate(
                            'Configuration/Flexforms/flexform.xmlt',
                            array('plugin' => $plugin)
                        );
                        $this->writeFile(
                            $this->extensionDirectory . 'Configuration/FlexForms/flexform_' . $currentPluginKey . '.xml',
                            $fileContents
                        );
                        GeneralUtility::devLog(
                            'Generated flexform_' . $currentPluginKey . '.xml',
                            'extension_builder',
                            0,
                            array('Content' => $fileContents));
                    }
                }
            } catch (\Exception $e) {
                throw new \Exception('Could not write  flexform_' . $currentPluginKey . '.xml. Error: ' . $e->getMessage());
            }
        }
    }

    protected function generateTCAFiles()
    {
        // Generate TCA
        try {
            GeneralUtility::mkdir_deep($this->extensionDirectory, 'Configuration/TCA');

            $domainObjects = $this->extension->getDomainObjects();

            foreach ($domainObjects as $domainObject) {
                /**
                 * @var $domainObject \EBT\ExtensionBuilder\Domain\Model\DomainObject
                 */
                if (!$domainObject->getMapToTable()) {
                    $fileContents = $this->generateTCA($domainObject);
                    $this->writeFile(
                        $this->configurationDirectory . 'TCA/' . $domainObject->getDatabaseTableName() . '.php',
                        $fileContents
                    );
                }
            }
            $domainObjectsNeedingOverrides = array();
            foreach ($this->extension->getDomainObjectsInHierarchicalOrder() as $domainObject) {
                if ($domainObject->isMappedToExistingTable() || $domainObject->getHasChildren()) {
                    if (!isset($domainObjectsNeedingOverrides[$domainObject->getDatabaseTableName()])) {
                        $domainObjectsNeedingOverrides[$domainObject->getDatabaseTableName()] = array();
                    }
                    $domainObjectsNeedingOverrides[$domainObject->getDatabaseTableName()][] = $domainObject;
                }
            }
            if (count($domainObjectsNeedingOverrides) > 0) {
                GeneralUtility::mkdir_deep($this->extensionDirectory, 'Configuration/TCA/Overrides');
            }
            $tablesNeedingTypeFields = $this->extension->getTablesForTypeFieldDefinitions();
            foreach ($domainObjectsNeedingOverrides as $tableName => $domainObjects) {
                $addRecordTypeField = in_array($tableName, $tablesNeedingTypeFields);
                $fileContents = $this->generateTCAOverride($domainObjects, $addRecordTypeField);
                $this->writeFile(
                    $this->configurationDirectory . 'TCA/Overrides/' . $tableName . '.php',
                    $fileContents
                );
            }
        } catch (\Exception $e) {
            throw new \Exception('Could not generate TCA files, error: ' . $e->getMessage() . $e->getFile());
        }
    }

    protected function generateLocallangFiles()
    {
        // Generate locallang*.xlf files
        try {
            GeneralUtility::mkdir_deep($this->privateResourcesDirectory, 'Language');
            $this->languageDirectory = $this->privateResourcesDirectory . 'Language/';
            $fileContents = $this->generateLocallangFileContent();
            $this->writeFile($this->languageDirectory . 'locallang.xlf', $fileContents);
            $fileContents = $this->generateLocallangFileContent('_db');
            $this->writeFile($this->languageDirectory . 'locallang_db.xlf', $fileContents);
            if ($this->extension->hasBackendModules()) {
                /** @var $backendModule \EBT\ExtensionBuilder\Domain\Model\Plugin */
                foreach ($this->extension->getBackendModules() as $backendModule) {
                    $fileContents = $this->generateLocallangFileContent('_mod', 'backendModule', $backendModule);
                    $this->writeFile(
                        $this->languageDirectory . 'locallang_' . $backendModule->getKey() . '.xlf',
                        $fileContents
                    );
                }
            }
            foreach ($this->extension->getDomainObjects() as $domainObject) {
                $fileContents = $this->generateLocallangFileContent('_csh', 'domainObject', $domainObject);
                $this->writeFile(
                    $this->languageDirectory . 'locallang_csh_' . $domainObject->getDatabaseTableName() . '.xlf',
                    $fileContents
                );
            }
        } catch (\Exception $e) {
            throw new \Exception('Could not generate locallang files, error: ' . $e->getMessage());
        }
    }

    protected function generateTemplateFiles($templateSubFolder = '')
    {
        $templateRootFolder = 'Resources/Private/' . $templateSubFolder;
        $absoluteTemplateRootFolder = $this->extensionDirectory . $templateRootFolder;

        $hasTemplates = false;
        foreach ($this->extension->getDomainObjects() as $domainObject) {
            /**
             * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
             */
            // Do not generate anyting if $domainObject is not an
            // Entity or has no actions defined
            if (!$domainObject->getEntity() || (count($domainObject->getActions()) == 0)) {
                continue;
            }
            $domainTemplateDirectory = $absoluteTemplateRootFolder . 'Templates/' . $domainObject->getName() . '/';
            foreach ($domainObject->getActions() as $action) {
                /**
                 * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject\Action $action
                 */
                if ($action->getNeedsTemplate()
                    && file_exists($this->codeTemplateRootPath . $templateRootFolder . 'Templates/' . $action->getName() . '.htmlt')

                ) {
                    $hasTemplates = true;
                    $this->mkdir_deep(
                        $this->extensionDirectory, $templateRootFolder . 'Templates/' . $domainObject->getName()
                    );
                    $fileContents = $this->generateDomainTemplate(
                        $templateRootFolder . 'Templates/',
                        $domainObject,
                        $action
                    );
                    $this->writeFile($domainTemplateDirectory . ucfirst($action->getName()) . '.html', $fileContents);
                    // generate partials for formfields
                    if ($action->getNeedsForm()) {
                        $this->mkdir_deep($absoluteTemplateRootFolder, 'Partials');
                        $partialDirectory = $absoluteTemplateRootFolder . 'Partials/';
                        $this->mkdir_deep($partialDirectory, $domainObject->getName());
                        $formfieldsPartial = $partialDirectory . $domainObject->getName() . '/FormFields.html';
                        $fileContents = $this->generateDomainFormFieldsPartial(
                            $templateRootFolder . 'Partials/',
                            $domainObject
                        );
                        $this->writeFile($formfieldsPartial, $fileContents);
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
            $this->writeFile($layoutsDirectory . 'Default.html', $this->generateLayout($templateRootFolder . 'Layouts/'));
        }
    }

    protected function generateTyposcriptFiles()
    {
        if ($this->extension->hasPlugins() || $this->extension->hasBackendModules()) {
            // Generate TypoScript setup
            try {
                $this->mkdir_deep($this->extensionDirectory, 'Configuration/TypoScript');
                $typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
                $fileContents = $this->generateTyposcriptSetup();
                $this->writeFile($typoscriptDirectory . 'setup.ts', $fileContents);
            } catch (\Exception $e) {
                throw new \Exception('Could not generate typoscript setup, error: ' . $e->getMessage());
            }

            // Generate TypoScript constants
            try {
                $typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
                $fileContents = $this->generateTyposcriptConstants();
                $this->writeFile($typoscriptDirectory . 'constants.ts', $fileContents);
            } catch (\Exception $e) {
                throw new \Exception('Could not generate typoscript constants, error: ' . $e->getMessage());
            }
        }

        // Generate Static TypoScript
        try {
            if ($this->extension->getDomainObjectsThatNeedMappingStatements()) {
                $fileContents = $this->generateStaticTyposcript();
                $this->writeFile($this->extensionDirectory . 'ext_typoscript_setup.txt', $fileContents);
            }
        } catch (\Exception $e) {
            throw new \Exception('Could not generate static typoscript, error: ' . $e->getMessage());
        }
    }

    /**
     * generate all domainObject related
     * files like PHP class files, templates etc.
     *
     * @throws \Exception
     */
    protected function generateDomainObjectRelatedFiles()
    {
        if (count($this->extension->getDomainObjects()) > 0) {
            $this->classBuilder->initialize($this->extension);
            // Generate Domain Model
            try {
                $domainModelDirectory = 'Classes/Domain/Model/';
                $this->mkdir_deep($this->extensionDirectory, $domainModelDirectory);

                $domainRepositoryDirectory = 'Classes/Domain/Repository/';
                $this->mkdir_deep($this->extensionDirectory, $domainRepositoryDirectory);

                $this->mkdir_deep($this->extensionDirectory, 'Tests/Unit/Domain/Model');
                $domainModelTestsDirectory = $this->extensionDirectory . 'Tests/Unit/Domain/Model/';

                $this->mkdir_deep($this->extensionDirectory, 'Tests/Unit/Controller');
                $crudEnabledControllerTestsDirectory = $this->extensionDirectory . 'Tests/Unit/Controller/';

                foreach ($this->extension->getDomainObjects() as $domainObject) {
                    /**
                     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
                     */
                    $destinationFile = $domainModelDirectory . $domainObject->getName() . '.php';

                    $fileContents = $this->generateDomainObjectCode($domainObject);
                    $fileContents = preg_replace('#^[ \t]+$#m', '', $fileContents);
                    $this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
                    GeneralUtility::devLog(
                        'Generated ' . $domainObject->getName() . '.php',
                        'extension_builder',
                        0
                    );
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
                        GeneralUtility::devLog(
                            'Generated ' . $domainObject->getName() . 'Repository.php',
                            'extension_builder',
                            0
                        );
                        $this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);
                    }

                    // Generate basic UnitTests
                    $fileContents = $this->generateDomainModelTests($domainObject);
                    $fileContents = preg_replace('#^[ \t]+$#m', '', $fileContents);
                    $this->writeFile($domainModelTestsDirectory . $domainObject->getName() . 'Test.php', $fileContents);
                }
            } catch (\Exception $e) {
                throw new \Exception('Could not generate domain model, error: ' . $e->getMessage());
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
                    GeneralUtility::devLog(
                        'Generated ' . $domainObject->getName() . 'Controller.php',
                        'extension_builder',
                        0
                    );
                    $this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);

                    // Generate basic UnitTests
                    $fileContents = $this->generateControllerTests(
                        $domainObject->getName() . 'Controller',
                        $domainObject
                    );
                    $fileContents = preg_replace('#^[ \t]+$#m', '', $fileContents);
                    $this->writeFile(
                        $crudEnabledControllerTestsDirectory . $domainObject->getName() . 'ControllerTest.php',
                        $fileContents
                    );
                }
            } catch (\Exception $e) {
                throw new \Exception('Could not generate action controller, error: ' . $e->getMessage());
            }

            // Generate Domain Templates
            try {
                if ($this->extension->getPlugins()) {
                    $this->generateTemplateFiles();
                }
                if ($this->extension->getBackendModules()) {
                    $this->generateTemplateFiles('Backend/');
                }
            } catch (\Exception $e) {
                throw new \Exception('Could not generate domain templates, error: ' . $e->getMessage());
            }

            try {
                $settings = $this->extension->getSettings();
            } catch (\Exception $e) {
                throw new \Exception('Could not generate ext_autoload.php, error: ' . $e->getMessage());
            }
        } else {
            GeneralUtility::devLog(
                'No domainObjects in this extension',
                'extension_builder',
                3,
                (array)$this->extension
            );
        }
    }

    protected function generateHtaccessFile()
    {
        // Generate Private Resources .htaccess
        try {
            $fileContents = $this->generatePrivateResourcesHtaccess();
            $this->writeFile($this->privateResourcesDirectory . '.htaccess', $fileContents);
        } catch (\Exception $e) {
            throw new \Exception('Could not create private resources folder, error: ' . $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    protected function copyStaticFiles()
    {
        try {
            $this->upload_copy_move(
                ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/Icons/ext_icon.gif',
                $this->extensionDirectory . 'ext_icon.gif'
            );
        } catch (\Exception $e) {
            throw new \Exception('Could not copy ext_icon.gif, error: ' . $e->getMessage());
        }

        try {
            $this->mkdir_deep($this->extensionDirectory, 'Resources/Public');
            $publicResourcesDirectory = $this->extensionDirectory . 'Resources/Public/';
            $this->mkdir_deep($publicResourcesDirectory, 'Icons');
            $this->iconsDirectory = $publicResourcesDirectory . 'Icons/';
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
            foreach ($this->extension->getBackendModules() as $backendModule) {
                $this->upload_copy_move(
                    $this->codeTemplateRootPath. 'Resources/Public/Icons/user_extension.svg',
                    $this->iconsDirectory . 'user_mod_' . $backendModule->getKey() . '.svg'
                );
            }
            foreach ($this->extension->getPlugins() as $plugin) {
                $this->upload_copy_move(
                    $this->codeTemplateRootPath. 'Resources/Public/Icons/user_extension.svg',
                    $this->iconsDirectory . 'user_plugin_' . $plugin->getKey() . '.svg'
                );
            }
        } catch (\Exception $e) {
            throw new \Exception('Could not create public resources folder, error: ' . $e->getMessage());
        }
    }

    /**
     * generate the folder structure for reST documentation
     */
    protected function generateDocumentationFiles()
    {
        $this->mkdir_deep($this->extensionDirectory, 'Documentation.tmpl');
        $docFiles = array();
        $docFiles = GeneralUtility::getAllFilesAndFoldersInPath(
            $docFiles,
            ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/CodeTemplates/Extbase/Documentation.tmpl/',
            '',
            true,
            5,
            '.*(rstt|ymlt)'
        );
        foreach ($docFiles as $docFile) {
            if (is_dir($docFile)) {
                $this->mkdir_deep(
                    $this->extensionDirectory,
                    'Documentation.tmpl/' . str_replace($this->codeTemplateRootPath . 'Documentation.tmpl/', '', $docFile)
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
        $fileContents = $this->renderTemplate('Documentation.tmpl/Index.rstt', array('extension' => $this->extension));
        $this->writeFile($this->extensionDirectory . 'Documentation.tmpl/Index.rst', $fileContents);
        $fileContents = $this->renderTemplate('Documentation.tmpl/Settings.ymlt', array('extension' => $this->extension));
        $this->writeFile($this->extensionDirectory . 'Documentation.tmpl/Settings.yml', $fileContents);
    }

    /**
     * Render a template with variables
     *
     * @param string $filePath
     * @param array $variables
     */
    public function renderTemplate($filePath, $variables)
    {
        $variables['settings'] = $this->settings;
        /* @var \TYPO3\CMS\Fluid\View\StandaloneView $standAloneView */
        $standAloneView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $standAloneView->setLayoutRootPaths(array($this->codeTemplateRootPath));
        $standAloneView->setPartialRootPaths(array($this->codeTemplateRootPath . 'Partials'));
        $standAloneView->setFormat('txt');
        $templatePathAndFilename = $this->codeTemplateRootPath . $filePath;
        $standAloneView->setTemplatePathAndFilename($templatePathAndFilename);
        $standAloneView->assignMultiple($variables);
        $renderedContent = $standAloneView->render();
        // remove all double empty lines (coming from fluid)
        return preg_replace('/^\\s*\\n[\\t ]*$/m', '', $renderedContent);
    }

    /**
     * Generates the code for the controller class
     * Either from ectionController template or from class partial
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @return string
     */
    public function generateActionControllerCode(
        \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        $controllerTemplateFilePath = $this->codeTemplateRootPath . 'Classes/Controller/Controller.phpt';
        $existingClassFileObject = null;
        if ($this->roundTripEnabled) {
            $existingClassFileObject = $this->roundTripService->getControllerClassFile($domainObject);
        }
        $controllerClassFileObject = $this->classBuilder->generateControllerClassFileObject(
            $domainObject,
            $controllerTemplateFilePath,
            $existingClassFileObject
        );
        // returns a class object if an existing class was found
        if ($controllerClassFileObject) {
            $this->addLicenseHeader($controllerClassFileObject->getFirstClass());
            return $this->printerService->renderFileObject($controllerClassFileObject, true);
        } else {
            throw new \Exception('Class file for controller could not be generated');
        }
    }

    /**
     * Generates the code for the domain model class
     * Either from domainObject template or from class partial
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @return string
     */
    public function generateDomainObjectCode(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        $modelTemplateClassPath = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';
        $existingClassFileObject = null;
        if ($this->roundTripEnabled) {
            $existingClassFileObject = $this->roundTripService->getDomainModelClassFile($domainObject);
        }
        $modelClassFileObject = $this->classBuilder->generateModelClassFileObject($domainObject, $modelTemplateClassPath, $existingClassFileObject);
        if ($modelClassFileObject) {
            $this->addLicenseHeader($modelClassFileObject->getFirstClass());
            return $this->printerService->renderFileObject($modelClassFileObject, true);
        } else {
            throw new \Exception('Class file for domain object could not be generated');
        }
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
     * @param string $nameSpaceName
     * @param string $classDocComment
     * @return string
     */
    protected function renderClassFile($classObject)
    {
        $nameSpace = new \EBT\ExtensionBuilder\Domain\Model\NamespaceObject($classObject->getNamespaceName());
        $this->addLicenseHeader($classObject);
        $nameSpace->addClass($classObject);
        $classFile = new \EBT\ExtensionBuilder\Domain\Model\File;
        $classFile->addNamespace($nameSpace);
        return $this->printerService->renderFileObject($classFile, true);
    }

    /**
     * Generates the code for the repository class
     * Either from domainRepository template or from class partial
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return string
     */
    public function generateDomainRepositoryCode(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        $repositoryTemplateClassPath = $this->codeTemplateRootPath . 'Classes/Domain/Repository/Repository.phpt';
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
            return $this->printerService->renderFileObject($repositoryClassFileObject, true);
        } else {
            throw new \Exception('Class file for repository could not be generated');
        }
    }

    /**
     * Generate the tests for a model
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return string
     */
    public function generateDomainModelTests(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        return $this->renderTemplate(
            'Tests/DomainModelTest.phpt',
            array('extension' => $this->extension, 'domainObject' => $domainObject)
        );
    }

    /**
     * Generate the tests for a CRUD-enabled controller
     *
     * @param array $extensionProperties
     * @param string $controllerName
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     *
     * @return string
     */
    public function generateControllerTests(
        $controllerName,
        \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        return $this->renderTemplate(
            'Tests/ControllerTest.phpt',
            array(
                'extension' => $this->extension,
                'controllerName' => $controllerName,
                'domainObject' => $domainObject
            )
        );
    }

    /**
     * generate a docComment for class files. Add a license header if none found
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
     *
     * @return void;
     */
    protected function addLicenseHeader($classObject)
    {
        $comments = $classObject->getComments();
        $needsLicenseHeader = true;
        foreach ($comments as $comment) {
            if (strpos($comment, 'license information') !== false) {
                $needsLicenseHeader = false;
            }
        }
        $extensionSettings = $this->extension->getSettings();
        if ($needsLicenseHeader && empty($extensionSettings['skipDocComment'])) {
            $licenseHeader = $this->renderTemplate(
                'Partials/Classes/licenseHeader.phpt',
                array('extension' => $this->extension)
            );
            $classObject->addComment($licenseHeader);
        }
    }

    /**
     * Generates the content of an Action template
     * For some Actions default templates are provided,
     * other Action templates will just be created emtpy
     *
     * @param string $templateRootFolder
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\Action $action
     * @return string The generated Template code (might be empty)
     */
    public function generateDomainTemplate(
        $templateRootFolder,
        \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject,
        \EBT\ExtensionBuilder\Domain\Model\DomainObject\Action $action)
    {
        return $this->renderTemplate(
            $templateRootFolder . $action->getName() . '.htmlt',
            array('domainObject' => $domainObject, 'action' => $action, 'extension' => $this->extension));
    }

    public function generateDomainFormFieldsPartial(
        $templateRootFolder,
        \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        return $this->renderTemplate(
            $templateRootFolder . 'formFields.htmlt',
            array('extension' => $this->extension, 'domainObject' => $domainObject)
        );
    }

    public function generateDomainPropertiesPartial(
        $templateRootFolder,
        \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        return $this->renderTemplate(
            $templateRootFolder . 'properties.htmlt',
            array('extension' => $this->extension, 'domainObject' => $domainObject)
        );
    }

    public function generateFormErrorsPartial($templateRootFolder)
    {
        return $this->renderTemplate($templateRootFolder . 'formErrors.htmlt', array('extension' => $this->extension));
    }

    public function generateLayout($templateRootFolder)
    {
        return $this->renderTemplate($templateRootFolder . 'default.htmlt', array('extension' => $this->extension));
    }

    /**
     * @param string $fileNameSuffix (_db, _csh, _mod)
     * @param string $variableName
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $variable
     * @return mixed
     */
    protected function generateLocallangFileContent($fileNameSuffix = '', $variableName = '', $variable = null)
    {
        $targetFile = 'Resources/Private/Language/locallang' . $fileNameSuffix;

        $variableArray = array('extension' => $this->extension);
        if (strlen($variableName) > 0) {
            $variableArray[$variableName] = $variable;
        }
        $languageLabels = array();
        if ($variableName == 'domainObject') {
            $languageLabels = $this->localizationService->prepareLabelArrayForContextHelp($variable);
        } elseif ($variableName == 'backendModule') {
            $languageLabels = $this->localizationService->prepareLabelArrayForBackendModule($variable);
        } else {
            $languageLabels = $this->localizationService->prepareLabelArray($this->extension, 'locallang' . $fileNameSuffix);
        }

        if ($this->fileShouldBeMerged($targetFile . '.xlf')) {
            $existingFile = null;
            $filenameToLookFor = $this->extensionDirectory . $targetFile;
            if ($variableName == 'domainObject') {
                $filenameToLookFor .= '_' . $variable->getDatabaseTableName();
            }
            $existingFile = $filenameToLookFor . '.xlf';

            if (@file_exists($existingFile)) {
                $existingLabels = $this->localizationService->getLabelArrayFromFile($existingFile, 'default');
                GeneralUtility::devLog(
                    'locallang' . $fileNameSuffix . ' existing labels',
                    'extension_builder',
                    1,
                    $existingLabels
                );
                if (is_array($existingLabels)) {
                    ArrayUtility::mergeRecursiveWithOverrule($languageLabels, $existingLabels);
                }
            }
        }
        if (empty($languageLabels)) {
            return '';
        }
        $variableArray['labelArray'] = $languageLabels;
        return $this->renderTemplate('Resources/Private/Language/locallang.xlf' . 't', $variableArray);
    }

    public function generatePrivateResourcesHtaccess()
    {
        return $this->renderTemplate('Resources/Private/htaccess.t', array());
    }

    public function generateTCA(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
        return $this->renderTemplate(
            'Configuration/TCA/tableName.phpt',
            array(
                'extension' => $this->extension,
                'domainObject' => $domainObject
            )
        );
    }

    /**
     * Overrides are needed for single table inheritance
     *
     * @param array $domainObjects
     * @param $addRecordTypeField
     * @return mixed
     */
    public function generateTCAOverride(array $domainObjects, $addRecordTypeField)
    {
        return $this->renderTemplate(
            'Configuration/TCA/Overrides/tableName.phpt',
            array(
                'extension' => $this->extension,
                'rootDomainObject' => reset($domainObjects),
                'domainObjects' => $domainObjects,
                'addRecordTypeField' => $addRecordTypeField
            )
        );
    }

    public function generateYamlSettings()
    {
        return $this->renderTemplate(
            'Configuration/ExtensionBuilder/settings.yamlt',
            array('extension' => $this->extension)
        );
    }

    public function generateTyposcriptSetup()
    {
        return $this->renderTemplate('Configuration/TypoScript/setup.tst', array('extension' => $this->extension));
    }

    public function generateTyposcriptConstants()
    {
        return $this->renderTemplate('Configuration/TypoScript/constants.tst', array('extension' => $this->extension));
    }

    public function generateStaticTyposcript()
    {
        return $this->renderTemplate('ext_typoscript_setup.txtt', array('extension' => $this->extension));
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
     * @param string $classType
     * @param string $methodType (used for add, get set etc.)
     * @param string $methodName (used for concrete methods like createAction, initialze etc.)
     * @return string method body
     */
    public function getDefaultMethodBody($domainObject, $domainProperty, $classType, $methodType, $methodName)
    {
        if ($classType == 'Controller' && !in_array($methodName, self::$defaultActions)) {
            return '';
        }
        if (!empty($methodType) && empty($methodName)) {
            $methodName = $methodType;
        }

        $variables = array(
            'domainObject' => $domainObject,
            'property' => $domainProperty,
            'extension' => $this->extension,
            'settings' => $this->settings
        );

        $methodBody = $this->renderTemplate(
            'Partials/Classes/' . $classType . '/Methods/' . $methodName . 'MethodBody.phpt',
            $variables
        );
        return $methodBody;
    }

    /**
     *
     * @param string $extensionDirectory
     * @param string $classType
     * @return string
     */
    public static function getFolderForClassFile($extensionDirectory, $classType, $createDirIfNotExist = true)
    {
        $classPath = '';
        switch ($classType) {
            case 'Model'        :
                $classPath = 'Classes/Domain/Model/';
                break;

            case 'Controller'    :
                $classPath = 'Classes/Controller/';
                break;

            case 'Repository'    :
                $classPath = 'Classes/Domain/Repository/';
                break;
        }
        if (!empty($classPath)) {
            if (!is_dir($extensionDirectory . $classPath) && $createDirIfNotExist) {
                GeneralUtility::mkdir_deep($extensionDirectory, $classPath);
            }
            if (!is_dir($extensionDirectory . $classPath) && $createDirIfNotExist) {
                throw new \Exception('folder could not be created:' . $extensionDirectory . $classPath);
            }
            return $extensionDirectory . $classPath;
        } else {
            throw new \Exception('Unexpected classPath:' . $classPath);
        }
    }

    /**
     * wrapper for GeneralUtility::writeFile
     * checks for overwrite settings
     *
     * path and filename of the targetFile, relative to extension dir:
     * @param string $targetFile
     * @param string $fileContents
     * @throws \Exception
     *
     * @return void
     */
    protected function writeFile($targetFile, $fileContents)
    {
        if ($this->roundTripEnabled) {
            $overWriteMode = RoundTrip::getOverWriteSettingForPath(
                $targetFile,
                $this->extension
            );
            if ($overWriteMode == -1) {
                // skip file creation
                return;
            }
            if ($overWriteMode == 1 && strpos($targetFile, 'Classes') === false) {
                // classes are merged by the class builder
                $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                if ($fileExtension == 'html') {
                    //TODO: We need some kind of protocol to be displayed after code generation
                    GeneralUtility::devLog(
                        'File ' . basename($targetFile) . ' was not written. Template files can\'t be merged!',
                        'extension_builder',
                        1
                    );
                    return;
                } elseif (in_array($fileExtension, $this->filesSupportingSplitToken)) {
                    $fileContents = $this->insertSplitToken($targetFile, $fileContents);
                }
            } elseif (file_exists($targetFile) && $overWriteMode == 2) {
                // keep the existing file
                return;
            }
        }

        if (empty($fileContents)) {
            GeneralUtility::devLog(
                'No file content! File ' . $targetFile . ' had no content',
                'extension_builder',
                0,
                $this->settings
            );
            return;
        }
        $success = GeneralUtility::writeFile($targetFile, $fileContents);
        if (!$success) {
            throw new \Exception('File ' . $targetFile . ' could not be created!');
        }
    }

    /**
     * @param $destinationFile
     * @return bool
     */
    protected function fileShouldBeMerged($destinationFile)
    {
        $overwriteSettings = RoundTrip::getOverWriteSettingForPath($destinationFile, $this->extension);
        if ($this->roundTripEnabled && $overwriteSettings > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Inserts the token into the file content
     * and preserves everything below the token
     *
     * @param $targetFile
     * @param $fileContents
     * @return mixed|string
     */
    protected function insertSplitToken($targetFile, $fileContents)
    {
        $customFileContent = '';
        if (file_exists($targetFile)) {

            // merge the files means append everything behind the split token
            $existingFileContent = file_get_contents($targetFile);

            $fileParts = explode(RoundTrip::SPLIT_TOKEN, $existingFileContent);
            if (count($fileParts) == 2) {
                $customFileContent = str_replace('?>', '', $fileParts[1]);
            }
        }

        $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($fileExtension == 'php') {
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
     * @param string $targetFile the path and filename of the targetFile
     * @param string $fileContents
     */
    protected function upload_copy_move($sourceFile, $targetFile)
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
     */
    protected function mkdir_deep($directory, $deepDirectory)
    {
        if (!$this->roundTripEnabled) {
            GeneralUtility::mkdir_deep($directory, $deepDirectory);
        } else {
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
                    GeneralUtility::mkdir_deep($tmpBasePath, $subDirectory);
                }
                $tmpBasePath .= $subDirectory . '/';
            }
        }
    }
}
