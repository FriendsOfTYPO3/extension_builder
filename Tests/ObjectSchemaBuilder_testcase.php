<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
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

require_once('BaseTestCase.php');
class Tx_ExtbaseKickstarter_ObjectSchemaBuilder_testcase extends Tx_ExtbaseKickstarter_BaseTestCase { //extends Tx_Extbase_Base_testcase {

	protected $objectSchemaBuilder;
	
	public function setUp() {
		$this->objectSchemaBuilder = $this->getMock($this->buildAccessibleProxy('Tx_ExtbaseKickstarter_ObjectSchemaBuilder'), array('dummy'));
	}
	/**
	 * @test
	 */
	public function conversionExtractsExtensionProperties() {

		$description = 'My cool fancy description';
		$name = 'ExtName';
		$extensionKey = 'EXTKEY';
		$state = 1;

		$input = array(
		    'properties' => array(
			'description' => $description,
			'extensionKey' => $extensionKey,
			'name' => $name,
			'state' => $state
		    )
		    );

		$extension = new Tx_ExtbaseKickstarter_Domain_Model_Extension();
		$extension->setDescription($description);
		$extension->setName($name);
		$extension->setExtensionKey($extensionKey);
		$extension->setState($state);


		$actual = $this->objectSchemaBuilder->build($input);
		$this->assertEquals($actual, $extension, 'Extension properties were not extracted.');
	}

	/**
	 * @test
	 */
	public function conversionExtractsPersons() {
		$this->markTestIncomplete('Persons not supported');

	}


	/**
	 * @test
	 */
	public function conversionExtractsSingleDomainObjectMetadata() {
		$name = 'MyDomainObject';
		$description = 'My long domain object description';

		$input = array(
			'name' => $name,
			'objectsettings' => array(
				'description' => $description,
				'aggregateRoot' => TRUE,
				'type' => 'Entity'
			),
			'propertyGroup' => array(
				'properties' => array(
					0 => array(
						'propertyName' => 'name',
						'propertyType' => 'String'
					),
					1 => array(
						'propertyName' => 'type',
						'propertyType' => 'Integer'
					)
				)
			    ),
			'relationGroup' => array()
		    );
		$expected = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject();
		$expected->setName($name);
		$expected->setDescription($description);
		$expected->setEntity(TRUE);
		$expected->setAggregateRoot(TRUE);

		$property0 = new Tx_ExtbaseKickstarter_Domain_Model_Property_StringProperty();
		$property0->setName('name');
		$property1 = new Tx_ExtbaseKickstarter_Domain_Model_Property_IntegerProperty();
		$property1->setName('type');
		$expected->addProperty($property0);
		$expected->addProperty($property1);

		$actual = $this->objectSchemaBuilder->_call('buildDomainObject', $input);
		$this->assertEquals($actual, $expected, 'Domain Object not built correctly.');
	}
}