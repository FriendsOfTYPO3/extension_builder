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

namespace EBT\ExtensionBuilder\Domain\Model;

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use PhpParser\Node;

/**
 * Provides methods that are common to Class, File and Namespace objects
 */
class Container extends AbstractObject
{
    /**
     * associative array constName => constValue
     *
     * @var array
     */
    protected $constants = [];
    /**
     * @var array
     */
    protected $preIncludes = [];
    /**
     * @var array
     */
    protected $postIncludes = [];
    /**
     * @var FunctionObject[]
     */
    protected $functions = [];
    /**
     * Contains all statements that occurred before the first class statement.
     *
     * @var array
     */
    protected $preClassStatements = [];
    /**
     * Contains all statements that occurred after the first class statement they
     * will be rewritten after the last class!
     *
     * @var array
     */
    protected $postClassStatements = [];
    /**
     * @var ClassObject[]
     */
    protected $classes = [];
    /**
     * array with alias declarations
     *
     * Each declaration is an array of the following type:
     * array(name => alias)
     *
     * @var string[]
     */
    protected $aliasDeclarations = [];

    /**
     * @return ClassObject
     */
    public function getFirstClass()
    {
        $classes = $this->getClasses();
        return reset($classes);
    }

    /**
     * @param ClassObject $class
     */
    public function addClass(ClassObject $class): void
    {
        $this->classes[] = $class;
    }

    /**
     * @param array \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
     */
    public function setClasses($classes): void
    {
        $this->classes = $classes;
    }

    /**
     * @return ClassObject[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setConstant($name, $value): void
    {
        $this->constants[$name] = $value;
    }

    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * @param string $constantName
     * @return mixed
     */
    public function getConstant(string $constantName)
    {
        return $this->constants[$constantName] ?? null;
    }

    public function removeConstant(string $constantName): bool
    {
        if (isset($this->constants[$constantName])) {
            unset($this->constants[$constantName]);
            return true;
        }
        return false;
    }

    /**
     * @param $postInclude
     */
    public function addPostInclude($postInclude): void
    {
        $this->postIncludes[] = $postInclude;
    }

    public function getPostIncludes(): array
    {
        return $this->postIncludes;
    }

    /**
     * @param $preInclude
     */
    public function addPreInclude($preInclude): void
    {
        $this->preIncludes[] = $preInclude;
    }

    public function getPreIncludes(): array
    {
        return $this->preIncludes;
    }

    /**
     * @param array FunctionObject[]
     */
    public function setFunctions(array $functions): void
    {
        $this->functions = $functions;
    }

    /**
     * @param FunctionObject $function
     */
    public function addFunction(FunctionObject $function): void
    {
        $this->functions[$function->getName()] = $function;
    }

    /**
     * @return FunctionObject[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    public function getFunction(string $name): ?FunctionObject
    {
        return $this->functions[$name] ?? null;
    }

    /**
     * @param Node $postClassStatements
     */
    public function addPostClassStatements($postClassStatements): void
    {
        $this->postClassStatements[] = $postClassStatements;
    }

    public function getPostClassStatements(): array
    {
        return $this->postClassStatements;
    }

    public function addPreClassStatements(Node $preClassStatements): void
    {
        $this->preClassStatements[] = $preClassStatements;
    }

    public function getPreClassStatements(): array
    {
        return $this->preClassStatements;
    }

    /**
     * @param array $aliasDeclaration
     */
    public function addAliasDeclaration($aliasDeclaration): void
    {
        $this->aliasDeclarations[] = $aliasDeclaration;
    }

    /**
     * @return string[]
     */
    public function getAliasDeclarations(): array
    {
        return $this->aliasDeclarations;
    }
}
