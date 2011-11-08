<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nico de Haen
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


class Tx_ExtensionBuilder_ObjectSchemaBuilderTest extends Tx_ExtensionBuilder_Tests_BaseTest {


	/**
	 * @test
	 */
	public function domainObjectHasExpectedProperties() {
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
						'propertyType' => 'String',
						'propertyIsRequired' => 'true'
					),
					1 => array(
						'propertyName' => 'type',
						'propertyType' => 'Integer'
					)
				)
			),
			'actionGroup' => array(
				'customActions' => array('test'),
				'list' => 1,
			),
			'relationGroup' => array()
		);

		$expected = new Tx_ExtensionBuilder_Domain_Model_DomainObject();
		$expected->setName($name);
		$expected->setDescription($description);
		$expected->setEntity(TRUE);
		$expected->setAggregateRoot(TRUE);

		$property0 = new Tx_ExtensionBuilder_Domain_Model_DomainObject_StringProperty('name');
		$property0->setRequired(TRUE);
		$property1 = new Tx_ExtensionBuilder_Domain_Model_DomainObject_IntegerProperty('type');
		$expected->addProperty($property0);
		$expected->addProperty($property1);

		$testAction = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
		$testAction->setName('test');
		$expected->addAction($testAction);

		$listAction = t3lib_div::makeInstance('Tx_ExtensionBuilder_Domain_Model_DomainObject_Action');
		$listAction->setName('list');
		$expected->addAction($listAction);

		$actual = Tx_ExtensionBuilder_Service_ObjectSchemaBuilder::build($input);

		$this->assertEquals($actual, $expected, 'Domain Object not built correctly.');

	}
}

?>
