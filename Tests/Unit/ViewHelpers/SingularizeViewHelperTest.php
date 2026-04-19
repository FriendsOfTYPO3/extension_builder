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

use EBT\ExtensionBuilder\ViewHelpers\SingularizeViewHelper;

class SingularizeViewHelperTest extends ViewHelperBaseTestcase
{
    public static function renderDataProvider(): array
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

    #[\PHPUnit\Framework\Attributes\DataProvider('renderDataProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function renderWithChildren(string $singular, string $expected): void
    {
        $viewHelper = new SingularizeViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($singular) {
            return $singular;
        });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
