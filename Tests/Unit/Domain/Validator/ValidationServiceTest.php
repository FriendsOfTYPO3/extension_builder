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

namespace EBT\ExtensionBuilder\Tests\Unit\Domain\Validator;

use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;
use EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

class ValidationServiceTest extends BaseUnitTest
{
    /**
     * @test
     */
    public function testForReservedWord(): void
    {
        self::assertTrue(ExtensionValidator::isReservedWord('DATABASE'));
    }

    /**
     * @test
     */
    public function validateExtensionWarnsForDomainObjectWithNoProperties(): void
    {
        $domainObject = $this->buildDomainObject('EmptyModel');
        // no properties added
        $this->extension->addDomainObject($domainObject);

        $extensionValidator = new ExtensionValidator();
        $result = $extensionValidator->validateExtension($this->extension);

        $warningCodes = array_map(fn($w) => $w->getCode(), $result['warnings']);
        self::assertContains(
            ExtensionValidator::ERROR_DOMAINOBJECT_NO_PROPERTIES,
            $warningCodes,
            'Expected warning for domain object without properties'
        );
    }

    /**
     * @test
     */
    public function noWarningForDomainObjectWithNoPropertiesWhenItIsInlineFkTarget(): void
    {
        $ownerObject = $this->buildDomainObject('OwnerModel', true, true);
        $childObject = $this->buildDomainObject('ChildModel', true);
        // child has no own properties, but is target of an inline ZeroToMany relation
        $relation = new ZeroToManyRelation('children');
        $relation->setForeignModel($childObject);
        $relation->setRenderType('inline');
        $ownerObject->addProperty($relation);
        $this->extension->addDomainObject($ownerObject);
        $this->extension->addDomainObject($childObject);

        $extensionValidator = new ExtensionValidator();
        $result = $extensionValidator->validateExtension($this->extension);

        $warningCodes = array_map(fn($w) => $w->getCode(), $result['warnings']);
        self::assertNotContains(
            ExtensionValidator::ERROR_DOMAINOBJECT_NO_PROPERTIES,
            $warningCodes,
            'No warning expected for child model that is target of an inline FK relation'
        );
    }

    /**
     * @test
     */
    public function validateConfigurationFormatReturnsExceptionsOnDuplicatePropertyNames(): void
    {
        $fixture = [
            'modules' => [
                [
                    'value' => [
                        'name' => 'Foo',
                        'propertyGroup' => [
                            'properties' => [
                                [
                                    'propertyName' => 'bar',
                                ],
                            ],
                        ],
                        'relationGroup' => [
                            'relations' => [
                                [
                                    'relationName' => 'bar',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'properties' => [
                'plugins' => [],
                'backendModules' => [],
            ],
        ];
        $extensionValidator = new ExtensionValidator();

        $result = $extensionValidator->validateConfigurationFormat($fixture);
        $expected = [
            'errors' => [],
            'warnings' => [],
        ];
        $expected['errors'][] = new ExtensionException(
            'Property "bar" of Model "Foo" exists twice.',
            ExtensionValidator::ERROR_PROPERTY_DUPLICATE
        );
        self::assertEquals($expected, $result);
    }
}
