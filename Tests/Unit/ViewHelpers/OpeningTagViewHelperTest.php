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

use EBT\ExtensionBuilder\ViewHelpers\OpeningTagViewHelper;

class OpeningTagViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            [
                'tag',
                '<tag>',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = new OpeningTagViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($template) {
            return $template;
        });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
