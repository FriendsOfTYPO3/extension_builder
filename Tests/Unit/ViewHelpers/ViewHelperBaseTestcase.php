<?php

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

use EBT\ExtensionBuilder\Tests\Unit\Core\Rendering\RenderingContextFixture;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * Base test class for testing view helpers
 */
abstract class ViewHelperBaseTestcase extends UnitTestCase
{
    protected ViewHelperVariableContainer $viewHelperVariableContainer;

    protected StandardVariableProvider $templateVariableContainer;

    protected RenderingContextFixture $renderingContext;

    /**
     * Mock contents of the $viewHelperVariableContainer in the format:
     * array(
     *  'Some\ViewHelper\Class' => array('key1' => 'value1', 'key2' => 'value2')
     * )
     *
     * @var array
     */
    protected array $viewHelperVariableContainerData = [];

    protected array $arguments = [];

    public function setUp(): void
    {
        $this->viewHelperVariableContainer = new ViewHelperVariableContainer();
        $this->templateVariableContainer = new StandardVariableProvider();
        $this->renderingContext = new RenderingContextFixture();
        $this->renderingContext->setVariableProvider($this->templateVariableContainer);
        $this->renderingContext->setViewHelperVariableContainer($this->viewHelperVariableContainer);
    }

    public function viewHelperVariableContainerExistsCallback(string $viewHelperName, string $key): bool
    {
        return isset($this->viewHelperVariableContainerData[$viewHelperName][$key]);
    }

    public function viewHelperVariableContainerGetCallback(string $viewHelperName, string $key): bool
    {
        return $this->viewHelperVariableContainerData[$viewHelperName][$key];
    }

    protected function injectDependenciesIntoViewHelper(AbstractViewHelper $viewHelper): void
    {
        $viewHelper->setRenderingContext($this->renderingContext);
        $viewHelper->setArguments($this->arguments);
    }
}
