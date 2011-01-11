<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
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

abstract class Tx_ExtbaseKickstarter_Tests_BaseTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	var $modelClassDir = 'Classes/Domain/Model/';

	function setUp($settingFile = ''){

		$this->extension = $this->getMock('Tx_ExtbaseKickstarter_Domain_Model_Extension',array('getExtensionDir'));
		$extensionKey = 'dummy';
		//$dummyExtensionDir = PATH_typo3conf.'ext/extbase_kickstarter/Tests/Examples/'.$extensionKey.'/';
		vfsStream::setup('testDir');
		$dummyExtensionDir = vfsStream::url('testDir').'/';

		$this->extension->setExtensionKey($extensionKey);
		$this->extension->expects(
			$this->any())
				->method('getExtensionDir')
				->will($this->returnValue($dummyExtensionDir));

		$yamlParser = new Tx_ExtbaseKickstarter_Utility_SpycYAMLParser();
		$settings = $yamlParser->YAMLLoadString(file_get_contents(PATH_typo3conf.'ext/extbase_kickstarter/Tests/Examples/Settings/settings1.yaml'));
		$this->extension->setSettings($settings);

		$this->classParser = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Utility_ClassParser');
		$this->roundTripService =  $this->getMock($this->buildAccessibleProxy('Tx_ExtbaseKickstarter_Service_RoundTrip'),array('dummy'));
		$this->classBuilder = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_ClassBuilder');
		$this->templateParser = $this->getMock($this->buildAccessibleProxy('Tx_Fluid_Core_Parser_TemplateParser'),array('dummy'));
		$this->codeGenerator = $this->getMock($this->buildAccessibleProxy('Tx_ExtbaseKickstarter_Service_CodeGenerator'),array('dummy'));

		if (class_exists('Tx_Extbase_Object_ObjectManager')) {
			$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
			//parent::runBare(); causes a memory exhausted error??
			$this->codeGenerator->injectObjectManager($this->objectManager);
			$this->templateParser->injectObjectManager($this->objectManager);

		} else{
			$this->objectManager = new Tx_Fluid_Compatibility_ObjectManager();
			$this->codeGenerator->_set('objectManager',$this->objectManager);
			$this->templateParser->_set('objectManager',$this->objectManager);
		}

		$this->roundTripService->injectClassParser($this->classParser);
		$this->roundTripService->initialize($this->extension);
	
		$this->classBuilder->injectRoundtripService($this->roundTripService);
		$this->classBuilder->initialize($this->extension);

		$this->codeGenerator->injectTemplateParser($this->templateParser);
		$this->codeGenerator->injectClassBuilder($this->classBuilder);
		$this->codeGenerator->settings= array('enableRoundtrip'=>'1');

	}



	/**
	 * Helper function
	 * @param $name
	 * @param $entity
	 * @param $aggregateRoot
	 * @return object Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	protected function buildDomainObject($name, $entity = false, $aggregateRoot = false){
		$domainObject = $this->getMock($this->buildAccessibleProxy('Tx_ExtbaseKickstarter_Domain_Model_DomainObject'),array('dummy'));
		$domainObject->setExtension($this->extension);
		$domainObject->setName($name);
		$domainObject->setEntity($entity);
		$domainObject->setAggregateRoot($aggregateRoot);
		if($aggregateRoot){
			$defaultActions = array('list','show','create','update','delete');
			foreach($defaultActions as $actionName){
				$action = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Domain_Model_DomainObject_Action');
				$action->setName($actionName);
				if($actionName == 'deleted'){
					$action->setNeedsTemplate = false;
				}
				$domainObject->addAction($action);
			}
		}
		return $domainObject;
	}

	/**
	 * Builds an initial class file to test parsing and modifiying of existing classes
	 * 
	 * This class file is generated based on the CodeTemplates
	 * @param string $modelName
	 */
	function generateInitialModelClassFile($modelName){
		$domainObject = $this->buildDomainObject($modelName);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);
		$modelClassDir = 'Classes/Domain/Model/';
		$result = t3lib_div::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');

		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		t3lib_div::writeFile($modelClassPath,$classFileContent);
	}

	function removeInitialModelClassFile($modelName){
		if(@file_exists($this->extension->getExtensionDir().$this->modelClassDir.$modelName . '.php')){
			//unlink($this->extension->getExtensionDir().$this->modelClassDir.$modelName . '.php');
		}
		$this->assertFalse(file_exists($this->extension->getExtensionDir().$this->modelClassDir. $modelName . '.php'),'Dummy files could not be removed:'.$this->extension->getExtensionDir().$this->modelClassDir. $modelName . '.php');
	}

}

?>