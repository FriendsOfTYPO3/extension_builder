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

/**
 * Select properties are stored as varchar(255) in TYPO3, consistent with
 * the TCA type=select schema and the fact that item values can be strings.
 */
class SelectPropertySqlTest extends BaseUnitTest
{
    /**
     * @test
     */
    public function selectPropertyReturnsVarcharSqlDefinition(): void
    {
        $property = new SelectProperty();
        $property->setName('status');
        $property->setDomainObject($this->buildDomainObject('TestModel'));

        self::assertSame(
            "status varchar(255) NOT NULL DEFAULT '',",
            $property->getSqlDefinition()
        );
    }

    /**
     * @test
     */
    public function selectPropertyTypeForCommentIsString(): void
    {
        $property = new SelectProperty();
        self::assertSame('string', $property->getTypeForComment());
    }

    /**
     * @test
     */
    public function selectPropertyTypeHintIsString(): void
    {
        $property = new SelectProperty();
        self::assertSame('string', $property->getTypeHint());
    }
}
