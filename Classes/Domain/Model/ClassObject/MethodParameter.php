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

/**
 * parameter representing a method parameter in the context of software
 * development
 */
class MethodParameter extends AbstractObject
{
    protected string $varType = '';
    protected string $typeHint = '';
    protected string $typeForParamTag = '';
    /**
     * @var mixed
     */
    protected $defaultValue;
    protected int $position = 0;
    protected bool $optional = false;
    protected int $startLine = -1;
    protected int $endLine = -1;
    protected bool $passedByReference = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getVarType(): string
    {
        if (empty($this->varType) && !empty($this->typeHint)) {
            return $this->typeHint;
        }
        return $this->varType;
    }

    public function setVarType(string $varType): self
    {
        $this->varType = $varType;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue = null): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function isOptional(): bool
    {
        return $this->optional;
    }

    public function setOptional(bool $optional): void
    {
        $this->optional = $optional;
    }

    public function isPassedByReference(): bool
    {
        return $this->passedByReference;
    }

    public function getPassedByReference(): bool
    {
        return $this->passedByReference;
    }

    public function setPassedByReference(bool $passedByReference): void
    {
        $this->passedByReference = $passedByReference;
    }

    public function getTypeHint(): string
    {
        return $this->typeHint;
    }

    public function setTypeHint(string $typeHint): self
    {
        $this->typeHint = $typeHint;
        return $this;
    }

    public function hasTypeHint(): bool
    {
        return !empty($this->typeHint);
    }

    public function setTypeForParamTag(string $typeForParamTag): void
    {
        $this->typeForParamTag = $typeForParamTag;
    }

    public function getTypeForParamTag(): string
    {
        return $this->typeForParamTag;
    }

    public function setStartLine(int $startLine): void
    {
        $this->startLine = $startLine;
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function setEndLine(int $endLine): void
    {
        $this->endLine = $endLine;
    }

    public function getEndLine(): int
    {
        return $this->endLine;
    }
}
