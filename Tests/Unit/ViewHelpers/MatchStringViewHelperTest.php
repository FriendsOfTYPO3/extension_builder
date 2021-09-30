<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers;

use EBT\ExtensionBuilder\ViewHelpers\MatchStringViewHelper;

class MatchStringViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
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
