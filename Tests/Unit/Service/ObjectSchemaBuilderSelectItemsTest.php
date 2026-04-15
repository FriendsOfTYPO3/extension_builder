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

namespace EBT\ExtensionBuilder\Tests\Unit\Service;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\SelectProperty;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

class ObjectSchemaBuilderSelectItemsTest extends BaseUnitTest
{

    /**
     * @test
     */
    public function buildPropertyMapsSelectItemsToSelectProperty(): void
    {
        $input = [
            'propertyName' => 'status',
            'propertyType' => 'Select',
            'selectItems' => [
                ['label' => 'Active', 'value' => 'active'],
                ['label' => 'Inactive', 'value' => 'inactive'],
            ],
        ];

        $property = ObjectSchemaBuilder::buildProperty($input);

        self::assertInstanceOf(SelectProperty::class, $property);
        self::assertSame([
            ['label' => 'Active', 'value' => 'active'],
            ['label' => 'Inactive', 'value' => 'inactive'],
        ], $property->getSelectItems());
    }

    /**
     * @test
     */
    public function buildPropertyDefaultsSelectItemsToEmptyArrayWhenMissing(): void
    {
        $input = [
            'propertyName' => 'color',
            'propertyType' => 'Select',
        ];

        $property = ObjectSchemaBuilder::buildProperty($input);

        self::assertInstanceOf(SelectProperty::class, $property);
        self::assertSame([], $property->getSelectItems());
    }
}
