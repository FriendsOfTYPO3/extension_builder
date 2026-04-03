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

use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripDomainObjectTableWarningTest extends BaseFunctionalTest
{
    /**
     * @test
     */
    public function domainObjectRenameAddsDbTableWarning(): void
    {
        $oldName = 'OldArticle';
        $newName = 'NewArticle';
        $uid = md5('warn-obj-table-rename');

        $this->generateInitialModelClassFile($oldName);

        $oldDomainObject = $this->buildDomainObject($oldName);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $newDomainObject = $this->buildDomainObject($newName);
        $newDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->getDomainModelClassFile($newDomainObject);

        $warnings = $this->roundTripService->getParseWarnings();
        self::assertNotEmpty($warnings, 'A migration warning must be generated for domain object rename');
        $combined = implode(' ', $warnings);
        self::assertStringContainsString('tx_dummy_domain_model_oldarticle', $combined);
        self::assertStringContainsString('tx_dummy_domain_model_newarticle', $combined);
        self::assertStringContainsString('TCA', $combined);
    }

    /**
     * @test
     */
    public function domainObjectRenameWarningIsAbsentWhenNameUnchanged(): void
    {
        $name = 'UnchangedObject';
        $uid = md5('warn-obj-unchanged');

        $this->generateInitialModelClassFile($name);

        $oldDomainObject = $this->buildDomainObject($name);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $newDomainObject = $this->buildDomainObject($name);
        $newDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->getDomainModelClassFile($newDomainObject);

        $warnings = $this->roundTripService->getParseWarnings();
        self::assertEmpty($warnings, 'No migration warning must be generated when domain object name is unchanged');
    }
}
