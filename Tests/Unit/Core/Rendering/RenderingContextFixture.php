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

namespace EBT\ExtensionBuilder\Tests\Unit\Core\Rendering;

use PHPUnit\Framework\MockObject\Generator;
use TYPO3Fluid\Fluid\Core\Cache\FluidCacheInterface;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\ErrorHandler\ErrorHandlerInterface;
use TYPO3Fluid\Fluid\Core\ErrorHandler\StandardErrorHandler;
use TYPO3Fluid\Fluid\Core\Parser\Configuration;
use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;
use TYPO3Fluid\Fluid\Core\Parser\TemplateProcessorInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInvoker;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3Fluid\Fluid\View\TemplatePaths;

class RenderingContextFixture implements RenderingContextInterface
{
    public ErrorHandlerInterface $errorHandler;

    public VariableProviderInterface $variableProvider;

    public ViewHelperVariableContainer $viewHelperVariableContainer;

    public ViewHelperResolver $viewHelperResolver;

    public ViewHelperInvoker $viewHelperInvoker;

    public TemplateParser $templateParser;

    public TemplateCompiler $templateCompiler;

    public TemplatePaths $templatePaths;

    public FluidCacheInterface $cache;

    /**
     * @var TemplateProcessorInterface[]
     */
    public array $templateProcessors = [];

    public array $expressionNodeTypes = [];

    public string $controllerName = 'Default';

    public string $controllerAction = 'Default';

    public bool $cacheDisabled = false;

    public function __construct()
    {
        $mockBuilder = new Generator();
        $this->variableProvider = $mockBuilder->getMock(VariableProviderInterface::class);
        $this->viewHelperVariableContainer = $mockBuilder->getMock(ViewHelperVariableContainer::class, ['dummy']);
        $this->viewHelperResolver = $mockBuilder->getMock(ViewHelperResolver::class, ['dummy']);
        $this->viewHelperInvoker = $mockBuilder->getMock(ViewHelperInvoker::class, ['dummy']);
        $this->templateParser = $mockBuilder->getMock(TemplateParser::class, ['dummy']);
        $this->templateCompiler = $mockBuilder->getMock(TemplateCompiler::class, ['dummy']);
        $this->templatePaths = $mockBuilder->getMock(TemplatePaths::class, ['dummy']);
        $this->cache = $mockBuilder->getMock(FluidCacheInterface::class);
    }

    /**
     * @return ErrorHandlerInterface
     */
    public function getErrorHandler()
    {
        return $this->errorHandler ?? new StandardErrorHandler();
    }

    /**
     * @param ErrorHandlerInterface $errorHandler
     */
    public function setErrorHandler(ErrorHandlerInterface $errorHandler): void
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * Injects the template variable container containing all variables available through ObjectAccessors
     * in the template
     *
     * @param VariableProviderInterface $variableProvider The template variable container to set
     */
    public function setVariableProvider(VariableProviderInterface $variableProvider): void
    {
        $this->variableProvider = $variableProvider;
    }

    /**
     * @param ViewHelperVariableContainer $viewHelperVariableContainer
     */
    public function setViewHelperVariableContainer(ViewHelperVariableContainer $viewHelperVariableContainer): void
    {
        $this->viewHelperVariableContainer = $viewHelperVariableContainer;
    }

    /**
     * Get the template variable container
     *
     * @return VariableProviderInterface The Template Variable Container
     */
    public function getVariableProvider()
    {
        return $this->variableProvider;
    }

    /**
     * Get the ViewHelperVariableContainer
     *
     * @return ViewHelperVariableContainer
     */
    public function getViewHelperVariableContainer()
    {
        return $this->viewHelperVariableContainer;
    }

    /**
     * @return ViewHelperResolver
     */
    public function getViewHelperResolver()
    {
        return $this->viewHelperResolver;
    }

    /**
     * @param ViewHelperResolver $viewHelperResolver
     */
    public function setViewHelperResolver(ViewHelperResolver $viewHelperResolver): void
    {
        $this->viewHelperResolver = $viewHelperResolver;
    }

    /**
     * @return ViewHelperInvoker
     */
    public function getViewHelperInvoker()
    {
        return $this->viewHelperInvoker;
    }

    /**
     * @param ViewHelperInvoker $viewHelperInvoker
     */
    public function setViewHelperInvoker(ViewHelperInvoker $viewHelperInvoker): void
    {
        $this->viewHelperInvoker = $viewHelperInvoker;
    }

    /**
     * Inject the Template Parser
     *
     * @param TemplateParser $templateParser The template parser
     */
    public function setTemplateParser(TemplateParser $templateParser): void
    {
        $this->templateParser = $templateParser;
    }

    /**
     * @return TemplateParser
     */
    public function getTemplateParser()
    {
        return $this->templateParser;
    }

    /**
     * @param TemplateCompiler $templateCompiler
     */
    public function setTemplateCompiler(TemplateCompiler $templateCompiler): void
    {
        $this->templateCompiler = $templateCompiler;
    }

    /**
     * @return TemplateCompiler
     */
    public function getTemplateCompiler()
    {
        return $this->templateCompiler;
    }

    /**
     * @return TemplatePaths
     */
    public function getTemplatePaths()
    {
        return $this->templatePaths;
    }

    /**
     * @param TemplatePaths $templatePaths
     */
    public function setTemplatePaths(TemplatePaths $templatePaths): void
    {
        $this->templatePaths = $templatePaths;
    }

    /**
     * Delegation: Set the cache used by this View's compiler
     *
     * @param FluidCacheInterface $cache
     */
    public function setCache(FluidCacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @return FluidCacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return bool
     */
    public function isCacheEnabled()
    {
        return !$this->cacheDisabled;
    }

    /**
     * Delegation: Set TemplateProcessor instances in the parser
     * through a public API.
     *
     * @param TemplateProcessorInterface[] $templateProcessors
     */
    public function setTemplateProcessors(array $templateProcessors): void
    {
        $this->templateProcessors = $templateProcessors;
    }

    /**
     * @return TemplateProcessorInterface[]
     */
    public function getTemplateProcessors()
    {
        return $this->templateProcessors;
    }

    /**
     * @return array
     */
    public function getExpressionNodeTypes()
    {
        return $this->expressionNodeTypes;
    }

    /**
     * @param array $expressionNodeTypes
     */
    public function setExpressionNodeTypes(array $expressionNodeTypes): void
    {
        $this->expressionNodeTypes = $expressionNodeTypes;
    }

    /**
     * Build parser configuration
     *
     * @return Configuration
     */
    public function buildParserConfiguration()
    {
        return new Configuration();
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param string $controllerName
     */
    public function setControllerName($controllerName): void
    {
        $this->controllerName;
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    /**
     * @param string $action
     */
    public function setControllerAction($action): void
    {
        $this->controllerAction = $action;
    }
}
