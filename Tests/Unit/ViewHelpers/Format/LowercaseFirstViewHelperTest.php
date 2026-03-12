<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\Format;

use EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;
use EBT\ExtensionBuilder\ViewHelpers\Format\LowercaseFirstViewHelper;

class LowercaseFirstViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'lowercaseFirstLetter' => [
                'ClassName',
                'className',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = new LowercaseFirstViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($template) { return $template; });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
