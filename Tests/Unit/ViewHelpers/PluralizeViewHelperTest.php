<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\ViewHelpers\PluralizeViewHelper;

class PluralizeViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            [
                'horse',
                'horses',
            ],
            [
                'information',
                'information',
            ],
            [
                'child',
                'children',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $singular, string $expected): void
    {
        $viewHelper = new PluralizeViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($singular) { return $singular; });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
