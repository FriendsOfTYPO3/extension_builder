<?php
namespace EBT\ExtensionBuilder\Tests\Unit\Validation;

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

/**
 * test for validation service
 */
class ValidationServiceTest extends \EBT\ExtensionBuilder\Tests\BaseUnitTest
{
    /**
     * @test
     * @return void
     */
    public function propertyRenamesFieldIfItMatchesReservedWord()
    {
        $domainObject = $this->buildDomainObject('SomeModel', true, true);
        $property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty();
        $property->setName('Order');
        $property->setDomainObject($domainObject);
        self::assertEquals('tx_dummy_order', $property->getFieldName());
    }

    /**
     * @test
     */
    public function testForReservedWord()
    {
        self::assertTrue(\EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator::isReservedWord('DATABASE'));
    }

    /**
     * @test
     */
    public function validateConfigurationFormatReturnsExceptionsOnDuplicatePropertyNames()
    {
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
        $expected['errors'][] = new \EBT\ExtensionBuilder\Domain\Exception\ExtensionException(
            'Property "bar" of Model "Foo" exists twice.',
            \EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator::ERROR_PROPERTY_DUPLICATE
        );
        self::assertEquals($result, $expected);
    }
}
