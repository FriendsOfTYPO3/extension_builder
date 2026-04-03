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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RoundTripTypeChangeTest extends BaseFunctionalTest
{
    private ObjectSchemaBuilder $objectSchemaBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectSchemaBuilder = GeneralUtility::makeInstance(ObjectSchemaBuilder::class);
    }

    /**
     * @test
     */
    public function propertyTypeChangeUpdatesVarTagInClassObject(): void
    {
        $modelName = 'TypeChangeModel';
        $this->generateInitialModelClassFile($modelName);

        $domainObject = $this->buildDomainObject($modelName);
        $propertyUid = md5('type-change-active');
        $oldProperty = new StringProperty('active');
        $oldProperty->setUniqueIdentifier($propertyUid);
        $domainObject->addProperty($oldProperty);

        $objectUid = md5('type-change-object');
        $domainObject->setUniqueIdentifier($objectUid);

        $this->roundTripService->_set('previousDomainObjects', [$objectUid => $domainObject]);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();

        $this->roundTripService->_set('classObject', $modelClassObject);

        // Change "active" from String to Boolean (same UID)
        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objectUid);
        $newProperty = new BooleanProperty('active');
        $newProperty->setUniqueIdentifier($propertyUid);
        $newDomainObject->addProperty($newProperty);

        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);

        $classObject = $this->roundTripService->_get('classObject');
        $classProperty = $classObject->getProperty('active');

        self::assertNotNull($classProperty, 'Property "active" must still exist after type change');
        self::assertStringContainsString('bool', $classProperty->getTagValue('var'), '@var tag must reflect Boolean type after type change');
    }

    /**
     * @test
     */
    public function relationTypeChangeFromManyToManyToManyToOneRemovesAddAndRemoveMethods(): void
    {
        $modelName = 'RelationTypeChangeModel';
        $this->generateInitialModelClassFile($modelName);

        $domainObject = $this->buildDomainObject($modelName);
        $relationUid = md5('relation-type-change');

        // Old: manyToMany → generates add/remove/get/set
        $oldRelationConfig = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'posts',
            'relationType' => 'manyToMany',
        ];
        $oldRelation = $this->objectSchemaBuilder->buildRelation($oldRelationConfig, $domainObject);
        $oldRelation->setUniqueIdentifier($relationUid);
        $oldRelation->setForeignModel($this->buildDomainObject('Post'));
        $domainObject->addProperty($oldRelation);

        $objectUid = md5('relation-type-change-object');
        $domainObject->setUniqueIdentifier($objectUid);

        $this->roundTripService->_set('previousDomainObjects', [$objectUid => $domainObject]);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();

        // Verify initial methods are present (manyToMany)
        self::assertTrue($modelClassObject->methodExists('addPost'), 'addPost must exist for manyToMany');
        self::assertTrue($modelClassObject->methodExists('removePost'), 'removePost must exist for manyToMany');
        self::assertTrue($modelClassObject->methodExists('getPosts'), 'getPosts must exist for manyToMany');
        self::assertTrue($modelClassObject->methodExists('setPosts'), 'setPosts must exist for manyToMany');

        $this->roundTripService->_set('classObject', $modelClassObject);

        // New: manyToOne → isAnyToManyRelation() = false → no add/remove
        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objectUid);
        $newRelationConfig = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'posts',
            'relationType' => 'manyToOne',
        ];
        $newRelation = $this->objectSchemaBuilder->buildRelation($newRelationConfig, $newDomainObject);
        $newRelation->setUniqueIdentifier($relationUid);
        $newRelation->setForeignModel($this->buildDomainObject('Post'));
        $newDomainObject->addProperty($newRelation);

        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);

        $classObject = $this->roundTripService->_get('classObject');

        // After type change from anyToMany → manyToOne, old methods and property are removed
        // (the class builder will add the appropriate get/set for the new type)
        self::assertFalse($classObject->methodExists('addPost'), 'addPost must be removed after type change to manyToOne');
        self::assertFalse($classObject->methodExists('removePost'), 'removePost must be removed after type change to manyToOne');
        self::assertFalse($classObject->methodExists('getPosts'), 'getPosts must be removed (property removed, new one added by builder)');
        self::assertNull($classObject->getProperty('posts'), 'posts property must be removed (new one added by class builder)');
    }
}
