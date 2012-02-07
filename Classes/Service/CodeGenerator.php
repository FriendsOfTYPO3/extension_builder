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
 *
 * @package ExtensionBuilder
 */
class Tx_ExtensionBuilder_Service_CodeGenerator implements t3lib_Singleton {

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
	 * @var Tx_Extbase_Object_ObjectManager
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
	 * @var Tx_Fluid_Core_Parser_TemplateParser
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
	 * @param Tx_ExtensionBuilder_Service_ClassBuilder $classBuilder
	 */
	public function injectClassBuilder(Tx_ExtensionBuilder_Service_ClassBuilder $classBuilder) {
		$this->classBuilder = $classBuilder;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Fluid_Core_Parser_TemplateParser $templateParser
	 */
	public function injectTemplateParser(Tx_Fluid_Core_Parser_TemplateParser $templateParser) {
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
	 * TODO: split this huge method into smaller methods
	 *
	 * @param Tx_ExtensionBuilder_Domain_Model_Extension $extension
	 */
	public function build(Tx_ExtensionBuilder_Domain_Model_Extension $extension) {
		$this->extension = $extension;
		if ($this->settings['extConf']['enableRoundtrip'] == 1) {
			$this->roundTripEnabled = TRUE;
			t3lib_div::devLog('roundtrip enabled', 'extension_builder', 0, $this->settings);
		}
		else {
			t3lib_div::devLog('roundtrip disabled', 'extension_builder', 0, $this->settings);
		}
		$this->classBuilder->initialize($this, $extension, $this->roundTripEnabled);
		if (isset($this->settings['codeTemplateRootPath'])) {
			$this->codeTemplateRootPath = $this->settings['codeTemplateRootPath'];
		} else {
			throw new Exception('No codeTemplateRootPath configured');
		}

		// Base directory already exists at this point
		$this->extensionDirectory = $this->extension->getExtensionDir();
		if (!is_dir($this->extensionDirectory)) {
			t3lib_div::mkdir($this->extensionDirectory);
		}

		// Generate ext_emconf.php, ext_tables.* and TCA definition
		$extensionFiles = array('ext_emconf.php', 'ext_tables.php', 'ext_tables.sql');
		foreach ($extensionFiles as $extensionFile) {
			try {
				$fileContents = $this->renderTemplate(t3lib_div::underscoredToLowerCamelCase($extensionFile) . 't', array('extension' => $this->extension));
				$this->writeFile($this->extensionDirectory . $extensionFile, $fileContents);
				t3lib_div::devlog('Generated ' . $extensionFile, 'extension_builder', 0, array('Content' => $fileContents));
			}
			catch (Exception $e) {
				throw new Exception('Could not write ' . $extensionFile . ', error: ' . $e->getMessage());
			}
		}

		if ($this->extension->getPlugins()) {
			try {
				$fileContents = $this->renderTemplate(t3lib_div::underscoredToLowerCamelCase('ext_localconf.phpt'), array('extension' => $this->extension));
				$this->writeFile($this->extensionDirectory . 'ext_localconf.php', $fileContents);
				t3lib_div::devlog('Generated ext_localconf.php', 'extension_builder', 0, array('Content' => $fileContents));
			}
			catch (Exception $e) {
				throw new Exception('Could not write ext_localconf.php. Error: ' . $e->getMessage());
			}
			try {
				$currentPluginKey = '';
				foreach ($this->extension->getPlugins() as $plugin) {
					if ($plugin->getSwitchableControllerActions()) {
						if (!is_dir($this->extensionDirectory . 'Configuration/FlexForms')) {
							t3lib_div::mkdir_deep($this->extensionDirectory, 'Configuration/FlexForms');
						}
						$currentPluginKey = $plugin->getKey();
						$fileContents = $this->renderTemplate('Configuration/Flexforms/flexform.xmlt', array('plugin' => $plugin));
						$this->writeFile($this->extensionDirectory . 'Configuration/FlexForms/flexform_' . $currentPluginKey . '.xml', $fileContents);
						t3lib_div::devlog('Generated flexform_' . $currentPluginKey . '.xml', 'extension_builder', 0, array('Content' => $fileContents));
					}
				}
			}
			catch (Exception $e) {
				throw new Exception('Could not write  flexform_' . $currentPluginKey . '.xml. Error: ' . $e->getMessage());
			}
		}

		try {
			$this->upload_copy_move(t3lib_extMgm::extPath('extension_builder') . 'Resources/Private/Icons/ext_icon.gif', $this->extensionDirectory . 'ext_icon.gif');
		} catch (Exception $e) {
			throw new Exception('Could not copy ext_icon.gif, error: ' . $e->getMessage());
		}

		// insert a manual template
		try {
			if (!file_exists($this->extensionDirectory . 'doc/manual.sxw') && file_exists($this->codeTemplateRootPath . 'doc/manual.sxw')) {
				t3lib_div::mkdir_deep($this->extensionDirectory, 'doc');
				$this->upload_copy_move($this->codeTemplateRootPath . 'doc/manual.sxw', $this->extensionDirectory . 'doc/manual.sxw');
			}
		} catch (Exception $e) {
			throw new Exception('An error occurred when copying the manual template: ' . $e->getMessage() . $e->getFile());
		}

		// Generate TCA
		try {
			t3lib_div::mkdir_deep($this->extensionDirectory, 'Configuration/TCA');
			$configurationDirectory = $this->extensionDirectory . 'Configuration/';
			$domainObjects = $this->extension->getDomainObjects();

			foreach ($domainObjects as $domainObject) {
				$fileContents = $this->generateTCA($domainObject);
				$this->writeFile($configurationDirectory . 'TCA/' . $domainObject->getName() . '.php', $fileContents);
			}

		} catch (Exception $e) {
			throw new Exception('Could not generate Tca.php, error: ' . $e->getMessage() . $e->getFile());
		}

		if (!file_exists($configurationDirectory . 'ExtensionBuilder/settings.yaml')) {
			t3lib_div::mkdir($configurationDirectory . 'ExtensionBuilder');
			$fileContents = $this->generateYamlSettings();
			$targetFile = $configurationDirectory . 'ExtensionBuilder/settings.yaml';
			t3lib_div::writeFile($targetFile, $fileContents);
		}

		if ($extension->hasPlugins() || $extension->hasBackendModules()) {
			// Generate TypoScript setup
			try {
				t3lib_div::mkdir_deep($this->extensionDirectory, 'Configuration/TypoScript');
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

		// Generate Private Resources .htaccess
		try {
			t3lib_div::mkdir_deep($this->extensionDirectory, 'Resources/Private');
			$privateResourcesDirectory = $this->extensionDirectory . 'Resources/Private/';
			$fileContents = $this->generatePrivateResourcesHtaccess();
			$this->writeFile($privateResourcesDirectory . '.htaccess', $fileContents);
		} catch (Exception $e) {
			throw new Exception('Could not create private resources folder, error: ' . $e->getMessage());
		}

		// Generate locallang*.xml files
		try {
			t3lib_div::mkdir_deep($privateResourcesDirectory, 'Language');
			$languageDirectory = $privateResourcesDirectory . 'Language/';
			$fileContents = $this->generateLocallang();
			$this->writeFile($languageDirectory . 'locallang.xml', $fileContents);
			$fileContents = $this->generateLocallangDB();
			$this->writeFile($languageDirectory . 'locallang_db.xml', $fileContents);
			if ($this->extension->hasBackendModules()) {
				foreach ($this->extension->getBackendModules() as $backendModule) {
					$fileContents = $this->generateLocallangModule($backendModule);
					$this->writeFile($languageDirectory . 'locallang_' . $backendModule->getKey() . '.xml', $fileContents);
				}

			}
		} catch (Exception $e) {
			throw new Exception('Could not generate locallang files, error: ' . $e->getMessage());
		}

		try {
			t3lib_div::mkdir_deep($this->extensionDirectory, 'Resources/Public');
			$publicResourcesDirectory = $this->extensionDirectory . 'Resources/Public/';
			t3lib_div::mkdir_deep($publicResourcesDirectory, 'Icons');
			$iconsDirectory = $publicResourcesDirectory . 'Icons/';
			$this->upload_copy_move(t3lib_extMgm::extPath('extension_builder') . 'Resources/Private/Icons/relation.gif', $iconsDirectory . 'relation.gif');
		} catch (Exception $e) {
			throw new Exception('Could not create public resources folder, error: ' . $e->getMessage());
		}

		if (count($this->extension->getDomainObjects()) > 0) {
			// Generate Domain Model
			try {

				$domainModelDirectory = 'Classes/Domain/Model/';
				t3lib_div::mkdir_deep($this->extensionDirectory, $domainModelDirectory);

				$domainRepositoryDirectory = 'Classes/Domain/Repository/';
				t3lib_div::mkdir_deep($this->extensionDirectory, $domainRepositoryDirectory);

				t3lib_div::mkdir_deep($this->extensionDirectory, 'Tests/Unit/Domain/Model');
				$domainModelTestsDirectory = $this->extensionDirectory . 'Tests/Unit/Domain/Model/';

				t3lib_div::mkdir_deep($this->extensionDirectory, 'Tests/Unit/Controller');
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
					t3lib_div::devlog('Generated ' . $domainObject->getName() . '.php', 'extension_builder', 0);
					$this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);

					if ($domainObject->isAggregateRoot()) {
						$iconFileName = 'aggregate_root.gif';
					} elseif ($domainObject->isEntity()) {
						$iconFileName = 'entity.gif';
					} else {
						$iconFileName = 'value_object.gif';
					}
					$this->upload_copy_move(t3lib_extMgm::extPath('extension_builder') . 'Resources/Private/Icons/' . $iconFileName, $iconsDirectory . $domainObject->getDatabaseTableName() . '.gif');

					$fileContents = $this->generateLocallangCsh($domainObject);
					$this->writeFile($languageDirectory . 'locallang_csh_' . $domainObject->getDatabaseTableName() . '.xml', $fileContents);

					if ($domainObject->isAggregateRoot()) {
						$destinationFile = $domainRepositoryDirectory . $domainObject->getName() . 'Repository.php';
						if ($this->roundTripEnabled && Tx_ExtensionBuilder_Service_RoundTrip::getOverWriteSettingForPath($destinationFile, $this->extension) > 0) {
							$mergeWithExistingClass = TRUE;
						} else {
							$mergeWithExistingClass = FALSE;
						}
						$fileContents = $this->generateDomainRepositoryCode($domainObject, $mergeWithExistingClass);
						$this->writeFile($this->extensionDirectory . $destinationFile, $fileContents);
						t3lib_div::devlog('Generated ' . $domainObject->getName() . 'Repository.php', 'extension_builder', 0);
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
				t3lib_div::mkdir_deep($this->extensionDirectory, 'Classes/Controller');
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
					t3lib_div::devlog('Generated ' . $domainObject->getName() . 'Controller.php', 'extension_builder', 0);
					$this->extension->setMD5Hash($this->extensionDirectory . $destinationFile);

					// Generate basic UnitTests
					$fileContents = $this->generateScaffoldingControllerTests($domainObject->getName() . 'Controller', $domainObject);
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
					Tx_Extbase_Utility_Extension::createAutoloadRegistryForExtension($this->extension->getExtensionKey(), $this->extensionDirectory);
				}
			} catch (Exception $e) {
				throw new Exception('Could not generate ext_autoload.php, error: ' . $e->getMessage());
			}


		}
		else {
			t3lib_div::devlog('No domainObjects in this extension', 'extension_builder', 3, (array)$this->extension);
		}
	}

	protected function generateTemplateFiles($templateSubFolder = '') {
		$templateRootFolder = 'Resources/Private/' . $templateSubFolder;
		$privateResourcesDirectory = $this->extensionDirectory . $templateRootFolder;
		$hasTemplates = FALSE;
		$actionsUsingFormFieldsPartial = array('edit', 'new');
		$actionsUsingPropertiesPartial = array('show');
		foreach ($this->extension->getDomainObjects() as $domainObject) {
			// Do not generate anyting if $domainObject is not an Entity or has no actions defined
			if (!$domainObject->getEntity() || (count($domainObject->getActions()) == 0)) {
				continue;
			}
			$domainTemplateDirectory = $privateResourcesDirectory . 'Templates/' . $domainObject->getName() . '/';
			foreach ($domainObject->getActions() as $action) {
				if ($action->getNeedsTemplate()
					&& file_exists($this->codeTemplateRootPath . $templateRootFolder . 'Templates/' . $action->getName() . '.htmlt')
				) {
					$hasTemplates = TRUE;
					t3lib_div::mkdir_deep($this->extensionDirectory, $templateRootFolder . 'Templates/' . $domainObject->getName());
					$fileContents = $this->generateDomainTemplate($templateRootFolder . 'Templates/', $domainObject, $action);
					$this->writeFile($domainTemplateDirectory . ucfirst($action->getName()) . '.html', $fileContents);
					// generate partials for formfields
					if ($action->getNeedsForm()) {
						t3lib_div::mkdir_deep($privateResourcesDirectory, 'Partials');
						$partialDirectory = $privateResourcesDirectory . 'Partials/';
						t3lib_div::mkdir_deep($partialDirectory, $domainObject->getName());
						$formfieldsPartial = $partialDirectory . $domainObject->getName() . '/FormFields.html';
						$fileContents = $this->generateDomainFormFieldsPartial($templateRootFolder . 'Partials/', $domainObject);
						$this->writeFile($formfieldsPartial, $fileContents);
						if (!file_exists($partialDirectory . 'FormErrors.html')) {
							$this->writeFile($partialDirectory . 'FormErrors.html', $this->generateFormErrorsPartial($templateRootFolder . 'Partials/'));
						}
					}
					// generate partials for properties
					if ($action->getNeedsPropertyPartial()) {
						t3lib_div::mkdir_deep($privateResourcesDirectory, 'Partials');
						$partialDirectory = $privateResourcesDirectory . 'Partials/';
						t3lib_div::mkdir_deep($partialDirectory, $domainObject->getName());
						$propertiesPartial = $partialDirectory . $domainObject->getName() . '/Properties.html';
						$fileContents = $this->generateDomainPropertiesPartial($templateRootFolder . 'Partials/', $domainObject);
						$this->writeFile($propertiesPartial, $fileContents);
					}
				}
			}
		}
		if ($hasTemplates) {
			// Generate Layouts directory
			t3lib_div::mkdir_deep($privateResourcesDirectory, 'Layouts');
			$layoutsDirectory = $privateResourcesDirectory . 'Layouts/';
			$this->writeFile($layoutsDirectory . 'Default.html', $this->generateLayout($templateRootFolder . 'Layouts/'));
		}
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
	 *
	 * @return string rendered content
	 */
	protected function renderTemplate($filePath, $variables) {
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
		return preg_replace('/^\s*\n[\t ]*$/m','',$renderedContent);
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
	public function generateScaffoldingControllerTests($controllerName, Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Tests/ScaffoldingControllerTest.phpt', array('extension' => $this->extension, 'controllerName' => $controllerName, 'domainObject' => $domainObject));
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
		$codeTemplateRootPath = $this->codeTemplateRootPath . $templateRootFolder;
		return file_get_contents($codeTemplateRootPath . 'formErrors.htmlt');
	}

	public function generateLayout($templateRootFolder) {
		return $this->renderTemplate($templateRootFolder . 'default.htmlt', array('extension' => $this->extension));
	}

	public function generateLocallang() {
		return $this->renderTemplate('Resources/Private/Language/locallang.xmlt', array('extension' => $this->extension));
	}

	public function generateLocallangDB() {
		return $this->renderTemplate('Resources/Private/Language/locallang_db.xmlt', array('extension' => $this->extension));
	}

	public function generateLocallangModule($backendModule) {
		return $this->renderTemplate('Resources/Private/Language/locallang_mod.xmlt', array('extension' => $this->extension, 'backendModule' => $backendModule));
	}

	public function generateLocallangCsh(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Resources/Private/Language/locallang_csh.xmlt', array('extension' => $this->extension, 'domainObject' => $domainObject));
	}

	public function generatePrivateResourcesHtaccess() {
		return $this->renderTemplate('Resources/Private/htaccess.t', array());
	}

	public function generateTCA(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Configuration/TCA/domainObject.phpt', array('extension' => $this->extension, 'domainObject' => $domainObject));
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
			case 'Model'		:
				$classPath = 'Classes/Domain/Model/';
				break;

			case 'Controller'	:
				$classPath = 'Classes/Controller/';
				break;

			case 'Repository'	:
				$classPath = 'Classes/Domain/Repository/';
				break;
		}
		if (!empty($classPath)) {
			if (!is_dir($extensionDirectory . $classPath) && $createDirIfNotExist) {
				t3lib_div::mkdir_deep($extensionDirectory, $classPath);
			}
			if (!is_dir($extensionDirectory . $classPath) && $createDirIfNotExist) {
				throw new Exception('folder could not be created:' . $extensionDirectory . $classPath);
			}
			return $extensionDirectory . $classPath;
		}
		else throw new Exception('Unexpected classPath:' . $classPath);
	}

	/**
	 * wrapper for t3lib_div::writeFile
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
				if (strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)) == 'html') {
					//TODO: We need some kind of protocol to be displayed after code generation
					t3lib_div::devlog('File ' . basename($targetFile) . ' was not written. Template files can\'t be merged!', 'extension_builder', 1);
					return;
				} else {
					$fileContents = $this->insertSplitToken($targetFile, $fileContents);
				}
			}
			else if (file_exists($targetFile) && $overWriteMode == 2) {
				// keep the existing file
				return;
			}
		}

		if (empty($fileContents)) {
			t3lib_div::devLog('No file content! File ' . $targetFile . ' had no content', 'extension_builder', 0, $this->settings);
		}
		$success = t3lib_div::writeFile($targetFile, $fileContents);
		if (!$success) {
			throw new Exception('File ' . $targetFile . ' could not be created!');
		}
	}

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
		else if ($fileExtension == 'xml') {
			$fileContents = Tx_ExtensionBuilder_Service_RoundTrip::mergeLocallangXml($targetFile, $fileContents);
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
	 * wrapper for t3lib_div::writeFile
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
			t3lib_div::upload_copy_move($sourceFile, $targetFile);
		}
	}


}

?>
