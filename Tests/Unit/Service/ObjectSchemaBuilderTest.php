<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace EBT\ExtensionBuilder\Tests\Unit\Service;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\IntegerProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ManyToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ObjectSchemaBuilderTest extends BaseUnitTest
{
    protected ExtensionBuilderConfigurationManager $configurationManager;
    protected ObjectSchemaBuilder $objectSchemaBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectSchemaBuilder = $this->getAccessibleMock(ObjectSchemaBuilder::class, ['dummy']);

        $this->configurationManager = $this->getAccessibleMock(
            ExtensionBuilderConfigurationManager::class,
            ['getPersistenceTable']
        );
        $this->objectSchemaBuilder->injectConfigurationManager($this->configurationManager);
    }

    /**
     * @test
     */
    public function domainObjectHasExpectedProperties(): void
    {
        $name = 'MyDomainObject';
        $description = 'My long domain object description';

        $input = [
            'name' => $name,
            'objectsettings' => [
                'description' => $description,
                'aggregateRoot' => true,
                'type' => 'Entity'
            ],
            'propertyGroup' => [
                'properties' => [
                    0 => [
                        'propertyName' => 'name',
                        'propertyType' => 'String',
                        'propertyIsRequired' => true
                    ],
                    1 => [
                        'propertyName' => 'type',
                        'propertyType' => 'Integer'
                    ]
                ]
            ],
            'actionGroup' => [
                'customActions' => [
                    'test'
                ],
                'list' => true,
            ],
            'relationGroup' => []
        ];

        $expected = new DomainObject();
        $expected->setName($name);
        $expected->setDescription($description);
        $expected->setEntity(true);
        $expected->setAggregateRoot(true);

        $property0 = new StringProperty('name');
        $property0->setRequired(true);
        $expected->addProperty($property0);

        $property1 = new IntegerProperty('type');
        $expected->addProperty($property1);

        $testAction = GeneralUtility::makeInstance(Action::class);
        $testAction->setName('test');
        $testAction->setCustomAction(true);
        $expected->addAction($testAction);

        $listAction = GeneralUtility::makeInstance(Action::class);
        $listAction->setName('list');
        $expected->addAction($listAction);

        $actual = $this->objectSchemaBuilder->build($input);
        self::assertEquals($expected, $actual, 'Domain Object not built correctly.');
    }

    /**
     * @test
     */
    public function domainObjectHasExpectedRelations(): void
    {
//        $this->markTestSkipped(
//          'TODO: find a replacement for ConfigurationManager->getClassConfiguration'
//        );
        $name = 'MyDomainObject';
        $description = 'My long domain object description';
        $className = '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser';

        $input = [
            'name' => $name,
            'objectsettings' => [
                'description' => $description,
                'aggregateRoot' => true,
                'type' => 'Entity'
            ],
            'relationGroup' => [
                'relations' => [
                    0 => [
                        'relationName' => 'relation 1',
                        'relationType' => 'manyToMany',
                        'propertyIsExcludeField' => false,
                        'foreignRelationClass' => $className
                    ],
                    1 => [
                        'relationName' => 'relation 2',
                        'relationType' => 'manyToMany',
                        'propertyIsExcludeField' => false,
                        'foreignRelationClass' => $className
                    ],
                ]
            ],
        ];

        $expected = new DomainObject();
        $expected->setName($name);
        $expected->setDescription($description);
        $expected->setEntity(true);
        $expected->setAggregateRoot(true);

        $relation1 = new ManyToManyRelation('relation 1');
        $relation1->setForeignClassName($className);
        $relation1->setRelatedToExternalModel(true);
        $relation1->setExcludeField(false);
        $relation1->setForeignDatabaseTableName('fe_users');
        $expected->addProperty($relation1);

        $relation2 = new ManyToManyRelation('relation 2');
        $relation2->setForeignClassName($className);
        $relation2->setRelatedToExternalModel(true);
        $relation2->setExcludeField(false);
        $relation2->setForeignDatabaseTableName('fe_users');
        $expected->addProperty($relation2);

        $this->configurationManager->expects(self::atLeastOnce())
            ->method('getPersistenceTable')
            ->with($className)
            ->willReturn('fe_users');
        $actual = $this->objectSchemaBuilder->build($input);
        self::assertEquals($actual, $expected, 'Domain Object not built correctly.');
    }

    /**
     * @test
     */
    public function manyToManyRelationReturnsCorrectRelationTable(): void
    {
        $name = 'MyDomainObject';
        $description = 'My long domain object description';
        $relationName = 'Relation1';
        $className = '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser';

        $input = [
            'name' => $name,
            'objectsettings' => [
                'description' => $description,
                'aggregateRoot' => true,
                'type' => 'Entity'
            ],
            'relationGroup' => [
                'relations' => [
                    0 => [
                        'relationName' => $relationName,
                        'relationType' => 'manyToMany',
                        'propertyIsExcludeField' => false,
                        'foreignRelationClass' => $className
                    ],
                ]
            ],
        ];

        $this->configurationManager->expects(self::atLeastOnce())
            ->method('getPersistenceTable')
            ->with($className)
            ->willReturn('fe_users');

        $domainObject = $this->objectSchemaBuilder->build($input);
        $dummyExtension = new Extension();
        $dummyExtension->setExtensionKey('dummy');
        $domainObject->setExtension($dummyExtension);

        $relation = $domainObject->getPropertyByName($relationName);

        self::assertTrue($relation->getUseMMTable(), 'ManyToMany Relation->getUseMMTable() returned false.');

        self::assertEquals('tx_dummy_mydomainobject_frontenduser_mm', $relation->getRelationTableName());

        $relation->setUseExtendedRelationTableName(true);

        self::assertEquals('tx_dummy_mydomainobject_relation1_frontenduser_mm', $relation->getRelationTableName());

        self::assertEquals('fe_users', $relation->getForeignDatabaseTableName());
    }

    /**
     * @test
     */
    public function anyToManyRelationHasExpectedProperties(): void
    {
        $domainObjectName1 = 'DomainObject1';
        $domainObjectName2 = 'DomainObject2';
        $description = 'My long domain object description';
        $relationName = 'Relation1';
        $input = [
            'name' => $domainObjectName1,
            'objectsettings' => [
                'description' => $description,
                'aggregateRoot' => true,
                'type' => 'Entity'
            ],
            'relationGroup' => [
                'relations' => [
                    0 => [
                        'relationName' => $relationName,
                        'relationType' => 'zeroToMany',
                        'propertyIsExcludeField' => false,
                    ],
                ]
            ],
        ];

        $domainObject1 = $this->objectSchemaBuilder->build($input);
        $input['name'] = $domainObjectName2;
        $domainObject2 = $this->objectSchemaBuilder->build($input);

        $dummyExtension = new Extension();
        $dummyExtension->setExtensionKey('dummy');

        $domainObject1->setExtension($dummyExtension);
        $domainObject2->setExtension($dummyExtension);

        $relation = $domainObject1->getPropertyByName($relationName);

        $relation->setForeignModel($domainObject2);

        self::assertFalse($relation->getUseMMTable(), 'ZeroToMany Relation->getUseMMTable() returned true.');

        self::assertEquals('tx_dummy_domain_model_domainobject2', $relation->getForeignDatabaseTableName());
    }

    /**
     * Find the mapped table for a foreign related class
     * @test
     */
    public function anyToManyRelationToForeignClassBuildsCorrectRelationTableName(): void
    {
        $domainObjectName1 = 'DomainObject1';
        $description = 'My long domain object description';
        $relationName = 'Relation1';
        $className = '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser';

        $input = [
            'name' => $domainObjectName1,
            'objectsettings' => [
                'description' => $description,
                'aggregateRoot' => true,
                'type' => 'Entity'
            ],
            'relationGroup' => [
                'relations' => [
                    0 => [
                        'relationName' => $relationName,
                        'relationType' => 'zeroToMany',
                        'propertyIsExcludeField' => false,
                        'foreignRelationClass' => $className
                    ],
                ]
            ],
        ];

        $this->configurationManager->expects(self::atLeastOnce())
            ->method('getPersistenceTable')
            ->with($className)
            ->willReturn('fe_users');

        $domainObject1 = $this->objectSchemaBuilder->build($input);

        $relation = $domainObject1->getPropertyByName($relationName);

        self::assertFalse($relation->getUseMMTable(), 'ZeroToMany Relation->getUseMMTable() returned true.');

        self::assertEquals('fe_users', $relation->getForeignDatabaseTableName());
    }
}
