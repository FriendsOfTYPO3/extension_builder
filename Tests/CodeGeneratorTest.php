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

require_once('BaseRoundTripTestCase.php');

class Tx_ExtbaseKickstarter_CodeGeneratorTest extends Tx_ExtbaseKickstarter_BaseRoundTripTestCase {
	
	function setUp(){
		parent::setUp();
	}
	
	/**
	 * Write a simple model class for a non aggregate root domain obbject
	 * @test
	 */
	function writeSimpleModelClassFromDomainObject(){
		$domainObject = $this->buildDomainObject('Dummy');
		$property = new Tx_ExtbaseKickstarter_Domain_Model_Property_BooleanProperty();
		$property->setName('blue');
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject);
		
		$classFileContent = $this->codeGenerator->_call('renderTemplate','Partials/Classes/class.phpt', array('domainObject' => $domainObject, 'extension' => $this->extension,'classObject'=>$modelClassObject));
		$modelClassPath =  'Classes/Domain/Model/';
		$result = t3lib_div::mkdir_deep($this->extension->getExtensionDir(),$modelClassPath);
		$modelClassPath = $this->extension->getExtensionDir().$modelClassPath;
		$this->assertTrue(is_dir($modelClassPath),$result);
		t3lib_div::writeFile($modelClassPath . $domainObject->getName() . '.php', $classFileContent);
		$this->assertFileExists($modelClassPath. $domainObject->getName() . '.php');
	}
	
	/**
	 * This test is definitely too generic, since it creates the required classes 
	 * with a whole codeGenerator->build call
	 * 
	 * @test
	 */
	function writeAggregateRootClassesFromDomainObject(){
		$domainObject = $this->buildDomainObject('Dummy',true,true);
		$property = new Tx_ExtbaseKickstarter_Domain_Model_Property_BooleanProperty();
		$property->setName('blue');
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		
		$this->extension->addDomainObject($domainObject);
		
		$this->codeGenerator->build($this->extension);
		
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Domain/Model/'. $domainObject->getName() . '.php');
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Domain/Repository/'. $domainObject->getName() . 'Repository.php');
		$this->assertFileExists($this->extension->getExtensionDir().'Classes/Controller/'. $domainObject->getName() . 'Controller.php');
		
		t3lib_div::rmdir($this->extension->getExtensionDir(),true);
	}

}

?>