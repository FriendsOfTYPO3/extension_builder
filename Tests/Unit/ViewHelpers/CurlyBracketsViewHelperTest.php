<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\ViewHelpers\CurlyBracketsViewHelper;

class CurlyBracketsViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'add curly brackets' => [
                'variable',
                '{variable}',
            ],
            'do not escape content' => [
                '{variable -> k:format.lowercaseFirst()}.property',
                '{{variable -> k:format.lowercaseFirst()}.property}',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = $this->getAccessibleMock(CurlyBracketsViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren')->willReturn($template);

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
