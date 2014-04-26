<?php
namespace EBT\ExtensionBuilder\Tests\Unit\Validation;

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
class ValidationServiceTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * @test
	 * @return void
	 */
	public function propertyRenamesFieldIfItMatchesReservedWord() {
		$domainObject = $this->buildDomainObject('SomeModel', TRUE, TRUE);
		$property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty();
		$property->setName('Order');
		$property->setDomainObject($domainObject);
		$this->assertEquals('tx_dummy_order', $property->getFieldName());
	}

	/**
	 * @test
	 */
	public function validateConfigurationFormatReturnsExcpetionsOnDuplicatePropertyNames() {
		$fixture = array(
			'modules' => array(
				array(
					'value' => array(
						'name' => 'Foo',
						'propertyGroup' => array(
							'properties' => array(
								array(
									'propertyName' => 'bar'
								)
							)
						),
						'relationGroup' => array(
							'relations' => array(
								array(
									'relationName' => 'bar'
								)
							)
						)
					)
				)
			),
			'properties' => array(
				'plugins' => array(),
				'backendModules' => array()
			)

		);
		$extensionValidator = new \EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator();
		$result = $extensionValidator->validateConfigurationFormat($fixture);
		$expected = array(
			'errors' => array(),
			'warnings' => array()
		);
		$expected['errors'][] =  new \EBT\ExtensionBuilder\Domain\Exception\ExtensionException(
				'Property "bar" of Model "Foo" exists twice.',
				\EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator::ERROR_PROPERTY_DUPLICATE
		);
		$this->assertEquals($result, $expected);
	}
}
