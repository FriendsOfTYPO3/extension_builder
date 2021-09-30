<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\ViewHelpers\SingularizeViewHelper;

class SingularizeViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            [
                'horses',
                'horse',
            ],
            [
                'information',
                'information',
            ],
            [
                'children',
                'child',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $singular, string $expected): void
    {
        $viewHelper = $this->getAccessibleMock(SingularizeViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren')->willReturn($singular);

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
