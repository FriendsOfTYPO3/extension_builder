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

namespace EBT\ExtensionBuilder\Tests\Functional\FileGenerator\Property;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\SelectProperty;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class GeneratedSelectPropertyTcaTest extends BaseFunctionalTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function tcaContainsPlaceholderItemWhenNoItemsConfigured(): void
    {
        $domainObject = $this->buildDomainObject('ModelWithSelect');
        $property = new SelectProperty('color');
        $domainObject->addProperty($property);

        $tcaContent = $this->fileGenerator->generateTCA($domainObject);

        self::assertNotNull($tcaContent, 'generateTCA returned null');
        self::assertMatchesRegularExpression(
            "/\['label'\s*=>\s*'-- Label --',\s*'value'\s*=>\s*0\]/",
            $tcaContent,
            'TCA should contain placeholder item in associative format'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tcaContainsConfiguredItemsWithAssociativeFormat(): void
    {
        $domainObject = $this->buildDomainObject('ModelWithSelect');
        $property = new SelectProperty('status');
        $property->setSelectItems([
            ['label' => 'Active', 'value' => 'active'],
            ['label' => 'Inactive', 'value' => 'inactive'],
        ]);
        $domainObject->addProperty($property);

        $tcaContent = $this->fileGenerator->generateTCA($domainObject);

        self::assertNotNull($tcaContent, 'generateTCA returned null');
        self::assertMatchesRegularExpression(
            "/\['label'\s*=>\s*'Active',\s*'value'\s*=>\s*'active'\]/",
            $tcaContent,
            'TCA should contain first item in associative format'
        );
        self::assertMatchesRegularExpression(
            "/\['label'\s*=>\s*'Inactive',\s*'value'\s*=>\s*'inactive'\]/",
            $tcaContent,
            'TCA should contain second item in associative format'
        );
        self::assertStringNotContainsString(
            "'-- Label --'",
            $tcaContent,
            'TCA should not contain placeholder when items are configured'
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tcaItemsUseAssociativeArrayFormat(): void
    {
        $domainObject = $this->buildDomainObject('ModelWithSelect');
        $property = new SelectProperty('size');
        $property->setSelectItems([
            ['label' => 'Small', 'value' => 'small'],
        ]);
        $domainObject->addProperty($property);

        $tcaContent = $this->fileGenerator->generateTCA($domainObject);

        self::assertNotNull($tcaContent, 'generateTCA returned null');
        // Verify the TYPO3 13+ associative format — NOT the old positional format ['Small', 'small']
        self::assertMatchesRegularExpression(
            "/\['label'\s*=>\s*'Small',\s*'value'\s*=>\s*'small'\]/",
            $tcaContent,
            'TCA items must use associative array format required since TYPO3 12.3'
        );
    }
}
