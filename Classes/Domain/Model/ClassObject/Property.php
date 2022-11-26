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

namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;

use EBT\ExtensionBuilder\Domain\Model\AbstractObject;
use PhpParser\NodeAbstract;

/**
 * property representing a "property" in the context of software development
 */
class Property extends AbstractObject
{
    /**
     * PHP var type of this property (read from "@var" annotation in doc comment)
     */
    protected string $varType = '';
    /**
     * @var mixed
     */
    protected $default;
    /**
     * @var mixed
     */
    protected $value;
    /**
     * In case of properties of type array we need to preserve the parsed statements
     * to be able to reapply the original linebrakes.
     */
    protected ?NodeAbstract $defaultValueNode = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getVarType(): string
    {
        return $this->varType;
    }

    public function setVarType(string $varType): void
    {
        $this->varType = $varType;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default): void
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
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * This is a helper function to be called in fluid if conditions it returns true
     * even if the default value is 0 or an empty string or "false".
     *
     * @return bool
     */
    public function getHasDefaultValue(): bool
    {
        return isset($this->default) && $this->default !== null;
    }

    /**
     * This is a helper function to be called in fluid if conditions it returns true
     * even if the value is 0 or an empty string or "false".
     *
     * @return bool
     */
    public function getHasValue(): bool
    {
        return isset($this->value) && $this->value !== null;
    }

    public function setDefaultValueNode(NodeAbstract $defaultValueNode): void
    {
        $this->defaultValueNode = $defaultValueNode;
    }

    public function getDefaultValueNode(): ?NodeAbstract
    {
        return $this->defaultValueNode;
    }
}
