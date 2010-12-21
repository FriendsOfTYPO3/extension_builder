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

class Tx_ExtbaseKickstarter_RoundTripServiceTest extends Tx_ExtbaseKickstarter_BaseRoundTripTestCase {
	
	function setUp(){
		parent::setUp();
	}
	
	/**
	 * Write a simple model class for a non aggregate root domain obbject
	 * @test
	 */
	function relatedMethodsReflectRenamingProperty(){
		// create an "old" domainObject
		$domainObject = $this->buildDomainObject('Dummy');
		$property = new Tx_ExtbaseKickstarter_Domain_Model_Property_StringProperty();
		$property->setName('prop1');
		$uniqueIdentifier1 = md5(microtime() . 'prop1');
		$property->setUniqueIdentifier($uniqueIdentifier1);
		$domainObject->addProperty($property);
		$uniqueIdentifier2 = md5(microtime() . 'model');
		$domainObject->setUniqueIdentifier($uniqueIdentifier2);
		
		$this->roundTripService->_set('oldDomainObjects',array($domainObject->getUniqueIdentifier()=>$domainObject));
		
		// create an "old" class object. 
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject);
		
		// Check that the getter/methods exist
		$this->assertTrue($modelClassObject->methodExists('getProp1'));
		$this->assertTrue($modelClassObject->methodExists('setProp1'));
		
		// we have to modifiy the method bodies, otherwise the roundtrip service 
		// removes them and let them rebuild from ClassBuilder (see comment in line 388)
		$getterMethod =  $modelClassObject->getMethod('getProp1');
		$getterMethod->setBody('if($dummy) return $this->prop1;');
		$modelClassObject->setMethod($getterMethod);
		
		$setterMethod =  $modelClassObject->getMethod('setProp1');
		$setterMethod->setBody('if($dummy)$this->prop1 = $prop1;');
		$modelClassObject->setMethod($setterMethod);
		
		// set the class object manually, this is usually parsed from an existing class file
		$this->roundTripService->_set('classObject',$modelClassObject);
		
		// build a new domain object with the same unique identifiers
		$newDomainObject = $this->buildDomainObject('Dummy');
		$property = new Tx_ExtbaseKickstarter_Domain_Model_Property_BooleanProperty();
		$property->setName('newProp1Name');
		$property->setUniqueIdentifier($uniqueIdentifier1);
		$property->setRequired(TRUE);
		$newDomainObject->addProperty($property);
		$newDomainObject->setUniqueIdentifier($uniqueIdentifier2);
		
		// now the slass object should be updated
		$this->roundTripService->_call('updateModelClassProperties',$domainObject,$newDomainObject);
		
		$classObject = $this->roundTripService->_get('classObject');
		$this->assertTrue($classObject->methodExists('getNewProp1Name'));
		$this->assertTrue($classObject->methodExists('setNewProp1Name'));
		
		t3lib_div::rmdir($this->extension->getExtensionDir(),true);
	}

}

?>