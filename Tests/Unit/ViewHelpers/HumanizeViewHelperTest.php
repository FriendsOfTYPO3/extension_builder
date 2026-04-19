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

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\ViewHelpers\HumanizeViewHelper;

class HumanizeViewHelperTest extends ViewHelperBaseTestcase
{
    public static function renderDataProvider(): array
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

    #[\PHPUnit\Framework\Attributes\DataProvider('renderDataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function renderWithContentArgument(string $string, string $expected): void
    {
        $viewHelper = new HumanizeViewHelper();

        $this->arguments = [
            'string' => $string,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('renderDataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function renderWithChildren(string $string, string $expected): void
    {
        $viewHelper = new HumanizeViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($string) {
            return $string;
        });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
