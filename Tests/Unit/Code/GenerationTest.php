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

namespace EBT\ExtensionBuilder\Tests\Unit\Code;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\IntegerProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;

/**
 * Unit tests validating that code templates are TYPO3 13 compatible.
 *
 * These tests read template files directly and assert their content, verifying
 * deprecated patterns have been removed and required TYPO3 13 patterns are present.
 * They also test DomainObject helper methods used by templates.
 */
class GenerationTest extends BaseUnitTest
{
    private string $tcaPartialPath;
    private string $modelTemplatePath;

    protected function setUp(): void
    {
        parent::setUp();
        $packageRoot = dirname(__DIR__, 3);
        $this->tcaPartialPath = $packageRoot . '/Resources/Private/CodeTemplates/Extbase/Partials/TCA/';
        $this->modelTemplatePath = $packageRoot . '/Resources/Private/CodeTemplates/Extbase/Classes/Domain/Model/Model.phpt';
    }

    // --- RichTextProperty TCA template ---

    /**
     * @test
     */
    public function richTextPropertyTemplateDoesNotContainDeprecatedEval(): void
    {
        $content = $this->readTemplate('RichTextProperty.phpt');
        self::assertStringNotContainsString(
            "'eval'",
            $content,
            'RichTextProperty.phpt must not use deprecated eval key (removed in TYPO3 13)'
        );
    }

    /**
     * @test
     */
    public function richTextPropertyTemplateUsesRequiredFieldForValidation(): void
    {
        $content = $this->readTemplate('RichTextProperty.phpt');
        self::assertStringContainsString(
            "'required'",
            $content,
            'RichTextProperty.phpt should use required key instead of eval for TYPO3 13 compatibility'
        );
    }

    /**
     * @test
     */
    public function richTextPropertyTemplateHasEnableRichtextConfig(): void
    {
        $content = $this->readTemplate('RichTextProperty.phpt');
        self::assertStringContainsString(
            "'enableRichtext' => true",
            $content,
            'RichTextProperty.phpt must set enableRichtext'
        );
    }

    /**
     * @test
     */
    public function richTextPropertyTemplateHasTextType(): void
    {
        $content = $this->readTemplate('RichTextProperty.phpt');
        self::assertStringContainsString(
            "'type' => 'text'",
            $content,
            'RichTextProperty.phpt must use type => text'
        );
    }

    // --- SlugProperty TCA template ---

    /**
     * @test
     */
    public function slugPropertyTemplateDoesNotContainDeprecatedEval(): void
    {
        $content = $this->readTemplate('SlugProperty.phpt');
        self::assertStringNotContainsString(
            "'eval'",
            $content,
            'SlugProperty.phpt must not use deprecated eval key (removed in TYPO3 13)'
        );
    }

    /**
     * @test
     */
    public function slugPropertyTemplateDoesNotHardcodeTitle(): void
    {
        $content = $this->readTemplate('SlugProperty.phpt');
        self::assertStringNotContainsString(
            "['title']",
            $content,
            'SlugProperty.phpt must not hardcode title as slug source field'
        );
    }

    /**
     * @test
     */
    public function slugPropertyTemplateUsesDomainObjectFirstStringPropertyFieldName(): void
    {
        $content = $this->readTemplate('SlugProperty.phpt');
        self::assertStringContainsString(
            'firstStringPropertyFieldName',
            $content,
            'SlugProperty.phpt should use domainObject.firstStringPropertyFieldName for dynamic field detection'
        );
    }

    /**
     * @test
     */
    public function slugPropertyTemplateHasGeneratorOptions(): void
    {
        $content = $this->readTemplate('SlugProperty.phpt');
        self::assertStringContainsString(
            "'generatorOptions'",
            $content,
            'SlugProperty.phpt must include generatorOptions config'
        );
    }

    /**
     * @test
     */
    public function slugPropertyTemplateHasFallbackCharacter(): void
    {
        $content = $this->readTemplate('SlugProperty.phpt');
        self::assertStringContainsString(
            "'fallbackCharacter'",
            $content,
            'SlugProperty.phpt must set fallbackCharacter'
        );
    }

    /**
     * @test
     */
    public function slugPropertyTemplateHasSlugType(): void
    {
        $content = $this->readTemplate('SlugProperty.phpt');
        self::assertStringContainsString(
            "'type' => 'slug'",
            $content,
            'SlugProperty.phpt must use type => slug'
        );
    }

    // --- Model.phpt template ---

    /**
     * @test
     */
    public function modelTemplateIsPropertyMethodHasBoolReturnType(): void
    {
        $content = file_get_contents($this->modelTemplatePath);
        self::assertStringContainsString(
            'public function isProperty(): bool',
            $content,
            'Model.phpt isProperty() must declare : bool return type for TYPO3 13 / PHP 8.3 compatibility'
        );
    }

    /**
     * @test
     */
    public function modelTemplateIsPropertyMethodCastsValueToBool(): void
    {
        $content = file_get_contents($this->modelTemplatePath);
        self::assertStringContainsString(
            'return (bool) $this->property',
            $content,
            'Model.phpt isProperty() must cast return value to bool'
        );
    }

    /**
     * @test
     */
    public function modelTemplateGetterHasStringReturnType(): void
    {
        $content = file_get_contents($this->modelTemplatePath);
        self::assertStringContainsString(
            'public function getProperty(): string',
            $content,
            'Model.phpt getProperty() must have explicit string return type'
        );
    }

    /**
     * @test
     */
    public function modelTemplateSetterHasVoidReturnType(): void
    {
        $content = file_get_contents($this->modelTemplatePath);
        self::assertStringContainsString(
            'public function setProperty(string $property): void',
            $content,
            'Model.phpt setProperty() must have explicit void return type'
        );
    }

    /**
     * @test
     */
    public function modelTemplateUsesDeclareStrictTypes(): void
    {
        $content = file_get_contents($this->modelTemplatePath);
        self::assertStringContainsString(
            'declare(strict_types=1)',
            $content,
            'Model.phpt must include declare(strict_types=1)'
        );
    }

    // --- Relation TCA templates ---

    /**
     * @test
     */
    public function manyToOneRelationTemplateHasTypeSelect(): void
    {
        $content = $this->readTemplate('ManyToOneRelation.phpt');
        self::assertStringContainsString(
            "'type' => 'select'",
            $content,
            'ManyToOneRelation.phpt must declare type => select for TYPO3 13 compatibility'
        );
    }

    /**
     * @test
     */
    public function manyToManyRelationTemplateHasTypeSelect(): void
    {
        $content = $this->readTemplate('ManyToManyRelation.phpt');
        self::assertStringContainsString(
            "'type' => 'select'",
            $content,
            'ManyToManyRelation.phpt must declare type => select'
        );
    }

    /**
     * @test
     */
    public function zeroToOneRelationTemplateHasTypeSelectForSelectRenderType(): void
    {
        $content = $this->readTemplate('ZeroToOneRelation.phpt');
        self::assertStringContainsString(
            "'type' => 'select'",
            $content,
            'ZeroToOneRelation.phpt must declare type => select for select render types'
        );
    }

    /**
     * @test
     */
    public function zeroToManyRelationTemplateHasTypeSelectForSelectRenderType(): void
    {
        $content = $this->readTemplate('ZeroToManyRelation.phpt');
        self::assertStringContainsString(
            "'type' => 'select'",
            $content,
            'ZeroToManyRelation.phpt must declare type => select for select render types'
        );
    }

    /**
     * @test
     */
    public function zeroToManyRelationTemplateHasTypeInlineForDefaultCase(): void
    {
        $content = $this->readTemplate('ZeroToManyRelation.phpt');
        self::assertStringContainsString(
            "'type' => 'inline'",
            $content,
            'ZeroToManyRelation.phpt must use type => inline for the default (non-select) render case'
        );
    }

    // --- DomainObject::getFirstStringPropertyFieldName() ---

    /**
     * @test
     */
    public function getFirstStringPropertyFieldNameReturnsFieldNameOfFirstStringProperty(): void
    {
        $domainObject = $this->buildDomainObject('TestModel');
        $property = new StringProperty('myTitle');
        $property->setDomainObject($domainObject);
        $domainObject->addProperty($property);

        self::assertSame(
            'my_title',
            $domainObject->getFirstStringPropertyFieldName(),
            'Should return the field name (snake_case) of the first StringProperty'
        );
    }

    /**
     * @test
     */
    public function getFirstStringPropertyFieldNameFallsBackToTitleWhenNoStringProperty(): void
    {
        $domainObject = $this->buildDomainObject('TestModel');
        $property = new IntegerProperty('count');
        $property->setDomainObject($domainObject);
        $domainObject->addProperty($property);

        self::assertSame(
            'title',
            $domainObject->getFirstStringPropertyFieldName(),
            'Should fall back to title when no StringProperty exists'
        );
    }

    /**
     * @test
     */
    public function getFirstStringPropertyFieldNameFallsBackToTitleWhenPropertiesAreEmpty(): void
    {
        $domainObject = $this->buildDomainObject('TestModel');

        self::assertSame(
            'title',
            $domainObject->getFirstStringPropertyFieldName(),
            'Should fall back to title when property list is empty'
        );
    }

    /**
     * @test
     */
    public function getFirstStringPropertyFieldNameSkipsNonStringPropertiesBeforeStringProperty(): void
    {
        $domainObject = $this->buildDomainObject('TestModel');

        $intProp = new IntegerProperty('sortOrder');
        $intProp->setDomainObject($domainObject);
        $domainObject->addProperty($intProp);

        $boolProp = new BooleanProperty('active');
        $boolProp->setDomainObject($domainObject);
        $domainObject->addProperty($boolProp);

        $strProp = new StringProperty('headline');
        $strProp->setDomainObject($domainObject);
        $domainObject->addProperty($strProp);

        self::assertSame(
            'headline',
            $domainObject->getFirstStringPropertyFieldName(),
            'Should skip integer and boolean properties and return the first StringProperty field name'
        );
    }

    /**
     * @test
     */
    public function getFirstStringPropertyFieldNameReturnsFirstNotSubsequentStringProperty(): void
    {
        $domainObject = $this->buildDomainObject('TestModel');

        $first = new StringProperty('name');
        $first->setDomainObject($domainObject);
        $domainObject->addProperty($first);

        $second = new StringProperty('description');
        $second->setDomainObject($domainObject);
        $domainObject->addProperty($second);

        self::assertSame(
            'name',
            $domainObject->getFirstStringPropertyFieldName(),
            'Should return the FIRST StringProperty field name, not subsequent ones'
        );
    }

    // --- Helper ---

    private function readTemplate(string $filename): string
    {
        $path = $this->tcaPartialPath . $filename;
        self::assertFileExists($path, sprintf('Template file %s does not exist', $filename));
        return file_get_contents($path);
    }
}
