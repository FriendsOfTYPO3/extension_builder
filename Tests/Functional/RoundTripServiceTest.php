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

namespace EBT\ExtensionBuilder\Tests\Functional;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripServiceTest extends BaseFunctionalTest
{
    /**
     * @var ObjectSchemaBuilder
     */
    protected $objectSchemaBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectSchemaBuilder = $this->objectManager->get(ObjectSchemaBuilder::class);
    }

    /**
     * Write a simple model class for a non aggregate root domain object
     * @test
     */
    public function relatedMethodsReflectRenamingAProperty(): void
    {
        $modelName = 'model7';
        $this->generateInitialModelClassFile($modelName);
        // create an "old" domainObject
        $domainObject = $this->buildDomainObject($modelName);
        self::assertIsObject($domainObject, 'No domain object');

        $property = new StringProperty('prop1');
        $uniqueIdentifier1 = md5(microtime() . 'prop1');
        $property->setUniqueIdentifier($uniqueIdentifier1);
        $domainObject->addProperty($property);

        $uniqueIdentifier2 = md5(microtime() . 'model');
        $domainObject->setUniqueIdentifier($uniqueIdentifier2);

        $this->roundTripService->_set('previousDomainObjects', [$domainObject->getUniqueIdentifier() => $domainObject]);
        $templateClass = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';
        // create an "old" class object.
        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $templateClass,
            null
        )->getFirstClass();
        self::assertIsObject($modelClassObject, 'No class object');

        // Check that the getter/methods exist
        self::assertTrue($modelClassObject->methodExists('getProp1'));
        self::assertTrue($modelClassObject->methodExists('setProp1'));

        // set the class object manually, this is usually parsed from an existing class file
        $this->roundTripService->_set('classObject', $modelClassObject);

        // build a new domain object with the same unique identifiers
        $newDomainObject = $this->buildDomainObject('Dummy');
        $property = new BooleanProperty('newProp1Name');
        $property->setUniqueIdentifier($uniqueIdentifier1);
        $property->setRequired(true);
        $newDomainObject->addProperty($property);
        $newDomainObject->setUniqueIdentifier($uniqueIdentifier2);

        // now the class object should be updated
        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);

        $classObject = $this->roundTripService->_get('classObject');
        self::assertTrue($classObject->methodExists('getNewProp1Name'));
        self::assertTrue($classObject->methodExists('setNewProp1Name'));
    }

    /**
     * @test
     */
    public function relatedMethodsReflectRenamingARelation(): void
    {
        $modelName = 'Model8';
        $this->generateInitialModelClassFile($modelName);
        // create an "old" domainObject
        $domainObject = $this->buildDomainObject($modelName);
        self::assertIsObject($domainObject, 'No domain object');

        $relationJsonConfiguration = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'children',
            'relationType' => 'manyToMany',
        ];

        $relation = $this->objectSchemaBuilder->buildRelation($relationJsonConfiguration, $domainObject);

        $uniqueIdentifier1 = md5(microtime() . 'children');
        $relation->setUniqueIdentifier($uniqueIdentifier1);
        $relation->setForeignModel($this->buildDomainObject('ChildModel'));
        $domainObject->addProperty($relation);

        $uniqueIdentifier2 = md5(microtime() . 'Model8');
        $domainObject->setUniqueIdentifier($uniqueIdentifier2);

        $this->roundTripService->_set('previousDomainObjects', [$domainObject->getUniqueIdentifier() => $domainObject]);
        $templateClass = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';
        // create an "old" class object.
        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $templateClass,
            null
        )->getFirstClass();
        self::assertIsObject($modelClassObject, 'No class object');

        // Check that the property related methods exist
        self::assertTrue($modelClassObject->methodExists('setChildren'));
        self::assertTrue($modelClassObject->methodExists('getChildren'));
        self::assertTrue($modelClassObject->methodExists('addChild'));
        self::assertTrue($modelClassObject->methodExists('removeChild'));

        // set the class object manually, this is usually parsed from an existing class file
        $this->roundTripService->_set('classObject', $modelClassObject);

        // build a new domain object with the same unique identifiers
        $newDomainObject = $this->buildDomainObject('Model8');

        $newRelation = $this->objectSchemaBuilder->buildRelation($relationJsonConfiguration, $newDomainObject);
        $newRelation->setUniqueIdentifier($uniqueIdentifier1);
        $newRelation->setForeignModel($this->buildDomainObject('ChildModel'));

        $newRelation->setName('posts');

        $newDomainObject->addProperty($newRelation);
        $newDomainObject->setUniqueIdentifier($uniqueIdentifier2);

        // now the class object should be updated
        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);
        $modifiedModelClassObject = $this->roundTripService->_get('classObject');

        self::assertTrue($modifiedModelClassObject->methodExists('setPosts'));
        self::assertTrue($modifiedModelClassObject->methodExists('getPosts'));
        self::assertTrue($modifiedModelClassObject->methodExists('addPost'));
        self::assertTrue($modifiedModelClassObject->methodExists('removePost'));
    }

    /**
     * Write a simple model class for a non aggregate root domain object
     * @test
     */
    public function relatedMethodsReflectRenamingARelatedModel(): void
    {
        $modelName = 'Model8';
        $this->generateInitialModelClassFile($modelName);
        // create an "old" domainObject
        $domainObject = $this->buildDomainObject($modelName);
        self::assertIsObject($domainObject, 'No domain object');

        $relationJsonConfiguration = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'children',
            'relationType' => 'manyToMany',
        ];

        $relation = $this->objectSchemaBuilder->buildRelation($relationJsonConfiguration, $domainObject);

        $uniqueIdentifier1 = md5(microtime() . 'children');
        $relation->setUniqueIdentifier($uniqueIdentifier1);
        $relation->setForeignModel($this->buildDomainObject('ChildModel'));
        $domainObject->addProperty($relation);

        $uniqueIdentifier2 = md5(microtime() . 'Model8');
        $domainObject->setUniqueIdentifier($uniqueIdentifier2);

        $this->roundTripService->_set('previousDomainObjects', [$domainObject->getUniqueIdentifier() => $domainObject]);

        // create an "old" class object.
        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();

        self::assertIsObject($modelClassObject, 'No class object');

        // Check that the property related methods exist
        self::assertTrue($modelClassObject->methodExists('setChildren'));
        self::assertTrue($modelClassObject->methodExists('getChildren'));
        self::assertTrue($modelClassObject->methodExists('addChild'));
        self::assertTrue($modelClassObject->methodExists('removeChild'));

        // set the class object manually, this is usually parsed
        // from an existing class file
        $this->roundTripService->_set('classObject', $modelClassObject);

        // build a new domain object with the same unique identifiers
        $newDomainObject = $this->buildDomainObject('Model8');

        $newRelation = $this->objectSchemaBuilder->buildRelation($relationJsonConfiguration, $domainObject);
        $newRelation->setUniqueIdentifier($uniqueIdentifier1);
        $newRelation->setForeignModel($this->buildDomainObject('RenamedModel'));

        $newRelation->setName('children');

        $newDomainObject->addProperty($newRelation);
        $newDomainObject->setUniqueIdentifier($uniqueIdentifier2);

        // now the class object should be updated
        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);
        $modifiedModelClassObject = $this->roundTripService->_get('classObject');

        $newAddMethod = $modifiedModelClassObject->getMethod('addChild');
        $parameters = $newAddMethod->getParameters();
        self::assertCount(1, $parameters);

        $addParameter = current($parameters);
        self::assertEquals('\\EBT\\Dummy\\Domain\\Model\\RenamedModel', $addParameter->getTypeHint());

        $newRemoveMethod = $modifiedModelClassObject->getMethod('removeChild');
        $parameters = $newRemoveMethod->getParameters();
        self::assertCount(1, $parameters);

        $addParameter = current($parameters);
        self::assertEquals('\\EBT\\Dummy\\Domain\\Model\\RenamedModel', $addParameter->getTypeHint());
    }
}
