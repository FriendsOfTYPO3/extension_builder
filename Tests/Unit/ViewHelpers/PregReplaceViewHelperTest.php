<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\ViewHelpers\PregReplaceViewHelper;

class PregReplaceViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'add curly brackets' => [
                '/this/',
                'that',
                'this is awesome',
                'that is awesome',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithContentArgument(string $match, string $replace, string $subject, string $expected): void
    {
        $viewHelper = new PregReplaceViewHelper();

        $this->arguments = [
            'match' => $match,
            'replace' => $replace,
            'subject' => $subject,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $match, string $replace, string $subject, string $expected): void
    {
        $viewHelper = $this->getAccessibleMock(PregReplaceViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren')->willReturn($subject);

        $this->arguments = [
            'match' => $match,
            'replace' => $replace,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
