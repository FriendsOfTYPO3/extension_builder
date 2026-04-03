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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\DateProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\DateTimeProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\TimeProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\TimeSecProperty;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

/**
 * TYPO3 v13 auto-creates bigint columns for type=datetime TCA fields,
 * so no SQL definition should be generated for these property types.
 */
class DatetimePropertySqlTest extends BaseUnitTest
{
    /**
     * @test
     */
    public function dateTimePropertyReturnsEmptySqlDefinition(): void
    {
        $property = new DateTimeProperty();
        $property->setName('birthday');
        $property->setDomainObject($this->buildDomainObject('TestModel'));

        self::assertSame('', $property->getSqlDefinition());
    }

    /**
     * @test
     */
    public function datePropertyReturnsEmptySqlDefinition(): void
    {
        $property = new DateProperty();
        $property->setName('birthday');
        $property->setDomainObject($this->buildDomainObject('TestModel'));

        self::assertSame('', $property->getSqlDefinition());
    }

    /**
     * @test
     */
    public function timePropertyReturnsEmptySqlDefinition(): void
    {
        $property = new TimeProperty();
        $property->setName('startTime');
        $property->setDomainObject($this->buildDomainObject('TestModel'));

        self::assertSame('', $property->getSqlDefinition());
    }

    /**
     * @test
     */
    public function timeSecPropertyReturnsEmptySqlDefinition(): void
    {
        $property = new TimeSecProperty();
        $property->setName('startTime');
        $property->setDomainObject($this->buildDomainObject('TestModel'));

        self::assertSame('', $property->getSqlDefinition());
    }
}
