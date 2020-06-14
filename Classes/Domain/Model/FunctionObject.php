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

use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;

/**
 * Class FunctionObject
 */
class FunctionObject extends AbstractObject
{
    /**
     * stmts of this methods body
     *
     * @var array
     */
    protected $bodyStmts = [];
    /**
     * parameters
     *
     * @var MethodParameter[]
     */
    protected $parameters = [];
    /**
     * @var int
     */
    protected $startLine = -1;
    /**
     * @var int
     */
    protected $endLine = -1;

    /**
     * __construct
     *
     * @param string $name
     */
    public function __construct($name)
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
            $clonedParameters[] = clone($parameter);
        }
        $this->parameters = $clonedParameters;
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

    /**
     * Getter for body statements
     *
     * @return array body
     */
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

    /**
     * getter for parameter names
     *
     * @return array parameter names
     */
    public function getParameterNames(): array
    {
        $parameterNames = [];
        if (is_array($this->parameters)) {
            /** @var $parameter MethodParameter */
            foreach ($this->parameters as $parameter) {
                $parameterNames[] = $parameter->getName();
            }
        }
        return $parameterNames;
    }

    /**
     * @param int $position
     *
     * @return MethodParameter|null
     */
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

    /**
     * setter for a single parameter
     *
     * @param MethodParameter $parameter
     * @return $this
     */
    public function setParameter(MethodParameter $parameter): self
    {
        $this->parameters[$parameter->getPosition()] = $parameter;
        return $this;
    }

    /**
     * replace a single parameter, depending on position
     *
     * @param MethodParameter $parameter
     * @return void
     */
    public function replaceParameter(MethodParameter $parameter): void
    {
        $this->parameters[$parameter->getPosition()] = $parameter;
    }

    /**
     * removes a parameter
     *
     * @param string $parameterName
     * @param int $parameterPosition
     * @return bool true (if successfully removed)
     */
    public function removeParameter(string $parameterName, int $parameterPosition): bool
    {
        if (isset($this->parameters[$parameterPosition]) && $this->parameters[$parameterPosition]->getName() == $parameterName) {
            unset($this->parameters[$parameterPosition]);
            $this->updateParamTags();
            return true;
        }

        return false;
    }

    /**
     * renameParameter
     *
     * @param string $oldName
     * @param string $newName
     * @param int $parameterPosition
     * @return bool true (if successfully removed)
     */
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
     * TODO: THe sorting of tags/annotations should be controlled
     *
     * @return []
     */
    public function getAnnotations()
    {
        $annotations = parent::getAnnotations();
        if (is_array($this->parameters) && count($this->parameters) > 0 && !$this->isTaggedWith('param')) {
            $paramTags = [];
            /** @var $parameter MethodParameter */
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

    /**
     * @return int
     */
    public function getStartLine(): int
    {
        return $this->startLine;
    }

    /**
     * @param int $endLine
     */
    public function setEndLine(int $endLine): void
    {
        $this->endLine = $endLine;
    }

    /**
     * @return int
     */
    public function getEndLine(): int
    {
        return $this->endLine;
    }
}
