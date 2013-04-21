<?php


/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Nico de Haen <mail@ndh-websolutions.de>, ndh websolutions
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

/**
 * test for validation service
 */
class Tx_ExtensionBuilder_ValidationServiceTest extends Tx_ExtensionBuilder_Tests_BaseTest {

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @test
	 * @return void
	 */
	public function propertyRenamesFieldIfItMatchesReservedWord() {
		$domainObject = $this->buildDomainObject('SomeModel', TRUE, TRUE);
		$property = new Tx_ExtensionBuilder_Domain_Model_DomainObject_StringProperty();
		$property->setName('Order');
		$property->setDomainObject($domainObject);
		$this->assertEquals('tx_dummy_order', $property->getFieldName());
	}
}

?>