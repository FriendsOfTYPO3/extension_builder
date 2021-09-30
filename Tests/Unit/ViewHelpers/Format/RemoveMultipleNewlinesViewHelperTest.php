<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\Format;

use EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;
use EBT\ExtensionBuilder\ViewHelpers\Format\RemoveMultipleNewlinesViewHelper;

class RemoveMultipleNewlinesViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'noNewLineShouldBeKeptAsIs' => [
                'Line 1 Line 2',
                'Line 1 Line 2',
            ],
            'oneNewLineShouldNotBeRemoved' => [
                'Line 1' . chr(10) . 'Line 2',
                'Line 1' . chr(10) . 'Line 2',
            ],
            'twoNewLinesShouldBeReplacedWithOneNewLine' => [
                'Line 1' . chr(10) . chr(10) . 'Line 2',
                'Line 1' . chr(10) . 'Line 2',
            ],
            'threeNewLinesShouldBeReplacedWithOneNewLine' => [
                'Line 1' . chr(10) . chr(10) . chr(10) . 'Line 2',
                'Line 1' . chr(10) . 'Line 2',
            ],
            'fourNewLinesWithSomeWhiteSpacesShouldBeReplacedWithOneNewLine' => [
                '  Line 1' . chr(10) . chr(10) . chr(10) . chr(10) . 'Line 2  ',
                'Line 1' . chr(10) . 'Line 2',
            ],
            'oneNewLineWithSomeHTMLShouldBeReplacedWithOneNewLine' => [
                'Line 1' . chr(10) . 'Line 2<tr>',
                'Line 1' . chr(10) . 'Line 2<tr>',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = $this->getAccessibleMock(RemoveMultipleNewlinesViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren')->willReturn($template);

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
