<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
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


class ObjectSchemaBuilderTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Service\ObjectSchemaBuilder
	 */
	protected $objectSchemaBuilder = NULL;

	protected function setUp() {
		$this->objectSchemaBuilder = $this->getMock($this->buildAccessibleProxy('EBT\\ExtensionBuilder\\Service\\ObjectSchemaBuilder'), array('dummy'));
		$concreteConfigurationManager = $this->getMock($this->buildAccessibleProxy('TYPO3\\CMS\Extbase\\Configuration\\BackendConfigurationManager'));
		$typoScriptService = new \TYPO3\CMS\Extbase\Service\TypoScriptService ();
		$concreteConfigurationManager->_set('typoScriptService',$typoScriptService);
		$this->configurationManager = $this->getMock($this->buildAccessibleProxy('EBT\\ExtensionBuilder\\Configuration\\ConfigurationManager'),array('getExtbaseClassConfiguration'));
		$this->configurationManager->_set('concreteConfigurationManager',$concreteConfigurationManager);
		$this->objectSchemaBuilder->injectConfigurationManager($this->configurationManager);
	}


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
						'propertyIsRequired' => TRUE
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

		$expected = new \EBT\ExtensionBuilder\Domain\Model\DomainObject();
		$expected->setName($name);
		$expected->setDescription($description);
		$expected->setEntity(TRUE);
		$expected->setAggregateRoot(TRUE);

		$property0 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
		$property0->setRequired(TRUE);
		$property1 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\IntegerProperty('type');
		$expected->addProperty($property0);
		$expected->addProperty($property1);

		$testAction = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Action');
		$testAction->setName('test');
		$expected->addAction($testAction);

		$listAction = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Action');
		$listAction->setName('list');
		$expected->addAction($listAction);

		$actual = $this->objectSchemaBuilder->build($input);
		$this->assertEquals($actual, $expected, 'Domain Object not built correctly.');

	}

	/**
	 * @test
	 */
	public function domainObjectHasExpectedRelations() {
		$name = 'MyDomainObject';
		$description = 'My long domain object description';
		$className = '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser';

		$input = array(
			'name' => $name,
			'objectsettings' => array(
				'description' => $description,
				'aggregateRoot' => TRUE,
				'type' => 'Entity'
			),
			'relationGroup' => array(
				'relations' => array(
					0 => array(
						'relationName' => 'relation 1',
						'relationType' => 'manyToMany',
						'propertyIsExcludeField' => FALSE,
						'foreignRelationClass' => $className
					),
					1 => array(
						'relationName' => 'relation 2',
						'relationType' => 'manyToMany',
						'propertyIsExcludeField' => FALSE,
						'foreignRelationClass' => $className
					),
				)
			),
		);

		$expected = new \EBT\ExtensionBuilder\Domain\Model\DomainObject();
		$expected->setName($name);
		$expected->setDescription($description);
		$expected->setEntity(TRUE);
		$expected->setAggregateRoot(TRUE);

		$relation1 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ManyToManyRelation('relation 1');
		$relation1->setForeignClassName($className);
		$relation1->setRelatedToExternalModel(TRUE);
		$relation1->setExcludeField(FALSE);
		$relation1->setForeignDatabaseTableName('fe_users');
		$relation2 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ManyToManyRelation('relation 2');
		$relation2->setForeignClassName($className);
		$relation2->setRelatedToExternalModel(TRUE);
		$relation2->setExcludeField(FALSE);
		$relation2->setForeignDatabaseTableName('fe_users');
		$expected->addProperty($relation1);
		$expected->addProperty($relation2);

		$extbaseConfiguration = array(
			'tableName' => 'fe_users'
		);
		$this->configurationManager->expects($this->atLeastOnce())->method('getExtbaseClassConfiguration')->with($className)->will($this->returnValue($extbaseConfiguration));
		$actual = $this->objectSchemaBuilder->build($input);
		$this->assertEquals($actual, $expected, 'Domain Object not built correctly.');

	}

	/**
	 * @test
	 */
	public function manyToManyRelationReturnsCorrectRelationTable() {
		$name = 'MyDomainObject';
		$description = 'My long domain object description';
		$relationName = 'Relation1';
		$className = '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser';

		$input = array(
			'name' => $name,
			'objectsettings' => array(
				'description' => $description,
				'aggregateRoot' => TRUE,
				'type' => 'Entity'
			),
			'relationGroup' => array(
				'relations' => array(
					0 => array(
						'relationName' => $relationName,
						'relationType' => 'manyToMany',
						'propertyIsExcludeField' => FALSE,
						'foreignRelationClass' => $className
					),
				)
			),
		);

		$extbaseConfiguration = array(
			'tableName' => 'fe_users'
		);
		$this->configurationManager->expects($this->atLeastOnce())->method('getExtbaseClassConfiguration')->with($className)->will($this->returnValue($extbaseConfiguration));

		$domainObject = $this->objectSchemaBuilder->build($input);
		$dummyExtension = new \EBT\ExtensionBuilder\Domain\Model\Extension();
		$dummyExtension->setExtensionKey('dummy');
		$domainObject->setExtension($dummyExtension);

		$relation = $domainObject->getPropertyByName($relationName);

		$this->assertTrue($relation->getUseMMTable(), 'ManyToMany Relation->getUseMMTable() returned FALSE.');

		$this->assertEquals('tx_dummy_mydomainobject_frontenduser_mm',$relation->getRelationTableName());

		$relation->setUseExtendedRelationTableName(TRUE);

		$this->assertEquals('tx_dummy_mydomainobject_relation1_frontenduser_mm',$relation->getRelationTableName());

		$this->assertEquals('fe_users',$relation->getForeignDatabaseTableName());

	}

	/**
	 * @test
	 */
	public function anyToManyRelationHasExpectedProperties() {
		$domainObjectName1 = 'DomainObject1';
		$domainObjectName2 = 'DomainObject2';
		$description = 'My long domain object description';
		$relationName = 'Relation1';
		$input = array(
			'name' => $domainObjectName1,
			'objectsettings' => array(
				'description' => $description,
				'aggregateRoot' => TRUE,
				'type' => 'Entity'
			),
			'relationGroup' => array(
				'relations' => array(
					0 => array(
						'relationName' => $relationName,
						'relationType' => 'zeroToMany',
						'propertyIsExcludeField' => FALSE,
					),
				)
			),
		);

		$domainObject1 = $this->objectSchemaBuilder->build($input);
		$input['name'] = $domainObjectName2;
		$domainObject2 = $this->objectSchemaBuilder->build($input);

		$dummyExtension = new \EBT\ExtensionBuilder\Domain\Model\Extension();
		$dummyExtension->setExtensionKey('dummy');
		$domainObject1->setExtension($dummyExtension);
		$domainObject2->setExtension($dummyExtension);

		$relation = $domainObject1->getPropertyByName($relationName);

		$relation->setForeignModel($domainObject2);

		$this->assertFalse($relation->getUseMMTable(), 'ZeroToMany Relation->getUseMMTable() returned TRUE.');

		$this->assertEquals('tx_dummy_domain_model_domainobject2',$relation->getForeignDatabaseTableName());

	}

	/**
	 * Find the mapped table for a foreign related class
	 * @test
	 */
	public function anyToManyRelationToForeignClassBuildsCorrectRelationTableName() {
		$domainObjectName1 = 'DomainObject1';
		$description = 'My long domain object description';
		$relationName = 'Relation1';
		$className = '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser';

		$input = array(
			'name' => $domainObjectName1,
			'objectsettings' => array(
				'description' => $description,
				'aggregateRoot' => TRUE,
				'type' => 'Entity'
			),
			'relationGroup' => array(
				'relations' => array(
					0 => array(
						'relationName' => $relationName,
						'relationType' => 'zeroToMany',
						'propertyIsExcludeField' => FALSE,
						'foreignRelationClass' => $className
					),
				)
			),
		);

		$extbaseConfiguration = array(
			'tableName' => 'fe_users'
		);
		$this->configurationManager->expects($this->atLeastOnce())->method('getExtbaseClassConfiguration')->with($className)->will($this->returnValue($extbaseConfiguration));

		$domainObject1 = $this->objectSchemaBuilder->build($input);

		$relation = $domainObject1->getPropertyByName($relationName);

		$this->assertFalse($relation->getUseMMTable(), 'ZeroToMany Relation->getUseMMTable() returned TRUE.');

		$this->assertEquals('fe_users',$relation->getForeignDatabaseTableName());

	}
}
