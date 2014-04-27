<?php
namespace EBT\ExtensionBuilder\Domain\Model;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;

/**
 * Class FunctionObject
 */
class FunctionObject extends AbstractObject {
	/**
	 * stmts of this methods body
	 *
	 * @var array
	 */
	protected $bodyStmts= array();

	/**
	 * parameters
	 *
	 * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter[]
	 */
	protected $parameters = array();

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
	 * @return
	 */
	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * used when cloning methods from a template class
	 */
	public function __clone() {
		$clonedParameters = array();
		foreach($this->parameters as $parameter) {
			$clonedParameters[] = clone($parameter);
		}
		$this->parameters = $clonedParameters;
	}

		/**
	 * Setter for body statements
	 *
	 * @param array $stmts
	 * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject
	 */
	public function setBodyStmts($stmts) {
		if (!is_array($stmts)) {
			$stmts = array();
		}
		$this->bodyStmts = $stmts;
		return $this;
	}

	/**
	 * Getter for body statements
	 *
	 * @return array body
	 */
	public function getBodyStmts() {
		return $this->bodyStmts;
	}

	/**
	 * getter for parameters
	 *
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter[]
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * getter for parameter names
	 *
	 * @return array parameter names
	 */
	public function getParameterNames() {
		$parameterNames = array();
		if (is_array($this->parameters)) {
			/** @var $parameter \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter */
			foreach ($this->parameters as $parameter) {
				$parameterNames[] = $parameter->getName();
			}
		}
		return $parameterNames;
	}

	/**
	 * @param int $position
	 */
	public function getParameterByPosition($position) {
		if (isset($this->parameters[$position])) {
			return $this->parameters[$position];
		} else {
			return NULL;
		}

	}

	/**
	 * adder for parameters
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter[] $parameters
	 * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject
	 */
	public function setParameters($parameters) {
		$this->parameters = $parameters;
		return $this;
	}

	/**
	 * setter for a single parameter
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter $parameter
	 * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject
	 */
	public function setParameter(MethodParameter $parameter) {
		$this->parameters[$parameter->getPosition()] = $parameter;
		return $this;
	}

	/**
	 * replace a single parameter, depending on position
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter $parameter
	 * @return void
	 */
	public function replaceParameter(MethodParameter $parameter) {
		$this->parameters[$parameter->getPosition()] = $parameter;
	}

	/**
	 * removes a parameter
	 *
	 * @param $parameterName
	 * @param $parameterPosition
	 * @return boolean TRUE (if successfull removed)
	 */
	public function removeParameter($parameterName, $parameterPosition) {
		if (isset($this->parameters[$parameterPosition]) && $this->parameters[$parameterPosition]->getName() == $parameterName) {
			unset($this->parameters[$parameterPosition]);
			$this->updateParamTags();
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 * renameParameter
	 *
	 * @param $oldName
	 * @param $newName
	 * @param $parameterPosition
	 * @return boolean TRUE (if successfull removed)
	 */
	public function renameParameter($oldName, $newName, $parameterPosition) {
		if (isset($this->parameters[$parameterPosition])) {
			$parameter = $this->parameters[$parameterPosition];
			if ($parameter->getName() == $oldName) {
				$parameter->setName($newName);
				$this->parameters[$parameterPosition] = $parameter;
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * TODO: THe sorting of tags/annotations should be controlled
	 *
	 * @return
	 */
	public function getAnnotations() {
		$annotations = parent::getAnnotations();
		if (is_array($this->parameters) && count($this->parameters) > 0 && !$this->isTaggedWith('param')) {
			$paramTags = array();
			/** @var $parameter \EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter */
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
	public function updateParamTags() {
		$updatedParamTags = array();
		$existingParamTagValues = array();
		$paramTagsMissing = FALSE;
		if ($this->isTaggedWith('param')) {
			$existingParamTagValues = $this->getTagValues('param');
			if (!is_array($existingParamTagValues)) {
				$existingParamTagValues = array($existingParamTagValues);
			}
		}
		if (count($existingParamTagValues) < count(array_keys($this->parameters))) {
			$paramTagsMissing = TRUE;
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

			if (isset($existingParamTagValues[$paramPosition]) && strpos($existingParamTagValues[$paramPosition], '$' . $parameter->getName()) !== FALSE) {
				// param tag for this parameter was found
				if (!empty($varType) && strpos($existingParamTagValues[$paramPosition], $varType) === FALSE) {
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
	public function setStartLine($startLine) {
		$this->startLine = $startLine;
	}

	/**
	 * @return int
	 */
	public function getStartLine() {
		return $this->startLine;
	}

	/**
	 * @param int $endLine
	 */
	public function setEndLine($endLine) {
		$this->endLine = $endLine;
	}

	/**
	 * @return int
	 */
	public function getEndLine() {
		return $this->endLine;
	}

}
