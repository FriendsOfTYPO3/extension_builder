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
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripPropertyRenameWarningTest extends BaseFunctionalTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function propertyRenameAddsDbColumnWarning(): void
    {
        $modelName = 'WarnOnPropRename';
        $this->generateInitialModelClassFile($modelName);

        $propUid = md5('warn-prop-rename');
        $objUid = md5('warn-obj-prop-rename');

        $oldDomainObject = $this->buildDomainObject($modelName);
        $oldDomainObject->setUniqueIdentifier($objUid);
        $oldProperty = new StringProperty('title');
        $oldProperty->setUniqueIdentifier($propUid);
        $oldDomainObject->addProperty($oldProperty);

        $this->roundTripService->_set('previousDomainObjects', [$objUid => $oldDomainObject]);

        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objUid);
        $newProperty = new StringProperty('headline');
        $newProperty->setUniqueIdentifier($propUid);
        $newDomainObject->addProperty($newProperty);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $oldDomainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();
        $this->roundTripService->_set('classObject', $modelClassObject);

        $this->roundTripService->_call('updateModelClassProperties', $oldDomainObject, $newDomainObject);

        $warnings = $this->roundTripService->getParseWarnings();
        self::assertNotEmpty($warnings, 'A migration warning must be generated for property rename');
        $combined = implode(' ', $warnings);
        self::assertStringContainsString('tx_dummy_domain_model_warnonproprename.title', $combined);
        self::assertStringContainsString('tx_dummy_domain_model_warnonproprename.headline', $combined);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function propertyRenameWarningIsAbsentWhenNameUnchanged(): void
    {
        $modelName = 'WarnPropUnchanged';
        $this->generateInitialModelClassFile($modelName);

        $propUid = md5('no-warn-prop-unchanged');
        $objUid = md5('no-warn-obj-unchanged');

        $oldDomainObject = $this->buildDomainObject($modelName);
        $oldDomainObject->setUniqueIdentifier($objUid);
        $oldProperty = new StringProperty('title');
        $oldProperty->setUniqueIdentifier($propUid);
        $oldDomainObject->addProperty($oldProperty);

        $this->roundTripService->_set('previousDomainObjects', [$objUid => $oldDomainObject]);

        $newDomainObject = $this->buildDomainObject($modelName);
        $newDomainObject->setUniqueIdentifier($objUid);
        $newProperty = new StringProperty('title');
        $newProperty->setUniqueIdentifier($propUid);
        $newDomainObject->addProperty($newProperty);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject(
            $oldDomainObject,
            $this->modelClassTemplatePath,
            null
        )->getFirstClass();
        $this->roundTripService->_set('classObject', $modelClassObject);

        $this->roundTripService->_call('updateModelClassProperties', $oldDomainObject, $newDomainObject);

        $warnings = $this->roundTripService->getParseWarnings();
        self::assertEmpty($warnings, 'No migration warning must be generated when property name is unchanged');
    }
}
