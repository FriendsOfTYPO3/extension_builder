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

namespace EBT\ExtensionBuilder\Tests\Functional\Configuration;

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationManagerTest extends BaseFunctionalTest
{
    private ExtensionBuilderConfigurationManager $configurationManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);
    }

    /**
     * @test
     */
    public function getParentClassForValueObject(): void
    {
        $parentClassForValueObject = $this->configurationManager->getParentClassForValueObject($this->extension);
        self::assertSame('\\' . AbstractValueObject::class, $parentClassForValueObject);
    }

    /**
     * @test
     */
    public function getParentClassForEntityObject(): void
    {
        $parentClassForValueObject = $this->configurationManager->getParentClassForEntityObject($this->extension);
        self::assertSame('\\' . AbstractEntity::class, $parentClassForValueObject);
    }
}
