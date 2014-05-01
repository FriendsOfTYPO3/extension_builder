<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen <mail@ndh-websolutions.de>, ndh websolutions
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class ConfigurationManagerTest extends \EBT\ExtensionBuilder\Tests\BaseTest {

	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	protected function setUp() {
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->configurationManager = $this->objectManager->get('EBT\\ExtensionBuilder\\Configuration\\ConfigurationManager');
	}

	/**
	 * @test
	 */
	public function getExtbaseClassConfigurationReturnsCorrectValue() {
		$classConfiguration = $this->configurationManager->getExtbaseClassConfiguration('TYPO3\\CMS\\Extbase\\Domain\\Model\\FrontendUser');
		$this->assertSame($classConfiguration['tableName'], 'fe_users');
	}
}
