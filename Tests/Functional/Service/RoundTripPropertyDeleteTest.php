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

namespace EBT\ExtensionBuilder\Tests\Functional\Service;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RoundTripPropertyDeleteTest extends BaseFunctionalTest
{
    private ObjectSchemaBuilder $objectSchemaBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectSchemaBuilder = GeneralUtility::makeInstance(ObjectSchemaBuilder::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function propertyDeleteRemovesGetterAndSetter(): void
    {
        $modelName = 'PropertyDeleteModel';
        $this->generateInitialModelClassFile($modelName);

        $domainObject = $this->buildDomainObject($modelName);
        $property = new StringProperty('title');
        $propertyUid = md5('property-delete-title');
        $property->setUniqueIdentifier($propertyUid);
        $domainObject->addProperty($property);

        $objectUid = md5('property-delete-object');
        $domainObject->setUniqueIdentifier($objectUid);

        $this->roundTripService->_set('previousDomainObjects', [$objectUid => $domainObject]);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();

        self::assertTrue($modelClassObject->methodExists('getTitle'), 'getTitle must exist before deletion');
        self::assertTrue($modelClassObject->methodExists('setTitle'), 'setTitle must exist before deletion');

        $this->roundTripService->_set('classObject', $modelClassObject);

        // New domain object has the same UID but WITHOUT the "title" property
        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objectUid);
        // No properties added — title is removed

        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);

        $classObject = $this->roundTripService->_get('classObject');
        self::assertFalse($classObject->methodExists('getTitle'), 'getTitle must be removed after property deletion');
        self::assertFalse($classObject->methodExists('setTitle'), 'setTitle must be removed after property deletion');
        self::assertNull($classObject->getProperty('title'), 'title property must be removed after deletion');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function relationDeleteRemovesAllFourMethods(): void
    {
        $modelName = 'RelationDeleteModel';
        $this->generateInitialModelClassFile($modelName);

        $domainObject = $this->buildDomainObject($modelName);
        $relationConfig = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'tags',
            'relationType' => 'manyToMany',
        ];
        $relation = $this->objectSchemaBuilder->buildRelation($relationConfig, $domainObject);
        $relationUid = md5('relation-delete-tags');
        $relation->setUniqueIdentifier($relationUid);
        $relation->setForeignModel($this->buildDomainObject('Tag'));
        $domainObject->addProperty($relation);

        $objectUid = md5('relation-delete-object');
        $domainObject->setUniqueIdentifier($objectUid);

        $this->roundTripService->_set('previousDomainObjects', [$objectUid => $domainObject]);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();

        self::assertTrue($modelClassObject->methodExists('addTag'), 'addTag must exist before deletion');
        self::assertTrue($modelClassObject->methodExists('removeTag'), 'removeTag must exist before deletion');
        self::assertTrue($modelClassObject->methodExists('getTags'), 'getTags must exist before deletion');
        self::assertTrue($modelClassObject->methodExists('setTags'), 'setTags must exist before deletion');

        $this->roundTripService->_set('classObject', $modelClassObject);

        // New domain object has the same UID but WITHOUT the "tags" relation
        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objectUid);

        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);

        $classObject = $this->roundTripService->_get('classObject');
        self::assertFalse($classObject->methodExists('addTag'), 'addTag must be removed after relation deletion');
        self::assertFalse($classObject->methodExists('removeTag'), 'removeTag must be removed after relation deletion');
        self::assertFalse($classObject->methodExists('getTags'), 'getTags must be removed after relation deletion');
        self::assertFalse($classObject->methodExists('setTags'), 'setTags must be removed after relation deletion');
        self::assertNull($classObject->getProperty('tags'), 'tags property must be removed after deletion');
    }
}
