<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
 *
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
 *
 * @author Nico de Haen
 *
 */
class Tx_ExtensionBuilder_CodeGeneratorFunctionTest extends Tx_ExtensionBuilder_Tests_BaseTest {

	function setUp(){
		parent::setUp();
	}

	function tearDown(){
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function checkRequirements(){
		$this->assertTrue(class_exists(vfsStream),'Requirements not fulfilled: vfsStream is needed for file operation tests. Please make sure you are using at least phpunit Version 3.5.6');
	}

	/**
	 * Write a simple model class for a non aggregate root domain object with one boolean property
	 *
	 * @depends checkRequirements
	 * @test
	 */
	function writeModelClassWithBooleanProperty(){
		$modelName = 'ModelCgt1';
		$propertyName = 'blue';
		$domainObject = $this->buildDomainObject($modelName, TRUE);
		$property = new Tx_ExtensionBuilder_Domain_Model_DomainObject_BooleanProperty($propertyName);
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);
		$modelClassDir =  'Classes/Domain/Model/';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');

		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getFullQualifiedClassName();
		if(!class_exists($className)) {
			include_once($modelClassPath);
		}
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);
		$reflection = new ReflectionClass($className);
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$this->assertTrue($reflection->hasMethod('is' . ucfirst($propertyName)),'isMethod was not generated');
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$parameters = $setterMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), $propertyName,'Wrong parameter name in setter method');
	}

	/**
	 * Write a simple model class for a non aggregate root domain object with one string property
	 *
	 * @test
	 */
	function writeModelClassWithStringProperty(){
		$modelName = 'ModelCgt2';
		$propertyName = 'title';
		$domainObject = $this->buildDomainObject($modelName);
		$property = new Tx_ExtensionBuilder_Domain_Model_DomainObject_StringProperty($propertyName);
		//$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);

		$modelClassDir =  'Classes/Domain/Model/';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');

		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getFullQualifiedClassName();
		if(!class_exists($className)) {
			include($modelClassPath);
		}
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);

		$reflection = new ReflectionClass($className);
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$this->assertFalse($reflection->hasMethod('is' . ucfirst($propertyName)),'isMethod should not be generated');
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$parameters = $setterMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), $propertyName,'Wrong parameter name in setter method');

	}

	/**
	 * Write a simple model class for a non aggregate root domain object with one to one relation
	 *
	 * @test
	 */
	function writeModelClassWithZeroToOneRelation(){
		$modelName = 'ModelCgt3';
		$relatedModelName = 'RelatedModel';
		$propertyName = 'relName';
		$domainObject = $this->buildDomainObject($modelName);
		$relatedDomainObject = $this->buildDomainObject($relatedModelName);
		$relation = new Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ZeroToOneRelation($propertyName);
		$relation->setForeignModel($relatedDomainObject);
		$domainObject->addProperty($relation);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,TRUE);

		$modelClassDir =  'Classes/Domain/Model/';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');

		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Class Content','extension_builder',0,array('c'=>$classFileContent, 'path' => $absModelClassDir));
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getFullQualifiedClassName();
		if(!class_exists($className)) {
			include($modelClassPath);
		}
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);

		$reflection = new Tx_ExtensionBuilder_Reflection_ClassReflection($className);
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$this->assertTrue($setterMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $setterMethod->getTagValues('param');
		$this->assertEquals(0, strpos($paramTagValues[0],$relatedDomainObject->getFullQualifiedClassName()),'Wrong param tag:'.$paramTagValues[0]);

		$parameters = $setterMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), $propertyName,'Wrong parameter name in setter method');
		$this->assertEquals($parameter->getTypeHint(), $relatedDomainObject->getFullQualifiedClassName(),'Wrong type hint for setter parameter:'.$parameter->getTypeHint());
	}

	/**
	 * Write a simple model class for a non aggregate root domain object with zero to many relation
	 *
	 * @test
	 */
	function writeModelClassWithZeroToManyRelation(){
		$modelName = 'ModelCgt4';
		$relatedModelName = 'RelatedModel';
		$propertyName = 'relNames';
		$domainObject = $this->buildDomainObject($modelName);
		$relatedDomainObject = $this->buildDomainObject($relatedModelName);
		$relation = new Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ZeroToManyRelation($propertyName);
		$relation->setForeignModel($relatedDomainObject);
		$domainObject->addProperty($relation);

		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);

		$modelClassDir =  'Classes/Domain/Model/';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');

		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getFullQualifiedClassName();
		if(!class_exists($className)) {
			include($modelClassPath);
		}
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);

		$reflection = new Tx_ExtensionBuilder_Reflection_ClassReflection($className);
		$this->assertTrue($reflection->hasMethod('add' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName))),'Add method was not generated');
		$this->assertTrue($reflection->hasMethod('remove' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName))),'Remove method was not generated');
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');

		//checking methods
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$this->assertTrue($setterMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $setterMethod->getTagValues('param');
		$this->assertEquals(0, strpos($paramTagValues[0],'\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $relatedDomainObject->getFullQualifiedClassName()),'Wrong param tag:'.$paramTagValues[0]);

		$parameters = $setterMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), $propertyName,'Wrong parameter name in setter method');

		$addMethod = $reflection->getMethod('add' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName)));
		$this->assertTrue($addMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $addMethod->getTagValues('param');
		$this->assertEquals(0, strpos($paramTagValues[0],$relatedDomainObject->getFullQualifiedClassName()),'Wrong param tag:'.$paramTagValues[0]);

		$parameters = $addMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in add method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName),'Wrong parameter name in add method');
		$this->assertEquals($parameter->getTypeHint(), $relatedDomainObject->getFullQualifiedClassName(),'Wrong type hint for add method parameter:'.$parameter->getTypeHint());

		$removeMethod = $reflection->getMethod('remove' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName)));
		$this->assertTrue($removeMethod->isTaggedWith('param'),'No param tag set for remove method');
		$paramTagValues = $removeMethod->getTagValues('param');
		$this->assertEquals(0, strpos($paramTagValues[0],$relatedDomainObject->getFullQualifiedClassName()),'Wrong param tag:'.$paramTagValues[0]);

		$parameters = $removeMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in remove method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName).'ToRemove','Wrong parameter name in remove method');
		$this->assertEquals($parameter->getTypeHint(), $relatedDomainObject->getFullQualifiedClassName(),'Wrong type hint for remove method parameter:'.$parameter->getTypeHint());
	}

	/**
	 * Write a simple model class for a non aggregate root domain object with one to one relation
	 *
	 * @depends checkRequirements
	 * @test
	 */
	function writeModelClassWithManyToManyRelation(){
		$modelName = 'ModelCgt5';
		$relatedModelName = 'RelatedModel';
		$propertyName = 'relNames';
		$domainObject = $this->buildDomainObject($modelName);
		$relatedDomainObject = $this->buildDomainObject($relatedModelName);
		$relation = new Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ManyToManyRelation($propertyName);
		$relation->setForeignModel($relatedDomainObject);
		$relation->setInlineEditing(false);
		$domainObject->addProperty($relation);

		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject,$this->extension);

		$modelClassDir =  'Classes/Domain/Model/';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extension->getExtensionDir(),$modelClassDir);
		$absModelClassDir = $this->extension->getExtensionDir().$modelClassDir;
		$this->assertTrue(is_dir($absModelClassDir),'Directory ' . $absModelClassDir . ' was not created');

		$modelClassPath =  $absModelClassDir . $domainObject->getName() . '.php';
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($modelClassPath,$classFileContent);
		$this->assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
		$className = $domainObject->getFullQualifiedClassName();
		if(!class_exists($className)) {
			include($modelClassPath);
		}
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);

		$reflection = new Tx_ExtensionBuilder_Reflection_ClassReflection($className);
		$this->assertTrue($reflection->hasMethod('add' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName))),'Add method was not generated');
		$this->assertTrue($reflection->hasMethod('remove' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName))),'Remove method was not generated');
		$this->assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)),'Getter was not generated');
		$this->assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)),'Setter was not generated');
		$this->assertTrue($reflection->hasMethod('initStorageObjects'),'initStorageObjects was not generated');

		//checking methods
		$setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
		$this->assertTrue($setterMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $setterMethod->getTagValues('param');
		$this->assertEquals(0, strpos($paramTagValues[0],'\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $relatedDomainObject->getFullQualifiedClassName()),'Wrong param tag:'.$paramTagValues[0]);

		$parameters = $setterMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in setter method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), $propertyName,'Wrong parameter name in setter method');

		$addMethod = $reflection->getMethod('add' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName)));
		$this->assertTrue($addMethod->isTaggedWith('param'),'No param tag set for setter method');
		$paramTagValues = $addMethod->getTagValues('param');
		$this->assertEquals(0, strpos($paramTagValues[0],$relatedDomainObject->getFullQualifiedClassName()),'Wrong param tag:'.$paramTagValues[0]);

		$parameters = $addMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in add method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName),'Wrong parameter name in add method');
		$this->assertEquals($parameter->getTypeHint(), $relatedDomainObject->getFullQualifiedClassName(),'Wrong type hint for add method parameter:'.$parameter->getTypeHint());

		$removeMethod = $reflection->getMethod('remove' . ucfirst(Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName)));
		$this->assertTrue($removeMethod->isTaggedWith('param'),'No param tag set for remove method');
		$paramTagValues = $removeMethod->getTagValues('param');
		$this->assertEquals(0, strpos($paramTagValues[0],$relatedDomainObject->getFullQualifiedClassName()),'Wrong param tag:'.$paramTagValues[0]);

		$parameters = $removeMethod->getParameters();
		$this->assertEquals(1, count($parameters),'Wrong parameter count in remove method');
		$parameter = current($parameters);
		$this->assertEquals($parameter->getName(), Tx_ExtensionBuilder_Utility_Inflector::singularize($propertyName).'ToRemove','Wrong parameter name in remove method');
		$this->assertEquals($parameter->getTypeHint(), $relatedDomainObject->getFullQualifiedClassName(),'Wrong type hint for remove method parameter:'.$parameter->getTypeHint());
	}



	/**
	 * Write a simple model class for a non aggregate root domain object
	 *
	 * @test
	 */
	function writeSimpleControllerClassFromDomainObject(){
		$domainObject = $this->buildDomainObject('ModelCgt6',true);
		$action = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
		$action->setName('list');
		$domainObject->addAction($action);
		$classFileContent = $this->codeGenerator->generateActionControllerCode($domainObject,$this->extension);
		$controllerClassDir =  'Classes/Controller/';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extension->getExtensionDir(),$controllerClassDir);
		$absControllerClassDir = $this->extension->getExtensionDir().$controllerClassDir;
		$this->assertTrue(is_dir($absControllerClassDir),'Directory ' . $absControllerClassDir . ' was not created');
		$controllerClassPath =  $absControllerClassDir . $domainObject->getName() . 'Controller.php';
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($controllerClassPath,$classFileContent);
		$this->assertFileExists($controllerClassPath,'File was not generated: ' . $controllerClassPath);
		$className = $domainObject->getControllerClassName();
		if(!class_exists($className)) {
			include($controllerClassPath);
		}
		$this->assertTrue(class_exists($className),'Class was not generated:'.$className);

	}

	/**
	 * This test is too generic, since it creates the required classes
	 * with a whole codeGenerator->build call
	 *
	 * 
	 * @test
	 */
	function writeAggregateRootClassesFromDomainObject(){
		$domainObject = $this->buildDomainObject('ModelCgt7',true,true);
		$property = new Tx_ExtensionBuilder_Domain_Model_DomainObject_BooleanProperty('blue');
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);

		$this->extension->addDomainObject($domainObject);

		$this->codeGenerator->build($this->extension);

		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Domain/Model/'. $domainObject->getName() . '.php');
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Domain/Repository/'. $domainObject->getName() . 'Repository.php');
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Controller/'. $domainObject->getName() . 'Controller.php');

	}

	/**
	 * @depends writeModelClassWithManyToManyRelation
	 * @depends writeAggregateRootClassesFromDomainObject
	 *
	 * TODO: A lot of more testing possible here (file content etc.) But this is in fact not a unit test anymore...
	 *
	 * @test
	 */
	function writeExtensionFiles(){
		$modelName = 'ModelCgt8';
		$relatedModelName = 'RelatedModel';
		$propertyName = 'relNames';
		$domainObject = $this->buildDomainObject($modelName,true,true);

		$relatedDomainObject = $this->buildDomainObject($relatedModelName,true);
		$relation = new Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ManyToManyRelation($propertyName);
		$relation->setForeignModel($relatedDomainObject);
		$relation->setInlineEditing(false);
		$domainObject->addProperty($relation);

		$this->extension->addDomainObject($domainObject);
		$this->extension->addDomainObject($relatedDomainObject);

		$plugin = new Tx_ExtensionBuilder_Domain_Model_Plugin();
		$plugin->setName('Test');
		$plugin->setKey('test');
		$this->extension->addPlugin($plugin);

		$this->codeGenerator->build($this->extension);

		$extensionDir = $this->extension->getExtensionDir();

		$extensionFiles = array('ext_emconf.php','ext_tables.php','ext_tables.sql','ext_localconf.php');
		foreach($extensionFiles as  $extensionFile){
			$this->assertFileExists($extensionDir.$extensionFile,'File was not generated: ' . $extensionFile);
		}

		$this->assertFileExists($extensionDir.'Configuration/TCA/'. $domainObject->getName() . '.php');
		$this->assertFileExists($extensionDir.'Configuration/ExtensionBuilder/settings.yaml');

		$this->assertFileExists($extensionDir.'Resources/Private/Language/locallang_db.xlf');
		$this->assertFileExists($extensionDir.'Resources/Private/Language/locallang.xlf');
		$this->assertFileExists($extensionDir.'Resources/Private/Partials/'. $domainObject->getName() .'/Properties.html');
		$this->assertFileExists($extensionDir.'Resources/Private/Partials/'. $domainObject->getName() .'/FormFields.html');
	}

}

?>
