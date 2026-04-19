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

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\Format;

use EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;
use EBT\ExtensionBuilder\ViewHelpers\Format\RemoveMultipleNewlinesViewHelper;

class RemoveMultipleNewlinesViewHelperTest extends ViewHelperBaseTestcase
{
    public static function renderDataProvider(): array
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

    #[\PHPUnit\Framework\Attributes\DataProvider('renderDataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = new RemoveMultipleNewlinesViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($template) {
            return $template;
        });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
