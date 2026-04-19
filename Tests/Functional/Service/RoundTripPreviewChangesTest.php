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

class RoundTripPreviewChangesTest extends BaseFunctionalTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function previewChangesReturnsNoChangesWhenPropertiesUnchanged(): void
    {
        $modelName = 'PreviewUnchanged';
        $this->generateInitialModelClassFile($modelName);

        $propUid = md5('prop-title');
        $objUid = md5('obj-unchanged');

        $oldObj = $this->buildDomainObject($modelName);
        $oldObj->setUniqueIdentifier($objUid);
        $prop = new StringProperty('title');
        $prop->setUniqueIdentifier($propUid);
        $oldObj->addProperty($prop);

        $this->roundTripService->_set('previousDomainObjects', [$objUid => $oldObj]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $newObj = $this->buildDomainObject($modelName);
        $newObj->setUniqueIdentifier($objUid);
        $sameProp = new StringProperty('title');
        $sameProp->setUniqueIdentifier($propUid);
        $newObj->addProperty($sameProp);
        $this->extension->addDomainObject($newObj);

        $result = $this->roundTripService->previewChanges($this->extension);

        self::assertFalse($result['hasChanges']);
        self::assertEmpty($result['modifiedFiles']);
        self::assertEmpty($result['deletedFiles']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function previewChangesDetectsRenamedProperty(): void
    {
        $modelName = 'PreviewRename';
        $this->generateInitialModelClassFile($modelName);

        $propUid = md5('prop-rename');
        $objUid = md5('obj-rename');

        $oldObj = $this->buildDomainObject($modelName);
        $oldObj->setUniqueIdentifier($objUid);
        $oldProp = new StringProperty('oldName');
        $oldProp->setUniqueIdentifier($propUid);
        $oldObj->addProperty($oldProp);

        $this->roundTripService->_set('previousDomainObjects', [$objUid => $oldObj]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $newObj = $this->buildDomainObject($modelName);
        $newObj->setUniqueIdentifier($objUid);
        $newProp = new StringProperty('newName');
        $newProp->setUniqueIdentifier($propUid);
        $newObj->addProperty($newProp);
        $this->extension->addDomainObject($newObj);

        $result = $this->roundTripService->previewChanges($this->extension);

        self::assertTrue($result['hasChanges']);
        self::assertNotEmpty($result['modifiedFiles']);
        $changes = $result['modifiedFiles'][0]['changes'];
        $renames = array_values(array_filter($changes, fn($c) => $c['type'] === 'renamed'));
        self::assertNotEmpty($renames, 'Expected a renamed method entry');
        self::assertEquals('getOldName', $renames[0]['from']);
        self::assertEquals('getNewName', $renames[0]['to']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function previewChangesDetectsRemovedProperty(): void
    {
        $modelName = 'PreviewRemoved';
        $this->generateInitialModelClassFile($modelName);

        $propUid = md5('prop-removed');
        $objUid = md5('obj-removed');

        $oldObj = $this->buildDomainObject($modelName);
        $oldObj->setUniqueIdentifier($objUid);
        $removedProp = new StringProperty('removedField');
        $removedProp->setUniqueIdentifier($propUid);
        $oldObj->addProperty($removedProp);

        $this->roundTripService->_set('previousDomainObjects', [$objUid => $oldObj]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $newObj = $this->buildDomainObject($modelName);
        $newObj->setUniqueIdentifier($objUid);
        $this->extension->addDomainObject($newObj);

        $result = $this->roundTripService->previewChanges($this->extension);

        self::assertTrue($result['hasChanges']);
        self::assertNotEmpty($result['modifiedFiles']);
        $removed = array_filter($result['modifiedFiles'][0]['changes'], fn($c) => $c['type'] === 'removed');
        self::assertNotEmpty($removed);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function previewChangesDetectsDeletedDomainObject(): void
    {
        $modelName = 'PreviewDeleted';
        $this->generateInitialModelClassFile($modelName);

        $objUid = md5('obj-deleted');
        $oldObj = $this->buildDomainObject($modelName);
        $oldObj->setUniqueIdentifier($objUid);

        $this->roundTripService->_set('previousDomainObjects', [$objUid => $oldObj]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        // No domain objects in current extension — the old one was deleted
        $result = $this->roundTripService->previewChanges($this->extension);

        self::assertTrue($result['hasChanges']);
        self::assertNotEmpty($result['deletedFiles']);
        self::assertStringContainsString($modelName . '.php', $result['deletedFiles'][0]);
    }
}
