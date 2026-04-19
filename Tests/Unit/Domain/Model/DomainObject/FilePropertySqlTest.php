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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\FileProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\ImageProperty;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

/**
 * TYPO3 v13 auto-creates columns for type=file TCA fields,
 * so no SQL definition should be generated for file/image properties.
 */
class FilePropertySqlTest extends BaseUnitTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function filePropertyReturnsEmptySqlDefinition(): void
    {
        $property = new FileProperty();
        $property->setName('attachment');
        $property->setDomainObject($this->buildDomainObject('TestModel'));

        self::assertSame('', $property->getSqlDefinition());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function imagePropertyReturnsEmptySqlDefinition(): void
    {
        $property = new ImageProperty();
        $property->setName('photo');
        $property->setDomainObject($this->buildDomainObject('TestModel'));

        self::assertSame('', $property->getSqlDefinition());
    }
}
