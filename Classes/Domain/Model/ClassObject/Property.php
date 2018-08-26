<?php
namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;

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

use EBT\ExtensionBuilder\Domain\Model\AbstractObject;

/**
 * property representing a "property" in the context of software development
 */
class Property extends AbstractObject
{
    /**
     * PHP var type of this property (read from "@var" annotation in doc comment)
     *
     * @var string
     */
    protected $varType = '';
    /**
     * @var mixed
     */
    protected $default = null;
    /**
     * @var mixed
     */
    protected $value = null;
    /**
     * In case of properties of type array we need to preserve the parsed statements
     * to be able to reapply the original linebrakes.
     *
     * @var \PhpParser\NodeAbstract
     */
    protected $defaultValueNode = null;

    /**
     * @param string $name
     * @param string
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getVarType()
    {
        return $this->varType;
    }

    /**
     * @param string $varType
     * @return void
     */
    public function setVarType($varType)
    {
        $this->setTag('var', [$varType]);
        $this->varType = $varType;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     * @return void
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * This is a helper function to be called in fluid if conditions it returns true
     * even if the default value is 0 or an empty string or "false".
     *
     * @return bool
     */
    public function getHasDefaultValue()
    {
        if (isset($this->default) && $this->default !== null) {
            return true;
        }
        return false;
    }

    /**
     * This is a helper function to be called in fluid if conditions it returns true
     * even if the value is 0 or an empty string or "false".
     *
     * @return bool
     */
    public function getHasValue()
    {
        if (isset($this->value) && $this->value !== null) {
            return true;
        }
        return false;
    }

    /**
     * @param \PhpParser\NodeAbstract $defaultValueNode
     * @return void
     */
    public function setDefaultValueNode($defaultValueNode)
    {
        $this->defaultValueNode = $defaultValueNode;
    }

    /**
     * @return \PhpParser\NodeAbstract
     */
    public function getDefaultValueNode()
    {
        return $this->defaultValueNode;
    }
}
