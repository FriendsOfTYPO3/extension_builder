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

use EBT\ExtensionBuilder\ViewHelpers\MatchStringViewHelper;

class MatchStringViewHelperTest extends ViewHelperBaseTestcase
{
    public static function renderDataProvider(): array
    {
        return [
            'case insensitive with equal case' => [
                'TYPO3',
                'TYPO3 is awesome',
                false,
                true,
            ],
            'case insensitive with different case' => [
                'typo3',
                'TYPO3 is awesome',
                false,
                true,
            ],
            'case sensitive with different case' => [
                'TYPO3',
                'typo3 is awesome',
                true,
                false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithContentArgument(string $match, string $in, bool $caseSensitive, bool $expected): void
    {
        $viewHelper = new MatchStringViewHelper();

        $this->arguments = [
            'match' => $match,
            'in' => $in,
            'caseSensitive' => $caseSensitive,
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
