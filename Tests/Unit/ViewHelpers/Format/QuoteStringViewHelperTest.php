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
use EBT\ExtensionBuilder\ViewHelpers\Format\QuoteStringViewHelper;

class QuoteStringViewHelperTest extends ViewHelperBaseTestcase
{
    public static function renderDataProvider(): array
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

    #[\PHPUnit\Framework\Attributes\DataProvider('renderDataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function renderWithContentArgument(string $value, string $expected): void
    {
        $viewHelper = new QuoteStringViewHelper();

        $this->arguments = [
            'value' => $value,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderDataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = new QuoteStringViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($template) {
            return $template;
        });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
