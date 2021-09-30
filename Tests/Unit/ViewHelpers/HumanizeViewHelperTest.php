<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\ViewHelpers\HumanizeViewHelper;

class HumanizeViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'make camel case word human readable' => [
                'BlogAuthor',
                'Blog Author',
            ],
            'make underscored words human readable' => [
                'blog_author',
                'Blog Author',
            ],
            'make escaped words human readable' => [
                'blog\\author',
                'Blog Author',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithContentArgument(string $string, string $expected): void
    {
        $viewHelper = new HumanizeViewHelper();

        $this->arguments = [
            'string' => $string,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $string, string $expected): void
    {
        $viewHelper = $this->getAccessibleMock(HumanizeViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren')->willReturn($string);

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
