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

use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RoundTripMmTableWarningTest extends BaseFunctionalTest
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
    public function manyToManyToManyToOneAddsDataLossWarning(): void
    {
        $modelName = 'WarnMmLoss';
        $this->generateInitialModelClassFile($modelName);

        $domainObject = $this->buildDomainObject($modelName);
        $relationUid = md5('warn-mm-loss-relation');
        $objectUid = md5('warn-mm-loss-object');

        $oldRelationConfig = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'tags',
            'relationType' => 'manyToMany',
        ];
        $oldRelation = $this->objectSchemaBuilder->buildRelation($oldRelationConfig, $domainObject);
        $oldRelation->setUniqueIdentifier($relationUid);
        $oldRelation->setForeignModel($this->buildDomainObject('Tag'));
        $domainObject->addProperty($oldRelation);
        $domainObject->setUniqueIdentifier($objectUid);

        $this->roundTripService->_set('previousDomainObjects', [$objectUid => $domainObject]);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();
        $this->roundTripService->_set('classObject', $modelClassObject);

        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objectUid);
        $newRelationConfig = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'tags',
            'relationType' => 'manyToOne',
        ];
        $newRelation = $this->objectSchemaBuilder->buildRelation($newRelationConfig, $newDomainObject);
        $newRelation->setUniqueIdentifier($relationUid);
        $newRelation->setForeignModel($this->buildDomainObject('Tag'));
        $newDomainObject->addProperty($newRelation);

        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);

        $warnings = $this->roundTripService->getParseWarnings();
        self::assertNotEmpty($warnings, 'A warning must be generated when changing from manyToMany to manyToOne');
        $combined = implode(' ', $warnings);
        self::assertStringContainsString('manyToMany', $combined);
        self::assertStringContainsString('_mm', $combined);
    }

    /**
     * @test
     */
    public function manyToOneToManyToManyAddsNewMmTableWarning(): void
    {
        $modelName = 'WarnMmNew';
        $this->generateInitialModelClassFile($modelName);

        $domainObject = $this->buildDomainObject($modelName);
        $relationUid = md5('warn-mm-new-relation');
        $objectUid = md5('warn-mm-new-object');

        $oldRelationConfig = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'tags',
            'relationType' => 'manyToOne',
        ];
        $oldRelation = $this->objectSchemaBuilder->buildRelation($oldRelationConfig, $domainObject);
        $oldRelation->setUniqueIdentifier($relationUid);
        $oldRelation->setForeignModel($this->buildDomainObject('Tag'));
        $domainObject->addProperty($oldRelation);
        $domainObject->setUniqueIdentifier($objectUid);

        $this->roundTripService->_set('previousDomainObjects', [$objectUid => $domainObject]);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $domainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();
        $this->roundTripService->_set('classObject', $modelClassObject);

        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objectUid);
        $newRelationConfig = [
            'lazyLoading' => 0,
            'propertyIsExcludeField' => 1,
            'relationDescription' => '',
            'relationName' => 'tags',
            'relationType' => 'manyToMany',
        ];
        $newRelation = $this->objectSchemaBuilder->buildRelation($newRelationConfig, $newDomainObject);
        $newRelation->setUniqueIdentifier($relationUid);
        $newRelation->setForeignModel($this->buildDomainObject('Tag'));
        $newDomainObject->addProperty($newRelation);

        $this->roundTripService->_call('updateModelClassProperties', $domainObject, $newDomainObject);

        $warnings = $this->roundTripService->getParseWarnings();
        self::assertNotEmpty($warnings, 'A warning must be generated when changing to manyToMany');
        $combined = implode(' ', $warnings);
        self::assertStringContainsString('manyToMany', $combined);
        self::assertStringContainsString('_mm', $combined);
    }
}
