<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\Format;

use EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;
use EBT\ExtensionBuilder\ViewHelpers\Format\QuoteStringViewHelper;

class QuoteStringViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'someTextWithDoubleQuotes' => [
                'some "text" with double quotes',
                'some \"text\" with double quotes',
            ],
            'someTextWithSingleQuotes' => [
                "some 'text' with single quotes",
                "some \\'text\\' with single quotes",
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithContentArgument(string $value, string $expected): void
    {
        $viewHelper = new QuoteStringViewHelper();

        $this->arguments = [
            'value' => $value,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = $this->getAccessibleMock(QuoteStringViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren')->willReturn($template);

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
