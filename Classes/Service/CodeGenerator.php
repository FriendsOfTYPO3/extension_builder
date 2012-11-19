<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Ingmar Schlecht
 *  (c) 2010 Nico de Haen
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
 * Creates (or updates) all the required files for an extension
 */
class Tx_ExtensionBuilder_Service_CodeGenerator implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var Tx_ExtensionBuilder_Service_ClassBuilder
	 */
	protected $classBuilder;

	/**
	 * @var string
	 */
	protected $codeTemplateRootPath;

	/**
	 * @var Tx_ExtensionBuilder_Domain_Model_Extension
	 */
	protected $extension;

	/**
	 * @var string
	 */
	protected $extensionDirectory;

	/**
	 * @var TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var array
	 */
	protected $overWriteSettings;

	/**
	 * @var boolean
	 */
	protected $roundTripEnabled = FALSE;

	/**
	 * @var array Settings
	 */
	protected $settings;

	/**
	 * @var TYPO3\CMS\Fluid\Core\Parser\TemplateParser
	 */
	protected $templateParser;

	static public $defaultActions = array(
		'createAction',
		'deleteAction',
		'editAction',
		'listAction',
		'newAction',
		'showAction',
		'updateAction'
	);

	/**
	 * alle file types where a split token makes sense
	 * @var array
	 */
	protected $filesSupportingSplitToken = array(
		'php', //ext_tables, tca, localconf
		'sql',
		'txt' // Typoscript
	);

	/**
	 * @var string
	 */
	protected $locallangFileFormat = 'xlf';

	/**
	 * @param Tx_ExtensionBuilder_Service_ClassBuilder $classBuilder
	 */
	public function injectClassBuilder(Tx_ExtensionBuilder_Service_ClassBuilder $classBuilder) {
		$this->classBuilder = $classBuilder;
	}

	/**
	 * @param TYPO3\CMS\Extbase\Object\ObjectManagerInterface  $objectManager
	 */
	public function injectObjectManager(TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param TYPO3\CMS\Fluid\Core\Parser\TemplateParser $templateParser
	 */
	public function injectTemplateParser(TYPO3\CMS\Fluid\Core\Parser\TemplateParser $templateParser) {
		$this->templateParser = $templateParser;
	}

	/**
	 * called by controller
	 * @param Array $settings
	 */
	public function setSettings($settings) {
		$this->settings = $settings;
	}


	/**
	 * The entry point to the class
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 */
	public function build(Tx_ExtensionBuilder_Domain_Model_Extension $extension) {
		$this->extension = $extension;
		if ($this->settings['extConf']['enableRoundtrip'] == 1) {
			$this->roundTripEnabled = TRUE;
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('roundtrip enabled', 'extension_builder', 0, $this->settings);
		}
		else {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('roundtrip disabled', 'extension_builder', 0, $this->settings);
		}
		if (isset($this->settings['codeTemplateRootPath'])) {
			$this->codeTemplateRootPath = $this->settings['codeTemplateRootPath'];
		} else {
			throw new Exception('No codeTemplateRootPath configured');
		}

		// Base directory already exists at this point
		$this->extensionDirectory = $this->extension->getExtensionDir();
		if (!is_dir($this->extensionDirectory)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir($this->extensionDirectory);
		}

		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extensionDirectory, 'Configuration');

		$this->configurationDirectory = $this->extensionDirectory . 'Configuration/';

		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extensionDirectory, 'Resources/Private');

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

		$this->generateDocumentationFiles();
	}

	protected function generateYamlSettingsFile() {

		if (!file_exists($this->configurationDirectory . 'ExtensionBuilder/settings.yaml')) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir($this->configurationDirectory . 'ExtensionBuilder');
			$fileContents = $this->generateYamlSettings();
			$targetFile = $this->configurationDirectory . 'ExtensionBuilder/settings.yaml';
			\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($targetFile, $fileContents);
		}

	}

	protected function generateExtensionFiles() {
		// Generate ext_emconf.php, ext_tables.* and TCA definition
		$extensionFiles = array('ext_emconf.php', 'ext_tables.php', 'ext_tables.sql');
		foreach ($extensionFiles as $extensionFile) {
			try {
				$fileContents = $this->renderTemplate(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase($extensionFile) . 't', array('extension' => $this->extension, 'locallangFileFormat' => $this->locallangFileFormat));
				$this->writeFile($this->extensionDirectory . $extensionFile, $fileContents);
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Generated ' . $extensionFile, 'extension_builder', 0, array('Content' => $fileContents));
			}
			catch (Exception $e) {
				throw new Exception('Could not write ' . $extensionFile . ', error: ' . $e->getMessage());
			}
		}

	}

	protected function generatePluginFiles() {
		if ($this->extension->getPlugins()) {
			try {
				$fileContents = $this->renderTemplate(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToLowerCamelCase('ext_localconf.phpt'), array('extension' => $this->extension));
				$this->writeFile($this->extensionDirectory . 'ext_localconf.php', $fileContents);
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Generated ext_localconf.php', 'extension_builder', 0, array('Content' => $fileContents));
			}
			catch (Exception $e) {
				throw new Exception('Could not write ext_localconf.php. Error: ' . $e->getMessage());
			}
			try {
				$currentPluginKey = '';
				foreach ($this->extension->getPlugins() as $plugin) {
					if ($plugin->getSwitchableControllerActions()) {
						if (!is_dir($this->extensionDirectory . 'Configuration/FlexForms')) {
							$this->mkdir_deep($this->extensionDirectory, 'Configuration/FlexForms');
						}
						$currentPluginKey = $plugin->getKey();
						$fileContents = $this->renderTemplate('Configuration/Flexforms/flexform.xmlt', array('plugin' => $plugin));
						$this->writeFile($this->extensionDirectory . 'Configuration/FlexForms/flexform_' . $currentPluginKey . '.xml', $fileContents);
						\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Generated flexform_' . $currentPluginKey . '.xml', 'extension_builder', 0, array('Content' => $fileContents));
					}
				}
			}
			catch (Exception $e) {
				throw new Exception('Could not write  flexform_' . $currentPluginKey . '.xml. Error: ' . $e->getMessage());
			}
		}
	}

	protected function generateTCAFiles() {
		// Generate TCA
		try {
			\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extensionDirectory, 'Configuration/TCA');

			$domainObjects = $this->extension->getDomainObjects();

			foreach ($domainObjects as $domainObject) {
				$fileContents = $this->generateTCA($domainObject);
				$this->writeFile($this->configurationDirectory . 'TCA/' . $domainObject->getName() . '.php', $fileContents);
			}

		} catch (Exception $e) {
			throw new Exception('Could not generate Tca.php, error: ' . $e->getMessage() . $e->getFile());
		}
	}

	protected function generateLocallangFiles() {
		// Generate locallang*.xml files
		try {
			\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->privateResourcesDirectory, 'Language');
			$this->languageDirectory = $this->privateResourcesDirectory . 'Language/';
			$fileContents = $this->generateLocallangFileContent();
			$this->writeFile($this->languageDirectory . 'locallang.' . $this->locallangFileFormat, $fileContents);
			$fileContents = $this->generateLocallangFileContent('_db');
			$this->writeFile($this->languageDirectory . 'locallang_db.' . $this->locallangFileFormat, $fileContents);
			if ($this->extension->hasBackendModules()) {
				foreach ($this->extension->getBackendModules() as $backendModule) {
					$fileContents = $this->generateLocallangFileContent('_mod', 'backendModule', $backendModule);
					$this->writeFile($this->languageDirectory . 'locallang_' . $backendModule->getKey() . '.' . $this->locallangFileFormat, $fileContents);
				}

			}
			foreach ($this->extension->getDomainObjects() as $domainObject) {
				$fileContents = $this->generateLocallangFileContent('_csh', 'domainObject', $domainObject);
				$this->writeFile($this->languageDirectory . 'locallang_csh_' . $domainObject->getDatabaseTableName() . '.' . $this->locallangFileFormat, $fileContents);

			}
		} catch (Exception $e) {
			throw new Exception('Could not generate locallang files, error: ' . $e->getMessage());
		}
	}

	protected function generateTemplateFiles($templateSubFolder = '') {
		$templateRootFolder = 'Resources/Private/' . $templateSubFolder;
		$absoluteTemplateRootFolder = $this->extensionDirectory . $templateRootFolder;

		$hasTemplates = FALSE;
		//$actionsUsingFormFieldsPartial = array('edit', 'new');
		//$actionsUsingPropertiesPartial = array('show');
		foreach ($this->extension->getDomainObjects() as $domainObject) {
			// Do not generate anyting if $domainObject is not an Entity or has no actions defined
			if (!$domainObject->getEntity() || (count($domainObject->getActions()) == 0)) {
				continue;
			}
			$domainTemplateDirectory = $absoluteTemplateRootFolder . 'Templates/' . $domainObject->getName() . '/';
			foreach ($domainObject->getActions() as $action) {
				if ($action->getNeedsTemplate()
						&& file_exists($this->codeTemplateRootPath . $templateRootFolder . 'Templates/' . $action->getName() . '.htmlt')

				) {
					$hasTemplates = TRUE;
					$this->mkdir_deep($this->extensionDirectory, $templateRootFolder . 'Templates/' . $domainObject->getName());
					$fileContents = $this->generateDomainTemplate($templateRootFolder . 'Templates/', $domainObject, $action);
					$this->writeFile($domainTemplateDirectory . ucfirst($action->getName()) . '.html', $fileContents);
					// generate partials for formfields
					if ($action->getNeedsForm()) {
						$this->mkdir_deep($absoluteTemplateRootFolder, 'Partials');
						$partialDirectory = $absoluteTemplateRootFolder . 'Partials/';
						$this->mkdir_deep($partialDirectory, $domainObject->getName());
						$formfieldsPartial = $partialDirectory . $domainObject->getName() . '/FormFields.html';
						$fileContents = $this->generateDomainFormFieldsPartial($templateRootFolder . 'Partials/', $domainObject);
						$this->writeFile($formfieldsPartial, $fileContents);
						if (!file_exists($partialDirectory . 'FormErrors.html')) {
							$this->writeFile($partialDirectory . 'FormErrors.html', $this->generateFormErrorsPartial($templateRootFolder . 'Partials/'));
						}
					}
					// generate partials for properties
					if ($action->getNeedsPropertyPartial()) {
						$this->mkdir_deep($absoluteTemplateRootFolder, 'Partials');
						$partialDirectory = $absoluteTemplateRootFolder . 'Partials/';
						$this->mkdir_deep($partialDirectory, $domainObject->getName());
						$propertiesPartial = $partialDirectory . $domainObject->getName() . '/Properties.html';
						$fileContents = $this->generateDomainPropertiesPartial($templateRootFolder . 'Partials/', $domainObject);
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

	protected function generateTyposcriptFiles() {
		if ($this->extension->hasPlugins() || $this->extension->hasBackendModules()) {
			// Generate TypoScript setup
			try {
				$this->mkdir_deep($this->extensionDirectory, 'Configuration/TypoScript');
				$typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
				$fileContents = $this->generateTyposcriptSetup();
				$this->writeFile($typoscriptDirectory . 'setup.txt', $fileContents);
			} catch (Exception $e) {
				throw new Exception('Could not generate typoscript setup, error: ' . $e->getMessage());
			}

			// Generate TypoScript constants
			try {
				$typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
				$fileContents = $this->generateTyposcriptConstants();
				$this->writeFile($typoscriptDirectory . 'constants.txt', $fileContents);
			} catch (Exception $e) {
				throw new Exception('Could not generate typoscript constants, error: ' . $e->getMessage());
			}
		}

		// Generate Static TypoScript
		try {
			if ($this->extension->getDomainObjectsThatNeedMappingStatements()) {
				$fileContents = $this->generateStaticTyposcript();
				$this->writeFile($this->extensionDirectory . 'ext_typoscript_setup.txt', $fileContents);
			}
		} catch (Exception $e) {
			throw new Exception('Could not generate static typoscript, error: ' . $e->getMessage());
		}
	}

	protected function generateDomainObjectRelatedFiles() {

		if (count($this->extension->getDomainObjects()) > 0) {
			$this->classBuilder->initialize($this, $this->extension, $this->roundTripEnabled);
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
					$destinationFile = $domainModelDirectory . $domainObject->getName() . '.php';
					if ($this->roundTripEnabled && Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($destinationFile, $this->extension) > 0) {
						$mergeWithExistingClass = TRUE;
					} else {
						$mergeWithExistingClass = FALSE;
					}
					$fileContents = $this->generateDomainObjectCode($domainObject, $mergeWithExistingClass);
					$this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
					\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Generated ' . $domainObject->getName() . '.php', 'extension_builder', 0);
					$this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);

					if ($domainObject->isAggregateRoot()) {
						$iconFileName = 'aggregate_root.gif';
					} elseif ($domainObject->isEntity()) {
						$iconFileName = 'entity.gif';
					} else {
						$iconFileName = 'value_object.gif';
					}
					$this->upload_copy_move(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/Icons/' . $iconFileName, $this->iconsDirectory . $domainObject->getDatabaseTableName() . '.gif');

					if ($domainObject->isAggregateRoot()) {
						$destinationFile = $domainRepositoryDirectory . $domainObject->getName() . 'Repository.php';
						if ($this->roundTripEnabled && Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($destinationFile, $this->extension) > 0) {
							$mergeWithExistingClass = TRUE;
						} else {
							$mergeWithExistingClass = FALSE;
						}
						$fileContents = $this->generateDomainRepositoryCode($domainObject, $mergeWithExistingClass);
						$this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
						\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Generated ' . $domainObject->getName() . 'Repository.php', 'extension_builder', 0);
						$this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);
					}

					// Generate basic UnitTests
					$fileContents = $this->generateDomainModelTests($domainObject);
					$this->writeFile($domainModelTestsDirectory . $domainObject->getName() . 'Test.php', $fileContents);
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
					if ($this->roundTripEnabled && Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($destinationFile, $this->extension) > 0) {
						$mergeWithExistingClass = TRUE;
					} else {
						$mergeWithExistingClass = FALSE;
					}
					$fileContents = $this->generateActionControllerCode($domainObject, $mergeWithExistingClass);
					$this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
					\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Generated ' . $domainObject->getName() . 'Controller.php', 'extension_builder', 0);
					$this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);

					// Generate basic UnitTests
					$fileContents = $this->generateControllerTests($domainObject->getName() . 'Controller', $domainObject);
					$this->writeFile($crudEnabledControllerTestsDirectory . $domainObject->getName() . 'ControllerTest.php', $fileContents);
				}
			} catch (Exception $e) {
				throw new Exception('Could not generate action controller, error: ' . $e->getMessage());
			}

			// Generate Domain Templates
			try {
				if ($this->extension->getPlugins()) {
					$this->generateTemplateFiles();
				}
				if ($this->extension->getBackendModules()) {
					$this->generateTemplateFiles('Backend/');
				}
			} catch (Exception $e) {
				throw new Exception('Could not generate domain templates, error: ' . $e->getMessage());
			}

			try {
				$settings = $this->extension->getSettings();
				if (isset($settings['createAutoloadRegistry']) && $settings['createAutoloadRegistry'] == TRUE) {
					//\TYPO3\CMS\Extbase\Utility\Extension::createAutoloadRegistryForExtension($this->extension->getExtensionKey(), $this->extensionDirectory);
				}
			} catch (Exception $e) {
				throw new Exception('Could not generate ext_autoload.php, error: ' . $e->getMessage());
			}


		}
		else {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('No domainObjects in this extension', 'extension_builder', 3, (array)$this->extension);
		}
	}

	protected function generateHtaccessFile() {
		// Generate Private Resources .htaccess
		try {
			$fileContents = $this->generatePrivateResourcesHtaccess();
			$this->writeFile($this->privateResourcesDirectory . '.htaccess', $fileContents);
		} catch (Exception $e) {
			throw new Exception('Could not create private resources folder, error: ' . $e->getMessage());
		}
	}

	protected function copyStaticFiles() {
		try {
			$this->upload_copy_move(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/Icons/ext_icon.gif', $this->extensionDirectory . 'ext_icon.gif');
		} catch (Exception $e) {
			throw new Exception('Could not copy ext_icon.gif, error: ' . $e->getMessage());
		}

		// insert a manual template
		try {
			if (!file_exists($this->extensionDirectory . 'doc/manual.sxw') && file_exists($this->codeTemplateRootPath . 'doc/manual.sxw')) {
				$this->mkdir_deep($this->extensionDirectory, 'doc');
				$this->upload_copy_move($this->codeTemplateRootPath . 'doc/manual.sxw', $this->extensionDirectory . 'doc/manual.sxw');
			}
		} catch (Exception $e) {
			throw new Exception('An error occurred when copying the manual template: ' . $e->getMessage() . $e->getFile());
		}

		try {
			$this->mkdir_deep($this->extensionDirectory, 'Resources/Public');
			$publicResourcesDirectory = $this->extensionDirectory . 'Resources/Public/';
			$this->mkdir_deep($publicResourcesDirectory, 'Icons');
			$this->iconsDirectory = $publicResourcesDirectory . 'Icons/';
			$this->upload_copy_move(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/Icons/relation.gif', $this->iconsDirectory . 'relation.gif');
		} catch (Exception $e) {
			throw new Exception('Could not create public resources folder, error: ' . $e->getMessage());
		}


	}

	/**
	 * generate the folder structure for reST documentation
	 */
	protected function generateDocumentationFiles() {
		$this->mkdir_deep($this->extensionDirectory, 'Documentation');
		$docFiles = array();
		$docFiles = \TYPO3\CMS\Core\Utility\GeneralUtility::getAllFilesAndFoldersInPath($docFiles,\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Resources/Private/CodeTemplates/Extbase/Documentation/', '', TRUE, 5, '/.*rstt/');
		foreach($docFiles as $docFile) {
			if(is_dir($docFile)) {
				$this->mkdir_deep($this->extensionDirectory, 'Documentation/' . str_replace($this->codeTemplateRootPath . 'Documentation/','',$docFile));
			} else if(strpos($docFile,'.rstt') === FALSE) {
				$this->upload_copy_move($docFile, str_replace(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder').'Resources/Private/CodeTemplates/Extbase/', $this->extensionDirectory, $docFile));
			}
		}
		$this->upload_copy_move(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder').'Resources/Private/CodeTemplates/Extbase/Readme.rst', $this->extensionDirectory . 'Readme.rst');
		$fileContents = $this->renderTemplate('Documentation/Index.rstt', array('extension' => $this->extension));
		$this->writeFile($this->extensionDirectory . 'Documentation/Index.rst', $fileContents);

	}


	/**
	 * Build the rendering context
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	protected function buildRenderingContext($templateVariables) {
		$templateVariables['settings'] = $this->settings;
		$variableContainer = $this->objectManager->create('Tx_Fluid_Core_ViewHelper_TemplateVariableContainer', $templateVariables);

		$renderingContext = $this->objectManager->create('Tx_Fluid_Core_Rendering_RenderingContext');
		$viewHelperVariableContainer = $this->objectManager->create('Tx_Fluid_Core_ViewHelper_ViewHelperVariableContainer');
		if (method_exists($renderingContext, 'setTemplateVariableContainer')) {
			$renderingContext->setTemplateVariableContainer($variableContainer);
			$renderingContext->setViewHelperVariableContainer($viewHelperVariableContainer);
		} else {
			$renderingContext->injectTemplateVariableContainer($variableContainer);
			$renderingContext->injectViewHelperVariableContainer($viewHelperVariableContainer);
		}
		return $renderingContext;
	}

	/**
	 * Render a template with variables
	 *
	 * @param string $filePath
	 * @param array $variables
	 */
	public function renderTemplate($filePath, $variables) {
		//$codeTemplateRootPath = $this->getCodeTemplateRootPath();
		$variables['settings'] = $this->settings;
		//$variables['settings']['codeTemplateRootPath'] = $this->codeTemplateRootPath;
		if (!is_file($this->codeTemplateRootPath . $filePath)) {
			throw(new Exception('TemplateFile ' . $this->codeTemplateRootPath . $filePath . ' not found'));
		}
		$templateCode = file_get_contents($this->codeTemplateRootPath . $filePath);
		if (empty($templateCode)) {
			throw(new Exception('TemplateFile ' . $this->codeTemplateRootPath . $filePath . ' has no content'));
		}
		$parsedTemplate = $this->templateParser->parse($templateCode);

		$renderedContent = trim($parsedTemplate->render($this->buildRenderingContext($variables)));
		// remove all double empty lines (coming from fluid)
		return preg_replace('/^\s*\n[\t ]*$/m', '', $renderedContent);

	}


	/**
	 * Generates the code for the controller class
	 * Either from ectionController template or from class partial
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 */
	public function generateActionControllerCode(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject, $mergeWithExistingClass) {
		$controllerClassObject = $this->classBuilder->generateControllerClassObject($domainObject, $mergeWithExistingClass);
		// returns a class object if an existing class was found
		if ($controllerClassObject) {
			$classDocComment = $this->renderDocComment($controllerClassObject, $domainObject);
			$controllerClassObject->setDocComment($classDocComment);

			return $this->renderTemplate('Classes/class.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension, 'classObject' => $controllerClassObject));
		} else {
			throw new Exception('Class file for controller could not be generated');
		}
	}

	/**
	 * Generates the code for the domain model class
	 * Either from domainObject template or from class partial
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 */
	public function generateDomainObjectCode(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject, $mergeWithExistingClass) {
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject, $mergeWithExistingClass);
		t3lib_div::devlog('Namespace: ' . $modelClassObject->getNameSpace(),'extension_builder');
		if ($modelClassObject) {
			$classDocComment = $this->renderDocComment($modelClassObject, $domainObject);
			$modelClassObject->setDocComment($classDocComment);
			return $this->renderTemplate('Classes/class.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension, 'classObject' => $modelClassObject));
		} else {
			throw new Exception('Class file for domain object could not be generated');
		}

	}

	/**
	 * Generates the code for the repository class
	 * Either from domainRepository template or from class partial
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param boolean $mergeWithExistingClass
	 */
	public function generateDomainRepositoryCode(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject, $mergeWithExistingClass) {
		$repositoryClassObject = $this->classBuilder->generateRepositoryClassObject($domainObject, $mergeWithExistingClass);
		if ($repositoryClassObject) {
			$classDocComment = $this->renderDocComment($repositoryClassObject, $domainObject);
			$repositoryClassObject->setDocComment($classDocComment);

			return $this->renderTemplate('Classes/class.phpt', array('domainObject' => $domainObject, 'classObject' => $repositoryClassObject));
		} else {
			throw new Exception('Class file for repository could not be generated');
		}
	}

	/**
	 * Generate the tests for a model
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 *
	 * @return string
	 */
	public function generateDomainModelTests(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Tests/DomainModelTest.phpt', array('extension' => $this->extension, 'domainObject' => $domainObject));
	}

	/**
	 * Generate the tests for a CRUD-enabled controller
	 *
	 * @param array $extensionProperties
	 * @param string $controllerName
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 *
	 * @return string
	 */
	public function generateControllerTests($controllerName, Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Tests/ControllerTest.phpt', array('extension' => $this->extension, 'controllerName' => $controllerName, 'domainObject' => $domainObject));
	}

	/**
	 * generate a docComment for class files. Add a license haeder if none found
	 * @param unknown_type $classObject
	 * @param unknown_type $domainObject
	 */
	protected function renderDocComment($classObject, $domainObject) {
		if (!$classObject->hasDocComment()) {
			$docComment = $this->renderTemplate('Partials/Classes/classDocComment.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension, 'classObject' => $classObject));
		}
		else {
			$docComment = $classObject->getDocComment();
		}
		$precedingBlock = $classObject->getPrecedingBlock();

		if (empty($precedingBlock) || strpos($precedingBlock, 'GNU General Public License') < 1) {

			$licenseHeader = $this->renderTemplate('Partials/Classes/licenseHeader.phpt', array('persons' => $this->extension->getPersons()));
			$docComment = "\n" . $licenseHeader . "\n\n\n" . $docComment;
		}
		else {
			$docComment = $precedingBlock . "\n" . $docComment;
		}
		return $docComment;
	}

	/**
	 * Generates the content of an Action template
	 * For some Actions default templates are provided, other Action templates will just be created emtpy
	 *
	 * @param string $templateRootFolder
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_Action $action
	 * @return string The generated Template code (might be empty)
	 */
	public function generateDomainTemplate($templateRootFolder, Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject, Tx_ExtensionBuilder_Domain_Model_DomainObject_Action $action) {
		return $this->renderTemplate($templateRootFolder . $action->getName() . '.htmlt', array('domainObject' => $domainObject, 'action' => $action, 'extension' => $this->extension));
	}

	public function generateDomainFormFieldsPartial($templateRootFolder, Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate($templateRootFolder . 'formFields.htmlt', array('extension' => $this->extension, 'domainObject' => $domainObject));
	}

	public function generateDomainPropertiesPartial($templateRootFolder, Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate($templateRootFolder . 'properties.htmlt', array('extension' => $this->extension, 'domainObject' => $domainObject));
	}

	public function generateFormErrorsPartial($templateRootFolder) {
		return $this->renderTemplate($templateRootFolder . 'formErrors.htmlt', array('extension' => $this->extension));
	}

	public function generateLayout($templateRootFolder) {
		return $this->renderTemplate($templateRootFolder . 'default.htmlt', array('extension' => $this->extension));
	}


	/**
	 * @param string $fileNameSuffix
	 * @param string $variableName
	 * @param null $variable
	 * @return mixed
	 */
	protected function generateLocallangFileContent($fileNameSuffix = '', $variableName = '', $variable = NULL) {
		$targetFile = 'Resources/Private/Language/locallang' . $fileNameSuffix;

		$variableArray = array('extension' => $this->extension);
		if (strlen($variableName) > 0) {
			$variableArray[$variableName] = $variable;
		}

		if ($this->roundTripEnabled && Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($targetFile . '.' . $this->locallangFileFormat, $this->extension) == 1) {
			$existingFile = NULL;
			$filenameToLookFor = $this->extensionDirectory . $targetFile;
			if ($variableName == 'domainObject') {
				$filenameToLookFor .= '_' . $variable->getDatabaseTableName();
			}
			if (file_exists($filenameToLookFor . '.xlf')) {
				$existingFile = $filenameToLookFor . '.xlf';
			} else if (file_exists($filenameToLookFor . '.xml')) {
				$existingFile = $filenameToLookFor . '.xml';
			}
			if ($existingFile != NULL) {
				$defaultFileContent = $this->renderTemplate($targetFile  . '.' . $this->locallangFileFormat . 't', $variableArray);
				if($this->locallangFileFormat == 'xlf') {
					throw new Exception('Merging xlf files is not yet supported. Please set overwrite settings to "keep" or "overwrite"');
					// this is prepared already but still needs some improvements
					//$labelArray = Tx_ExtensionBuilder_Utility_Tools::mergeLocallangXml($existingFile, $defaultFileContent, $this->locallangFileFormat);
					//$variableArray['labelArray'] = $labelArray;
				} else {
					return  Tx_ExtensionBuilder_Utility_Tools::mergeLocallangXml($existingFile, $defaultFileContent);
				}

			}
		}
		return $this->renderTemplate($targetFile . '.' . $this->locallangFileFormat . 't', $variableArray);
	}

	public function generatePrivateResourcesHtaccess() {
		return $this->renderTemplate('Resources/Private/htaccess.t', array());
	}

	public function generateTCA(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Configuration/TCA/domainObject.phpt', array('extension' => $this->extension, 'domainObject' => $domainObject, 'locallangFileFormat' => $this->locallangFileFormat));
	}

	public function generateYamlSettings() {
		return $this->renderTemplate('Configuration/ExtensionBuilder/settings.yamlt', array('extension' => $this->extension));
	}


	public function generateTyposcriptSetup() {
		return $this->renderTemplate('Configuration/TypoScript/setup.txtt', array('extension' => $this->extension));
	}

	public function generateTyposcriptConstants() {
		return $this->renderTemplate('Configuration/TypoScript/constants.txtt', array('extension' => $this->extension));
	}

	public function generateStaticTyposcript() {
		return $this->renderTemplate('ext_typoscript_setup.txtt', array('extension' => $this->extension));
	}


	/**
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject_AbstractProperty $domainProperty
	 * @param string $classType
	 * @param string $methodType (used for add, get set etc.)
	 * @param string $methodName (used for concrete methods like createAction, initialze etc.)
	 * @return string method body
	 */
	public function getDefaultMethodBody($domainObject, $domainProperty, $classType, $methodType, $methodName) {

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

		$methodBody = $this->renderTemplate('Partials/Classes/' . $classType . '/Methods/' . $methodName . 'MethodBody.phpt', $variables);
		return $methodBody;
	}

	/**
	 *
	 * @param string $extensionDirectory
	 * @param string $classType
	 * @return string
	 */
	public static function getFolderForClassFile($extensionDirectory, $classType, $createDirIfNotExist = TRUE) {
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
				\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($extensionDirectory, $classPath);
			}
			if (!is_dir($extensionDirectory . $classPath) && $createDirIfNotExist) {
				throw new Exception('folder could not be created:' . $extensionDirectory . $classPath);
			}
			return $extensionDirectory . $classPath;
		}
		else throw new Exception('Unexpected classPath:' . $classPath);
	}

	/**
	 * wrapper for \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile
	 * checks for overwrite settings
	 *
	 * @param string $targetFile the path and filename of the targetFile (relative to extension dir)
	 * @param string $fileContents
	 */
	protected function writeFile($targetFile, $fileContents) {
		if ($this->roundTripEnabled) {
			$overWriteMode = Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($targetFile, $this->extension);
			if ($overWriteMode == -1) {
				return; // skip file creation
			}
			if ($overWriteMode == 1 && strpos($targetFile, 'Classes') === FALSE) { // classes are merged by the class builder
				$fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
				if ($fileExtension == 'html') {
					//TODO: We need some kind of protocol to be displayed after code generation
					\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('File ' . basename($targetFile) . ' was not written. Template files can\'t be merged!', 'extension_builder', 1);
					return;
				} elseif (in_array($fileExtension, $this->filesSupportingSplitToken)) {
					$fileContents = $this->insertSplitToken($targetFile, $fileContents);
				}
			}
			else if (file_exists($targetFile) && $overWriteMode == 2) {
				// keep the existing file
				return;
			}
		}

		if (empty($fileContents)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('No file content! File ' . $targetFile . ' had no content', 'extension_builder', 0, $this->settings);
		}
		$success = \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($targetFile, $fileContents);
		if (!$success) {
			throw new Exception('File ' . $targetFile . ' could not be created!');
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
	protected function insertSplitToken($targetFile, $fileContents) {
		$customFileContent = '';
		if (file_exists($targetFile)) {

			// merge the files means append everything behind the split token
			$existingFileContent = file_get_contents($targetFile);
			if (strpos($existingFileContent, Tx_ExtensionBuilder_Service_RoundTrip::OLD_SPLIT_TOKEN)) {
				$existingFileContent = str_replace(Tx_ExtensionBuilder_Service_RoundTrip::OLD_SPLIT_TOKEN, Tx_ExtensionBuilder_Service_RoundTrip::SPLIT_TOKEN, $existingFileContent);
			}
			$fileParts = explode(Tx_ExtensionBuilder_Service_RoundTrip::SPLIT_TOKEN, $existingFileContent);
			if (count($fileParts) == 2) {
				$customFileContent = str_replace('?>', '', $fileParts[1]);
			}
		}

		$fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

		if ($fileExtension == 'php') {
			$fileContents = str_replace('?>', '', $fileContents);
			$fileContents .= Tx_ExtensionBuilder_Service_RoundTrip::SPLIT_TOKEN;
		}
		else if ($fileExtension == $this->locallangFileFormat) {
			//$fileContents = Tx_ExtensionBuilder_Utility_Tools::mergeLocallangXml($targetFile, $fileContents, $this->locallangFileFormat);
		}
		else {
			$fileContents .= "\n" . Tx_ExtensionBuilder_Service_RoundTrip::SPLIT_TOKEN;
		}

		$fileContents .= rtrim($customFileContent);

		if ($fileExtension == 'php') {
			$fileContents .= "\n?>";
		}
		return $fileContents;
	}

	/**
	 * wrapper for \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile
	 * checks for overwrite settings
	 *
	 * @param string $targetFile the path and filename of the targetFile
	 * @param string $fileContents
	 */
	protected function upload_copy_move($sourceFile, $targetFile) {
		$overWriteMode = Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($targetFile, $this->extension);
		if ($overWriteMode === -1) {
			// skip creation
			return;
		}
		if (!file_exists($targetFile) || ($this->roundTripEnabled && $overWriteMode < 2)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::upload_copy_move($sourceFile, $targetFile);
		}
	}

	/**
	 * wrapper for \TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep
	 * checks for overwrite settings
	 *
	 * @param string $directory base path
	 * @param string $deepDirectory
	 */
	protected function mkdir_deep($directory, $deepDirectory) {
		$subDirectories = explode('/',$deepDirectory);
		$tmpBasePath = $directory;
		foreach($subDirectories as $subDirectory) {
			$overWriteMode = Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($tmpBasePath . $subDirectory, $this->extension);
			//throw new Exception($directory . $subDirectory . '/' . $overWriteMode);
			if ($overWriteMode === -1) {
				// skip creation
				return;
			}
			if (!is_dir($deepDirectory) || ($this->roundTripEnabled && $overWriteMode < 2)) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($tmpBasePath, $subDirectory);
			}
			$tmpBasePath .= $subDirectory . '/';
		}

	}


}

?>