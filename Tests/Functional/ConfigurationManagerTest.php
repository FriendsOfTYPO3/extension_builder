<?php
namespace EBT\ExtensionBuilder\Tests\Unit;

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

class ConfigurationManagerTest extends \EBT\ExtensionBuilder\Tests\BaseFunctionalTest
{
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
     */
    protected $configurationManager;

    protected function setUp()
    {
        parent::setUp();
        $this->configurationManager = $this->objectManager->get('EBT\\ExtensionBuilder\\Configuration\\ConfigurationManager');
    }

    /**
     * @test
     */
    public function getExtbaseClassConfigurationReturnsCorrectValue()
    {
        $classConfiguration = $this->configurationManager->getExtbaseClassConfiguration('TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser');
        self::assertSame($classConfiguration['tableName'], 'fe_users');
    }
}
