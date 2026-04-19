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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Service\LocalizationService;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;

class LocalizationServiceTest extends BaseUnitTest
{
    protected LocalizationService $localizationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->localizationService = new LocalizationService();
        $this->localizationService->injectXliffParser($this->createMock(XliffParser::class));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function prepareLabelArrayPreservesDescriptionCaseForProperties(): void
    {
        $domainObject = $this->buildDomainObject('MyModel', true);
        $domainObject->setDescription('domain object description');

        $property = new StringProperty();
        $property->setName('myField');
        $property->setDescription('this should stay lowercase and not Be ucworded');
        $domainObject->addProperty($property);

        $this->extension->addDomainObject($domainObject);

        $labelArray = $this->localizationService->prepareLabelArray($this->extension);

        self::assertSame(
            'this should stay lowercase and not Be ucworded',
            $labelArray[$property->getDescriptionNamespace()]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function prepareLabelArrayPreservesDescriptionCaseForDomainObjects(): void
    {
        $domainObject = $this->buildDomainObject('MyModel', true);
        $domainObject->setDescription('lower case domain description — preserve this');

        $this->extension->addDomainObject($domainObject);

        $labelArray = $this->localizationService->prepareLabelArray($this->extension);

        self::assertSame(
            'lower case domain description — preserve this',
            $labelArray[$domainObject->getDescriptionNamespace()]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function prepareLabelArrayStillHumanizesNames(): void
    {
        $domainObject = $this->buildDomainObject('myModel', true);

        $property = new StringProperty();
        $property->setName('myField');
        $domainObject->addProperty($property);

        $this->extension->addDomainObject($domainObject);

        $labelArray = $this->localizationService->prepareLabelArray($this->extension);

        self::assertSame('My Model', $labelArray[$domainObject->getLabelNamespace()]);
        self::assertSame('My Field', $labelArray[$property->getLabelNamespace()]);
    }
}
