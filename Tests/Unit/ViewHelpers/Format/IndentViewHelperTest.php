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
use EBT\ExtensionBuilder\ViewHelpers\Format\IndentViewHelper;
use PHPUnit\Framework\MockObject\MockObject;

class IndentViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'viewHelperIndentsLineOneLevel' => [
                'Line 1' . chr(10) . 'Line 2',
                1,
                'Line 1' . chr(10) . '    Line 2',
            ],
            'viewHelperIndentsLineTwoLevels' => [
                'Line 1' . chr(10) . 'Line 2',
                2,
                'Line 1' . chr(10) . '        Line 2',
            ],
            'viewHelperIndentsMultipleLinesTwoLevels' => [
                'Line 1' . chr(10) . 'Line 2' . chr(10) . 'Line 3',
                2,
                'Line 1' . chr(10) . '        Line 2' . chr(10) . '        Line 3',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $template, int $level, string $expected): void
    {
        $viewHelper = $this->getAccessibleMock(IndentViewHelper::class, ['renderChildren']);
        $viewHelper->expects(self::once())->method('renderChildren')->willReturn($template);

        $this->arguments = [
            'indentation' => $level
        ];
        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
