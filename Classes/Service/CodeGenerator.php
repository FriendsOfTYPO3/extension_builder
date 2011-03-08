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
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
class Tx_ExtbaseKickstarter_Service_CodeGenerator implements t3lib_Singleton {
	
	/**
	 *
	 * @var Tx_Fluid_Core_Parser_TemplateParser
	 */
	protected $templateParser;

	/**
	 *
	 * @var Tx_Fluid_Compatibility_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_ExtbaseKickstarter_Domain_Model_Extension
	 */
	protected $extension;
	
	/**
	 * 
	 * @var Tx_ExtbaseKickstarter_Service_ClassBuilder
	 */
	protected $classBuilder;	
	
	/**
	 * @var boolean
	 */
	protected $roundTripEnabled = false;
	
	/**
	 * 
	 * @var array
	 */
	protected $overWriteSettings;
	
	
	/**
	 * 
	 * @return void
	 */
	public function __construct() {
		
		$this->settings = Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getKickstarterSettings();
		if (!$this->templateParser instanceof Tx_Fluid_Core_Parser_TemplateParser) {
			$this->injectTemplateParser(Tx_Fluid_Compatibility_TemplateParserBuilder::build());
			$this->classBuilder = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_ClassBuilder');
		}

		if (class_exists('Tx_Fluid_Compatibility_ObjectManager') &&
				!$this->objectManager instanceof Tx_Fluid_Compatibility_ObjectManager) {
			$this->objectManager = new Tx_Fluid_Compatibility_ObjectManager();
		} elseif (!$this->objectManager instanceof Tx_Extbase_Object_ObjectManager) {
			$this->injectObjectManager(new Tx_Extbase_Object_ObjectManager());
		}
	}
	
	
	/**
	 * @param Tx_Fluid_Core_Parser_TemplateParser $templateParser
	 * @return void
	 */
	public function injectTemplateParser(Tx_Fluid_Core_Parser_TemplateParser $templateParser) {
		$this->templateParser = $templateParser;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}
	
	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->settings = Tx_ExtbaseKickstarter_Utility_ConfigurationManager::getKickstarterSettings();
	}
	
	/**
	 * @param Tx_ExtbaseKickstarter_Service_ClassBuilder $classBuilder
	 * @return void
	 */
	public function injectClassBuilder(Tx_ExtbaseKickstarter_Service_ClassBuilder $classBuilder) {
		$this->classBuilder = $classBuilder;
	}
	
	
	/**
	 * The entry point to the class
	 * 
	 * TODO: split this huge method into smaller methods
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_Extension $extension
	 * @return string a result message "success" or an error message describing the error
	 */
	public function build(Tx_ExtbaseKickstarter_Domain_Model_Extension $extension) {
		$this->extension = $extension;
		
		$this->classBuilder->initialize($this->extension);
		if($this->settings['enableRoundtrip']==1){
			$this->roundTripEnabled = true;
		}
		else t3lib_div::devLog('roundtrip disabled', 'extbase_kickstarter',0,$this->settings);

		// Base directory already exists at this point
		$this->extensionDirectory = $this->extension->getExtensionDir();
		if(!is_dir($this->extensionDirectory)){
			t3lib_div::mkdir($this->extensionDirectory);
		}
		
		// Generate ext_emconf.php, ext_tables.* and TCA definition
		$extensionFiles = array('ext_emconf.php','ext_tables.php','ext_tables.sql');
		foreach($extensionFiles as  $extensionFile){
			try {
				$fileContents = $this->renderTemplate( Tx_Extbase_Utility_Extension::convertUnderscoredToLowerCamelCase($extensionFile).'t', array('extension' => $this->extension));
				$this->writeFile($this->extensionDirectory . $extensionFile, $fileContents);
				t3lib_div::devlog('Generated '.$extensionFile,'kickstarter',0,array('Content'=>$fileContents));
			} 
			catch (Exception $e) {
				return 'Could not write '.$extensionFile.', error: ' . $e->getMessage();
			}
		}
		
		if($this->extension->getPlugins()){
			try {
				$fileContents = $this->renderTemplate( Tx_Extbase_Utility_Extension::convertUnderscoredToLowerCamelCase('ext_localconf.phpt'), array('extension' => $this->extension));
				$this->writeFile($this->extensionDirectory . 'ext_localconf.php', $fileContents);
				t3lib_div::devlog('Generated ext_localconf.php','kickstarter',0,array('Content'=>$fileContents));
			} 
			catch (Exception $e) {
				return 'Could not write '.$extensionFile.', error: ' . $e->getMessage();
			}
		}
		
		try {
			$this->upload_copy_move(t3lib_extMgm::extPath('extbase_kickstarter') . 'Resources/Private/Icons/ext_icon.gif', $this->extensionDirectory . 'ext_icon.gif');
		} catch (Exception $e) {
			return 'Could not copy ext_icon.gif, error: ' . $e->getMessage();
		}
		
		// Generate TCA
		try {
			t3lib_div::mkdir_deep($this->extensionDirectory, 'Configuration/TCA');
			$tcaDirectory = $this->extensionDirectory . 'Configuration/';
			$domainObjects = $this->extension->getDomainObjects();
			
			foreach ($domainObjects as $domainObject) {
				$fileContents = $this->generateTCA($domainObject);
				$this->writeFile($tcaDirectory . 'TCA/' . $domainObject->getName() . '.php', $fileContents);
			}

		} catch (Exception $e) {
			return 'Could not generate Tca.php, error: ' . $e->getMessage();
		}
		
		if($this->roundTripEnabled && !file_exists($tcaDirectory.'Kickstarter/settings.yaml')){
			t3lib_div::mkdir($tcaDirectory.'Kickstarter');
			$fileContents = $this->generateYamlSettings();
			$targetFile = $tcaDirectory.'Kickstarter/settings.yaml';
			t3lib_div::writeFile($targetFile, $fileContents);
			
		}

		// Generate TypoScript setup
		try {
			t3lib_div::mkdir_deep($this->extensionDirectory, 'Configuration/TypoScript');
			$typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
			$fileContents = $this->generateTyposcriptSetup();
			$this->writeFile($typoscriptDirectory . 'setup.txt', $fileContents);
		} catch (Exception $e) {
			return 'Could not generate typoscript setup, error: ' . $e->getMessage();
		}

		// Generate TypoScript constants
		try {
			$typoscriptDirectory = $this->extensionDirectory . 'Configuration/TypoScript/';
			$fileContents = $this->generateTyposcriptConstants();
			$this->writeFile($typoscriptDirectory . 'constants.txt', $fileContents);
		} catch (Exception $e) {
			return 'Could not generate typoscript constants, error: ' . $e->getMessage();
		}
		
			// Generate Static TypoScript
		try {
			if($this->extension->hasPropertiesThatNeedMapping()){
				$fileContents = $this->generateStaticTyposcript();
				$this->writeFile($this->extensionDirectory . 'ext_typoscript_setup.txt', $fileContents);
			}
		} catch (Exception $e) {
			return 'Could not generate static typoscript, error: ' . $e->getMessage();
		}

		// Generate Private Resources .htaccess
		try {
			t3lib_div::mkdir_deep($this->extensionDirectory, 'Resources/Private');
			$privateResourcesDirectory = $this->extensionDirectory . 'Resources/Private/';
			$fileContents = $this->generatePrivateResourcesHtaccess();
			$this->writeFile($privateResourcesDirectory . '.htaccess', $fileContents);
		} catch (Exception $e) {
			return 'Could not create private resources folder, error: ' . $e->getMessage();
		}

		// Generate locallang*.xml files
		try {
			t3lib_div::mkdir_deep($privateResourcesDirectory, 'Language');
			$languageDirectory = $privateResourcesDirectory . 'Language/';
			$fileContents = $this->generateLocallang();
			$this->writeFile($languageDirectory . 'locallang.xml', $fileContents);
			$fileContents = $this->generateLocallangDB();
			$this->writeFile($languageDirectory . 'locallang_db.xml', $fileContents);
			if($this->extension->hasBackendModules()){
				foreach($this->extension->getBackendModules() as $backendModule){
					$fileContents = $this->generateLocallangModule($backendModule);
					$this->writeFile($languageDirectory . 'locallang_' . $backendModule->getKey() . '.xml', $fileContents);
				}

			}
		} catch (Exception $e) {
			return 'Could not generate locallang files, error: ' . $e->getMessage();
		}

		try {
			t3lib_div::mkdir_deep($this->extensionDirectory, 'Resources/Public');
			$publicResourcesDirectory = $this->extensionDirectory . 'Resources/Public/';
			t3lib_div::mkdir_deep($publicResourcesDirectory, 'Icons');
			$iconsDirectory = $publicResourcesDirectory . 'Icons/';
			$this->upload_copy_move(t3lib_extMgm::extPath('extbase_kickstarter') . 'Resources/Private/Icons/relation.gif', $iconsDirectory . 'relation.gif');
		} catch (Exception $e) {
			return 'Could not create public resources folder, error: ' . $e->getMessage();
		}
				
		if (count($this->extension->getDomainObjects()) > 0 ) {
			t3lib_div::devlog(count($this->extension->getDomainObjects()).' domainObjects','kickstarter',0,(array)$this->extension->getDomainObjects());
			// Generate Domain Model
			try {
				t3lib_div::mkdir_deep($this->extensionDirectory, 'Classes/Domain/Model');
				$domainModelDirectory = $this->extensionDirectory . 'Classes/Domain/Model/';
				t3lib_div::mkdir_deep($this->extensionDirectory, 'Classes/Domain/Repository');
				$domainRepositoryDirectory = $this->extensionDirectory . 'Classes/Domain/Repository/';

				t3lib_div::mkdir_deep($this->extensionDirectory, 'Tests/Domain/Model');
				$domainModelTestsDirectory = $this->extensionDirectory . 'Tests/Domain/Model/';

				t3lib_div::mkdir_deep($this->extensionDirectory, 'Tests/Controller');
				$crudEnabledControllerTestsDirectory = $this->extensionDirectory . 'Tests/Controller/';

				foreach ($this->extension->getDomainObjects() as $domainObject) {
					$fileContents = $this->generateDomainObjectCode($domainObject);
					$this->writeFile($domainModelDirectory . $domainObject->getName() . '.php', $fileContents);
					t3lib_div::devlog('Generated '.$domainObject->getName() . '.php','kickstarter',0);
					$this->extension->setMD5Hash($domainModelDirectory . $domainObject->getName() . '.php');
					
					if ($domainObject->isAggregateRoot()) {
						$iconFileName = 'aggregate_root.gif';
					} elseif ($domainObject->isEntity()) {
						$iconFileName = 'entity.gif';
					} else {
						$iconFileName = 'value_object.gif';
					}
					$this->upload_copy_move(t3lib_extMgm::extPath('extbase_kickstarter') . 'Resources/Private/Icons/' . $iconFileName, $iconsDirectory . $domainObject->getDatabaseTableName() . '.gif');

					$fileContents = $this->generateLocallangCsh($domainObject);
					$this->writeFile($languageDirectory . 'locallang_csh_' . $domainObject->getDatabaseTableName() . '.xml', $fileContents);

					if ($domainObject->isAggregateRoot()) {
						$fileContents = $this->generateDomainRepositoryCode($domainObject);
						$this->writeFile($domainRepositoryDirectory . $domainObject->getName() . 'Repository.php', $fileContents);
						t3lib_div::devlog('Generated '.$domainObject->getName() . 'Repository.php','kickstarter',0);
						$this->extension->setMD5Hash($domainRepositoryDirectory . $domainObject->getName() . 'Repository.php');
					}

					// Generate basic UnitTests
					$fileContents = $this->generateDomainModelTests($domainObject);
					$this->writeFile($domainModelTestsDirectory . $domainObject->getName() . 'Test.php', $fileContents);
				}
			} catch (Exception $e) {
				return 'Could not generate domain model, error: ' . $e->getMessage();
			}

			// Generate Action Controller
			try {
				t3lib_div::mkdir_deep($this->extensionDirectory, 'Classes/Controller');
				$controllerDirectory = $this->extensionDirectory . 'Classes/Controller/';
				foreach ($this->extension->getDomainObjectsForWhichAControllerShouldBeBuilt() as $domainObject) {
					$fileContents = $this->generateActionControllerCode($domainObject);
					$this->writeFile($controllerDirectory . $domainObject->getName() . 'Controller.php', $fileContents);
					t3lib_div::devlog('Generated '.$domainObject->getName() . 'Controller.php','kickstarter',0);
					$this->extension->setMD5Hash($controllerDirectory . $domainObject->getName() . 'Controller.php');

					$this->generateScaffoldingControllerTests($controllerName, $domainObject); 
				}
			} catch (Exception $e) {
				return 'Could not generate action controller, error: ' . $e->getMessage();
			}
			
			// Generate Domain Templates
			try {
				if($this->extension->getPlugins()){
					$this->generateTemplateFiles();
				}
				if($this->extension->getBackendModules()){
					$this->generateTemplateFiles('Backend/');
				}
			} catch (Exception $e) {
				return 'Could not generate domain templates, error: ' . $e->getMessage();
			}

			
		}
		else {
			t3lib_div::devlog('No domainObjects in this extension','kickstarter',3,(array)$this->extension);
		}
		return 'success';
	}
	
	protected function generateTemplateFiles($templateSubFolder = ''){
		$templateRootFolder = 'Resources/Private/'.$templateSubFolder;
		$privateResourcesDirectory = $this->extensionDirectory.$templateRootFolder;
		$hasTemplates = false;
		$actionsUsingFormFieldsPartial = array('edit','new');
		$actionsUsingPropertiesPartial = array('show');
		foreach ($this->extension->getDomainObjects() as $domainObject) {
			// Do not generate anyting if $domainObject is not an Entity or has no actions defined
			if (!$domainObject->getEntity() || (count($domainObject->getActions()) == 0)) {
				continue;
			}
			$domainTemplateDirectory = $privateResourcesDirectory . 'Templates/' . $domainObject->getName() . '/';
			foreach($domainObject->getActions() as $action) {
				if ($action->getNeedsTemplate()
					&& file_exists(t3lib_extMgm::extPath('extbase_kickstarter').'Resources/Private/CodeTemplates/'.$templateRootFolder.'Templates/' . $action->getName() . '.htmlt')){
					$hasTemplates = true;
					t3lib_div::mkdir_deep($this->extensionDirectory, $templateRootFolder .'Templates/' . $domainObject->getName());
					$fileContents = $this->generateDomainTemplate($templateRootFolder.'Templates/',$domainObject, $action);
					$this->writeFile($domainTemplateDirectory . ucfirst($action->getName()) . '.html', $fileContents);
						// generate partials for formfields 
					if($action->getNeedsForm()){
						t3lib_div::mkdir_deep($privateResourcesDirectory, 'Partials');
						$partialDirectory =  $privateResourcesDirectory . 'Partials/';
						t3lib_div::mkdir_deep($partialDirectory, $domainObject->getName());
						$formfieldsPartial = $partialDirectory.$domainObject->getName().'/FormFields.html';
						$fileContents = $this->generateDomainFormFieldsPartial($templateRootFolder.'Partials/',$domainObject);
						$this->writeFile($formfieldsPartial, $fileContents);
						t3lib_div::devlog($templateRootFolder.'formErrors.htmlt');
						if(!file_exists($partialDirectory . 'FormErrors.html')){
							t3lib_div::devLog($partialDirectory.'formErrors.htmlt', 'extbase_kickstarter',0,array('Content'=>$this->generateFormErrorsPartial($templateRootFolder.'Partials/')));
							$this->writeFile($partialDirectory . 'FormErrors.html',$this->generateFormErrorsPartial($templateRootFolder.'Partials/'));
						}
					}
						// generate partials for properties 
					if($action->getNeedsPropertyPartial()){
						t3lib_div::mkdir_deep($privateResourcesDirectory, 'Partials');
						$partialDirectory =  $privateResourcesDirectory . 'Partials/';
						t3lib_div::mkdir_deep($partialDirectory, $domainObject->getName());
						$propertiesPartial = $partialDirectory.$domainObject->getName().'/Properties.html';
						$fileContents = $this->generateDomainPropertiesPartial($templateRootFolder.'Partials/',$domainObject);
						$this->writeFile($propertiesPartial, $fileContents);
					}
				}
			}
		}
		if($hasTemplates){
			// Generate Layouts directory
			t3lib_div::mkdir_deep($privateResourcesDirectory, 'Layouts');
			$layoutsDirectory = $privateResourcesDirectory . 'Layouts/';
			$this->writeFile($layoutsDirectory . 'Default.html', $this->generateLayout($templateRootFolder.'Layouts/'));
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

		$renderingContext->setTemplateVariableContainer($variableContainer);
		$renderingContext->setViewHelperVariableContainer($viewHelperVariableContainer);

		return $renderingContext;
	}
	
	/**
	 * Render a template with variables
	 * 
	 * @param string $filePath
	 * @param array $variables
	 */
	protected function renderTemplate($filePath, $variables) {
		$codeTemplateRootPath = $this->getCodeTemplateRootPath();
		if(!is_file($codeTemplateRootPath. $filePath)){
			throw(new Exception('TemplateFile '.$codeTemplateRootPath . $filePath.' not found'));
		}
				
		$parsedTemplate = $this->templateParser->parse(file_get_contents($codeTemplateRootPath . $filePath));
		return trim($parsedTemplate->render($this->buildRenderingContext($variables)));
	}
	
	/**
	 * Get the path for the code templates
	 *
	 * @return string path
	 */
	protected function getCodeTemplateRootPath(){
		if(isset($this->settings['codeTemplateRootPath'])){
			return PATH_site.$this->settings['codeTemplateRootPath'];
		}
		else {
			return t3lib_extMgm::extPath('extbase_kickstarter').'Resources/Private/CodeTemplates/';
		}
	}

	/**
	 * Generates the code for the controller class
	 * Either from ectionController template or from class partial
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 */
	public function generateActionControllerCode(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		if($this->roundTripEnabled){
			$controllerClassObject = $this->classBuilder->generateControllerClassObject($domainObject);
			// returns a class object if an existing class was found
			if($controllerClassObject){
				$classDocComment = $this->renderDocComment($controllerClassObject,$domainObject);
				$controllerClassObject->setDocComment($classDocComment);
				
				return $this->renderTemplate('Partials/Classes/class.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension,'classObject'=>$controllerClassObject));
			}
		}
		return $this->renderTemplate('Classes/Controller/actionController.phpt', array('domainObject' => $domainObject,'extension'=>$this->extension));
	}
	
	/**
	 * Generates the code for the domain model class
	 * Either from domainObject template or from class partial
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 */
	public function generateDomainObjectCode(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		if($this->roundTripEnabled){
			$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject);
			if($modelClassObject){
				$classDocComment = $this->renderDocComment($modelClassObject,$domainObject);
				$modelClassObject->setDocComment($classDocComment);
				
				return $this->renderTemplate('Partials/Classes/class.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension,'classObject'=>$modelClassObject));
			}
		}
		return $this->renderTemplate('Classes/Domain/Model/domainObject.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension));
		
	}
	
	/**
	 * Generates the code for the repository class
	 * Either from domainRepository template or from class partial
	 * 
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 */
	public function generateDomainRepositoryCode(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		if($this->roundTripEnabled){
			$repositoryClassObject = $this->classBuilder->generateRepositoryClassObject($domainObject);
			if($repositoryClassObject){
				$classDocComment = $this->renderDocComment($repositoryClassObject,$domainObject);
				$repositoryClassObject->setDocComment($classDocComment);
				
				return $this->renderTemplate('Partials/Classes/class.phpt', array('domainObject' => $domainObject,'classObject' => $repositoryClassObject));
			}
			
		}
		return $this->renderTemplate('Classes/Domain/Repository/domainRepository.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension));
	}
	
	/**
	 * Generate the tests for a model
	 *
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 *
	 * @return string
	 */
	public function generateDomainModelTests(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Tests/DomainModelTest.phpt', array('extension' => $this->extension, 'domainObject' => $domainObject));
	}

	/**
	 * Generate the tests for a CRUD-enabled controller
	 *
	 * @param array $extensionProperties
	 * @param string $controllerName
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 *
	 * @return string
	 */
	public function generateScaffoldingControllerTests($controllerName, Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Tests/ScaffoldingControllerTest.phpt', array('extension' => $this->extension, 'controllerName' => $controllerName, 'domainObject' => $domainObject));
	}

	/**
	 * generate a docComment for class files. Add a license haeder if none found
	 * @param unknown_type $classObject
	 * @param unknown_type $domainObject
	 */
	protected function renderDocComment($classObject,$domainObject){
		if(!$classObject->hasDocComment()){
			$docComment = $this->renderTemplate('Partials/Classes/classDocComment.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension,'classObject'=>$classObject));
		}
		else {
			$docComment = $classObject->getDocComment();
		}
		$precedingBlock = $classObject->getPrecedingBlock();
		
		if(empty($precedingBlock) || strpos($precedingBlock,'GNU General Public License')<1){
			
			$licenseHeader = $this->renderTemplate('Partials/Classes/licenseHeader.phpt', array('persons' => $this->extension->getPersons()));
			$docComment = $licenseHeader."\n\n\n".$docComment;
			t3lib_div::devlog('No license header in: '.$classObject->getName(),'kickstarter');
		}
		else {
			$docComment = $precedingBlock."\n".$docComment;
		}
		return $docComment;
	}
	
	/**
	 * Generates the content of an Action template
	 * For some Actions default templates are provided, other Action templates will just be created emtpy
	 *
	 * @param string $templateRootFolder
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject
	 * @param Tx_ExtbaseKickstarter_Domain_Model_DomainObject_Action $action
	 * @return string The generated Template code (might be empty)
	 */
	public function generateDomainTemplate($templateRootFolder, Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject, Tx_ExtbaseKickstarter_Domain_Model_DomainObject_Action $action) {
			return $this->renderTemplate($templateRootFolder. $action->getName() . '.htmlt', array('domainObject' => $domainObject, 'action' => $action, 'extension' => $this->extension));
	}
	
	public function generateDomainFormFieldsPartial($templateRootFolder, Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject){
		return $this->renderTemplate($templateRootFolder.'formFields.htmlt', array('extension' => $this->extension,'domainObject' => $domainObject));
	}
	
	public function generateDomainPropertiesPartial($templateRootFolder, Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject){
		return $this->renderTemplate($templateRootFolder.'properties.htmlt', array('extension' => $this->extension,'domainObject' => $domainObject));
	}

	public function generateFormErrorsPartial($templateRootFolder) {
		$codeTemplateRootPath = $this->getCodeTemplateRootPath().$templateRootFolder;
		return file_get_contents($codeTemplateRootPath.'formErrors.htmlt');
	}

	public function generateLayout($templateRootFolder) {
		return $this->renderTemplate($templateRootFolder.'default.htmlt', array('extension' => $this->extension));
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

	public function generateLocallangCsh(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Resources/Private/Language/locallang_csh.xmlt', array('extension' => $this->extension, 'domainObject' => $domainObject));
	}
	
	public function generatePrivateResourcesHtaccess() {
		return $this->renderTemplate('Resources/Private/htaccess.t', array());
	}

	public function generateTCA(Tx_ExtbaseKickstarter_Domain_Model_DomainObject $domainObject) {
		return $this->renderTemplate('Configuration/TCA/domainObject.phpt', array('extension' => $this->extension, 'domainObject' => $domainObject));
	#public function generateTCA() {
		#return $this->renderTemplate('Configuration/Tca.phpt', array('extension' => $this->extension));
	}
	
	public function generateYamlSettings() {
		return $this->renderTemplate('Configuration/Kickstarter/settings.yamlt', array('extension' => $this->extension));
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
	 * @param string $extensionDirectory
	 * @param string $classType
	 * @return string
	 */	
	public static function getFolderForClassFile($extensionDirectory,$classType,$createDirIfNotExist=true){
		$classPath = '';
		switch ($classType) {
			case 'Model'		:	$classPath = 'Classes/Domain/Model/';
									break;
								
			case 'Controller'	:	$classPath = 'Classes/Controller/';
									break;					
								
			case 'Repository'	:	$classPath = 'Classes/Domain/Repository/';
									break;					
		}
		if(!empty($classPath)){
			if(!is_dir($extensionDirectory . $classPath) && $createDirIfNotExist){
				t3lib_div::mkdir_deep($extensionDirectory, $classPath);
			}
			if(!is_dir($extensionDirectory . $classPath) && $createDirIfNotExist){
				throw new Exception('folder could not be created:'.$extensionDirectory . $classPath);
			}
			return $extensionDirectory . $classPath;
		}
		else throw new Exception('Unexpected classPath:'.$classPath);
	}
	
	/**
	 * wrapper for t3lib_div::writeFile
	 * checks for overwrite settings
	 * 
	 * @param string $targetFile the path and filename of the targetFile
	 * @param string $fileContents
	 */
	protected function writeFile($targetFile,$fileContents){
		if($this->roundTripEnabled){
			$overWriteMode = Tx_ExtbaseKickstarter_Service_RoundTrip::getOverWriteSettingForPath($targetFile,$this->extension);
			//t3lib_div::devlog($targetFile.'-'.$overWriteMode,'extbase_kickstarter');
			if($overWriteMode == -1){
				return; // skip file creation
			}
			if($overWriteMode == 1 && strpos($targetFile,'Classes')===false){
					// classes are merged by the class builder
				$fileContents = $this->insertSplitToken($targetFile,$fileContents);
			}
			else if(file_exists($targetFile) && $overWriteMode == 2){
				// keep the existing file
				return;
			}
		}

		if(empty($fileContents)){
			t3lib_div::devLog('No file content! File ' . $targetFile . 'could not be created', 'extbase_kickstarter',0,$this->settings);
		}
		$success = t3lib_div::writeFile($targetFile, $fileContents);
		if(!$success){
			throw new Exception('File ' . $targetFile . 'could not be created!');
		}
	}
	
	protected function insertSplitToken($targetFile,$fileContents){
		$customFileContent = '';
		if(file_exists($targetFile)){
			// merge the files means append everything behind the split token
			$existingFileContent = file_get_contents($targetFile);
			$fileParts = explode(Tx_ExtbaseKickstarter_Service_RoundTrip::SPLIT_TOKEN,$existingFileContent);
			if(count($fileParts) == 2){
				$customFileContent = str_replace('?>','',$fileParts[1]);
			}
		}
		if(strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)) == 'php'){
			$fileContents = str_replace('?>','',$fileContents);
			$fileContents .= Tx_ExtbaseKickstarter_Service_RoundTrip::SPLIT_TOKEN;
		}
		else if(strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)) == 'xml'){
			$fileContents = Tx_ExtbaseKickstarter_Service_RoundTrip::mergeLocallangXml($targetFile,$fileContents);
		}
		else {
			$fileContents .= "\n".Tx_ExtbaseKickstarter_Service_RoundTrip::SPLIT_TOKEN;
		}

		$fileContents .= rtrim($customFileContent);

		if(strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)) == 'php'){
			$fileContents .=  "\n?>";
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
	protected function upload_copy_move($sourceFile,$targetFile){
		$overWriteMode = Tx_ExtbaseKickstarter_Service_RoundTrip::getOverWriteSettingForPath($targetFile,$this->extension);
		if($overWriteMode === -1){
			// skip creation
			return;
		}
		if(!file_exists($targetFile) || ($this->roundTripEnabled && $overWriteMode < 2)){
			t3lib_div::upload_copy_move($sourceFile,$targetFile);
		}
	}
	
	
}

?>