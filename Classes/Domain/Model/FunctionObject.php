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

use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;

/**
 * Class FunctionObject
 */
class FunctionObject extends AbstractObject
{
    protected ?string $returnType = null;
    protected array $bodyStmts = [];
    /**
     * parameters
     *
     * @var MethodParameter[]
     */
    protected array $parameters = [];
    protected int $startLine = -1;
    protected int $endLine = -1;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * used when cloning methods from a template class
     */
    public function __clone()
    {
        $clonedParameters = [];
        foreach ($this->parameters as $parameter) {
            $clonedParameters[] = clone $parameter;
        }
        $this->parameters = $clonedParameters;
    }

    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    public function setReturnType(?string $returnType): self
    {
        $this->returnType = $returnType;
        return $this;
    }

    /**
     * Setter for body statements
     *
     * @param array $stmts
     * @return $this
     */
    public function setBodyStmts($stmts): self
    {
        if (!is_array($stmts)) {
            $stmts = [];
        }
        $this->bodyStmts = $stmts;
        return $this;
    }

    public function getBodyStmts(): array
    {
        return $this->bodyStmts;
    }

    /**
     * getter for parameters
     *
     * @return MethodParameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameterNames(): array
    {
        $parameterNames = [];
        if (is_array($this->parameters)) {
            /** @var MethodParameter $parameter */
            foreach ($this->parameters as $parameter) {
                $parameterNames[] = $parameter->getName();
            }
        }
        return $parameterNames;
    }

    public function getParameterByPosition(int $position): ?MethodParameter
    {
        return $this->parameters[$position] ?? null;
    }

    /**
     * adder for parameters
     *
     * @param MethodParameter[] $parameters
     * @return $this
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function setParameter(MethodParameter $parameter): self
    {
        $this->parameters[$parameter->getPosition()] = $parameter;
        return $this;
    }

    /**
     * replace a single parameter, depending on position
     *
     * @param MethodParameter $parameter
     */
    public function replaceParameter(MethodParameter $parameter): void
    {
        $this->parameters[$parameter->getPosition()] = $parameter;
    }

    public function removeParameter(string $parameterName, int $parameterPosition): bool
    {
        if (isset($this->parameters[$parameterPosition]) && $this->parameters[$parameterPosition]->getName() === $parameterName) {
            unset($this->parameters[$parameterPosition]);
            $this->updateParamTags();
            return true;
        }

        return false;
    }

    public function renameParameter(string $oldName, string $newName, int $parameterPosition): bool
    {
        if (isset($this->parameters[$parameterPosition])) {
            $parameter = $this->parameters[$parameterPosition];
            if ($parameter->getName() == $oldName) {
                $parameter->setName($newName);
                $this->parameters[$parameterPosition] = $parameter;
                return true;
            }
        }
        return false;
    }

    /**
     * TODO: The sorting of tags/annotations should be controlled
     *
     * @return array
     */
    public function getAnnotations(): array
    {
        $annotations = parent::getAnnotations();
        if (is_array($this->parameters) && count($this->parameters) > 0 && !$this->isTaggedWith('param')) {
            $paramTags = [];
            /** @var MethodParameter $parameter */
            foreach ($this->parameters as $parameter) {
                $paramTags[] = 'param ' . strtolower($parameter->getVarType()) . '$' . $parameter->getName();
            }
            $annotations = array_merge($paramTags, $annotations);
        }
        if (!$this->isTaggedWith('return')) {
            $annotations[] = 'return';
        }
        return $annotations;
    }

    /**
     * set param tags according to the existing parameters
     *
     * if param tags with appropriate typeHint exist,
     * they should be preserved
     */
    public function updateParamTags(): void
    {
        $updatedParamTags = [];
        $existingParamTagValues = [];
        $paramTagsMissing = false;
        if ($this->isTaggedWith('param')) {
            $existingParamTagValues = $this->getTagValues('param');
            if (!is_array($existingParamTagValues)) {
                $existingParamTagValues = [$existingParamTagValues];
            }
        }
        if (count($existingParamTagValues) < count(array_keys($this->parameters))) {
            $paramTagsMissing = true;
        }
        $paramPosition = 0;
        foreach ($this->parameters as $position => $parameter) {
            $varType = $parameter->getTypeForParamTag();
            if (empty($varType)) {
                $varType = $parameter->getTypeHint();
            }
            if (empty($varType)) {
                $varType = $parameter->getVarType();
            }

            if (isset($existingParamTagValues[$paramPosition])
                && strpos($existingParamTagValues[$paramPosition], '$' . $parameter->getName()) !== false
            ) {
                // param tag for this parameter was found
                if (!empty($varType) && strpos($existingParamTagValues[$paramPosition], $varType) === false) {
                    $updatedParamTags[$position] = $varType . ' $' . $parameter->getName();
                } else {
                    $updatedParamTags[$position] = $existingParamTagValues[$paramPosition];
                }
            } elseif ($paramTagsMissing) {
                // we insert a param tag
                if (!empty($varType)) {
                    $varType .= ' ';
                }
                $updatedParamTags[$position] = $varType . '$' . $parameter->getName();
                // the existing param tags might fit to other params
                $paramPosition++;
            }
            $paramPosition++;
        }

        if (count($updatedParamTags) > 0) {
            $this->setTag('param', $updatedParamTags);
        }
    }

    /**
     * @param int $startLine
     */
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
