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

class Tx_ExtbaseKickstarter_Service_ClassBuilder_testcase extends Tx_ExtbaseKickstarter_BaseRoundTripTestCase {
	
	function setUp(){
		parent::setUp();
	}
	
	/**
	* 
	*/
	public function classBuilderGeneratesSetterMethodForSimpleProperty() {
		$domainObject = $this->buildDomainObject('Blog',true,true);

		$property0 = new Tx_ExtbaseKickstarter_Domain_Model_Property_StringProperty();
		$property0->setName('name');
		$property0->setRequired(TRUE);
		$domainObject->addProperty($property0);
		
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject);
		
		$this->assertTrue(is_object($modelClassObject),'No model class object');
		$this->assertTrue($modelClassObject->methodExists('setName'),'No method: setName');
		
		$setNameMethod = $modelClassObject->getMethod('setName');
		$parameters = $setNameMethod->getParameters();
		$this->assertEquals(count($parameters),1);
		$firstParameter = array_shift($parameters);
		$this->assertEquals($firstParameter->getName(),'name');
	}
	

	/**
	* 
	*/
	
	public function classBuilderGeneratesGetterMethodForSimpleProperty() {
		
		$domainObject = $this->buildDomainObject('Blog',true,true);
		$property0 = new Tx_ExtbaseKickstarter_Domain_Model_Property_StringProperty();
		$property0->setName('name');
		$property0->setRequired(TRUE);
		$domainObject->addProperty($property0);
		
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject);
		$this->assertTrue($modelClassObject->methodExists('getName'),'No method: getName');
	
	}
	
	/**
	 * 
	 * 
	 */
	public function classBuilderGeneratesIsMethodForBooleanProperty() {
		
		$domainObject = $this->buildDomainObject('Dummy',true,true);
		$property = new Tx_ExtbaseKickstarter_Domain_Model_Property_BooleanProperty();
		$property->setName('blue');
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject);
		$this->assertTrue($modelClassObject->methodExists('isBlue'),'No method: isBlue');
	
	}
	
	/**
	* 
	*/
	public function classBuilderGeneratesAddMethodForRelationProperty() {
		$domainObject1 = $this->buildDomainObject('Blog',true,true);
		$domainObject2 = $this->buildDomainObject('Post');
		
		$relationProperty = new Tx_ExtbaseKickstarter_Domain_Model_Property_Relation_ManyToManyRelation();
		$relationProperty->setName('posts');
		$relationProperty->setForeignClass($domainObject2);
		$domainObject1->addProperty($relationProperty);
		
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject1);
		
		$this->assertTrue($modelClassObject->methodExists('addPost'),'No method: addPost');
		
		$setNameMethod = $modelClassObject->getMethod('addPost');
		$this->assertEquals($setNameMethod->getTagsValues('param'),'Tx_Dummy_Domain_Model_Post $post');
		
		$parameters = $setNameMethod->getParameters();
		$this->assertEquals(count($parameters),1);
		$firstParameter = array_shift($parameters);
		$this->assertEquals($firstParameter->getName(),'post');
		$this->assertEquals($firstParameter->getTypeHint(),'Tx_Dummy_Domain_Model_Post');
		
	}
	
	/**
	* 
	*/
	public function classBuilderGeneratesRemoveMethodForRelationProperty() {
		$domainObject1 = $this->buildDomainObject('Blog',true,true);
		$domainObject2 = $this->buildDomainObject('Post');
		
		$relationProperty = new Tx_ExtbaseKickstarter_Domain_Model_Property_Relation_ManyToManyRelation();
		$relationProperty->setName('posts');
		$relationProperty->setForeignClass($domainObject2);
		$domainObject1->addProperty($relationProperty);
		
		$modelClassObject = $this->classBuilder->generateModelClassObject($domainObject1);
		
		$this->assertTrue($modelClassObject->methodExists('removePost'),'No method: removePost');
		
		$setNameMethod = $modelClassObject->getMethod('removePost');
		$parameters = $setNameMethod->getParameters();
		$this->assertEquals(count($parameters),1);
		$firstParameter = array_shift($parameters);
		$this->assertEquals($firstParameter->getName(),'postToRemove');
		$this->assertEquals($firstParameter->getTypeHint(),'Tx_Dummy_Domain_Model_Post');
	}
	
	
}

?>