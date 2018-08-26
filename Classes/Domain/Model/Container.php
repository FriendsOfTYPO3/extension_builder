<?php
namespace EBT\ExtensionBuilder\Domain\Model;

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
     * @var \EBT\ExtensionBuilder\Domain\Model\FunctionObject[]
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
     * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
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
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    public function getFirstClass()
    {
        $classes = $this->getClasses();
        return reset($classes);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $class
     * @return void
     */
    public function addClass(ClassObject $class)
    {
        $this->classes[] = $class;
    }

    /**
     * @param array \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
     * @return void
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setConstant($name, $value)
    {
        $this->constants[$name] = $value;
    }

    /**
     * @return array constants
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * @param $constantName
     * @return mixed
     */
    public function getConstant($constantName)
    {
        if (isset($this->constants[$constantName])) {
            return $this->constants[$constantName];
        } else {
            return null;
        }
    }

    /**
     * @param string $constantName
     * @return bool true if successfully removed
     */
    public function removeConstant($constantName)
    {
        if (isset($this->constants[$constantName])) {
            unset($this->constants[$constantName]);
            return true;
        }
        return false;
    }

    /**
     * @param $postInclude
     * @return void
     */
    public function addPostInclude($postInclude)
    {
        $this->postIncludes[] = $postInclude;
    }

    /**
     * @return array
     */
    public function getPostIncludes()
    {
        return $this->postIncludes;
    }

    /**
     * @param $preInclude
     * @return void
     */
    public function addPreInclude($preInclude)
    {
        $this->preIncludes[] = $preInclude;
    }

    /**
     * @return array
     */
    public function getPreIncludes()
    {
        return $this->preIncludes;
    }

    /**
     * @param array FunctionObject[]
     * @return void
     */
    public function setFunctions(array $functions)
    {
        $this->functions = $functions;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\FunctionObject $function
     * @return void
     */
    public function addFunction(FunctionObject $function)
    {
        $this->functions[$function->getName()] = $function;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @param string $name
     * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject
     */
    public function getFunction($name)
    {
        if (isset($this->functions[$name])) {
            return $this->functions[$name];
        } else {
            return null;
        }
    }

    /**
     * @param Node $postClassStatements
     * @return void
     */
    public function addPostClassStatements($postClassStatements)
    {
        $this->postClassStatements[] = $postClassStatements;
    }

    /**
     * @return array
     */
    public function getPostClassStatements()
    {
        return $this->postClassStatements;
    }

    /**
     * @param Node $preClassStatements
     * @return void
     */
    public function addPreClassStatements($preClassStatements)
    {
        $this->preClassStatements[] = $preClassStatements;
    }

    /**
     * @return array
     */
    public function getPreClassStatements()
    {
        return $this->preClassStatements;
    }

    /**
     * @param string $aliasDeclaration
     * @return void
     */
    public function addAliasDeclaration($aliasDeclaration)
    {
        $this->aliasDeclarations[] = $aliasDeclaration;
    }

    /**
     * @return string[]
     */
    public function getAliasDeclarations()
    {
        return $this->aliasDeclarations;
    }
}
