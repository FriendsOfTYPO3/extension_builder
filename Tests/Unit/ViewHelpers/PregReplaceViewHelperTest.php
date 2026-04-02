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

use EBT\ExtensionBuilder\ViewHelpers\PregReplaceViewHelper;

class PregReplaceViewHelperTest extends ViewHelperBaseTestcase
{
    public static function renderDataProvider(): array
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
        $viewHelper = new PregReplaceViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($subject) {
            return $subject;
        });

        $this->arguments = [
            'match' => $match,
            'replace' => $replace,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
