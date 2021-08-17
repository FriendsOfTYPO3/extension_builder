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

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

class ConfigurationManagerTest extends BaseFunctionalTest
{
    /**
     * @var ExtensionBuilderConfigurationManager
     */
    private $configurationManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);
    }

    /**
     * @test
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function getPersistenceTableReturnsCorrectValue(): void
    {
        $tableName = $this->configurationManager->getPersistenceTable(FrontendUser::class);
        self::assertSame($tableName, 'fe_users');
    }
}
