<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\Format;

use EBT\ExtensionBuilder\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;
use EBT\ExtensionBuilder\ViewHelpers\Format\UppercaseFirstViewHelper;

class UppercaseFirstViewHelperTest extends ViewHelperBaseTestcase
{
    public function renderDataProvider(): array
    {
        return [
            'uppercaseFirstLetter' => [
                'className',
                'ClassName',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderDataProvider
     */
    public function renderWithChildren(string $template, string $expected): void
    {
        $viewHelper = new UppercaseFirstViewHelper();
        $viewHelper->setRenderChildrenClosure(function () use ($template) { return $template; });

        $this->injectDependenciesIntoViewHelper($viewHelper);

        self::assertEquals($expected, $viewHelper->initializeArgumentsAndRender());
    }
}
