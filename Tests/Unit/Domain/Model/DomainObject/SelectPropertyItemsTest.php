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

namespace EBT\ExtensionBuilder\Tests\Unit\Domain\Model\DomainObject;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\SelectProperty;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

class SelectPropertyItemsTest extends BaseUnitTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function selectItemsDefaultsToEmptyArray(): void
    {
        $property = new SelectProperty();
        self::assertSame([], $property->getSelectItems());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function selectItemsCanBeSetAndRetrieved(): void
    {
        $property = new SelectProperty();
        $items = [
            ['label' => 'Red', 'value' => 'red'],
            ['label' => 'Blue', 'value' => 'blue'],
        ];
        $property->setSelectItems($items);
        self::assertSame($items, $property->getSelectItems());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function hasSelectItemsReturnsFalseWhenEmpty(): void
    {
        $property = new SelectProperty();
        self::assertFalse($property->hasSelectItems());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function hasSelectItemsReturnsTrueWhenItemsAreSet(): void
    {
        $property = new SelectProperty();
        $property->setSelectItems([['label' => 'Foo', 'value' => 'foo']]);
        self::assertTrue($property->hasSelectItems());
    }
}
