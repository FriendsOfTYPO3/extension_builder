<?php
namespace EBT\ExtensionBuilder\Parser;
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
use \EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use \EBT\ExtensionBuilder\Domain\Model;

/**
 * factory for class objects and related objects (methods, properties etc)
 *
 * builds objects from PHP-Parser nodes
 *
 */

class ClassFactory  {

	/**
	 * @param \PHPParser_Node_Stmt_Class $classNode
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
	 */
	public function buildClassObject(\PHPParser_Node_Stmt_Class $classNode) {
		$classObject = new Model\ClassObject\ClassObject($classNode->name);
		foreach($classNode->implements as $interfaceNode) {
			$classObject->addInterfaceName($interfaceNode, FALSE);
		}
		$classObject->setModifiers($classNode->type);
		if (!is_null($classNode->extends)) {
			$classObject->setParentClassName(NodeConverter::getValueFromNode($classNode->extends));
		}
		$this->addCommentsFromAttributes($classObject, $classNode);
		return $classObject;
	}

	/**
	 * @param \PHPParser_Node_Stmt_ClassMethod $methodNode
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Method
	 */
	public function buildClassMethodObject (\PHPParser_Node_Stmt_ClassMethod $methodNode) {
		$methodObject = new Model\ClassObject\Method($methodNode->name);
		$methodObject->setModifiers($methodNode->type);
		$this->addCommentsFromAttributes($methodObject, $methodNode);
		$this->setFunctionProperties($methodNode, $methodObject);
		return $methodObject;
	}

	/**
	 * @param \PHPParser_Node_Stmt_Function $functionNode
	 * @return \EBT\ExtensionBuilder\Domain\Model\FunctionObject
	 */
	public function buildFunctionObject (\PHPParser_Node_Stmt_Function $functionNode) {
		$functionObject = new Model\FunctionObject($functionNode->name);
		$this->addCommentsFromAttributes($functionObject, $functionNode);
		$this->setFunctionProperties($functionNode, $functionObject);
		return $functionObject;
	}

	/**
	 * @param \PHPParser_Node_Stmt_Property $propertyNode
	 * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property
	 */
	public function buildPropertyObject(\PHPParser_Node_Stmt_Property $propertyNode) {
		$propertyName = '';
		$propertyDefault = NULL;

		foreach($propertyNode->props as $subNode) {
			if ($subNode instanceof \PHPParser_Node_Stmt_PropertyProperty) {
				$propertyName = $subNode->name;
				if ($subNode->default) {
					$propertyDefault = $subNode->default;
				}
			}
		}

		$propertyObject = new Model\ClassObject\Property($propertyName);
		$propertyObject->setModifiers($propertyNode->type);
		if (NULL !== $propertyDefault) {
			$propertyObject->setValue(NodeConverter::getValueFromNode($propertyDefault), FALSE, $propertyObject->isTaggedWith('var'));
			$propertyObject->setDefaultValueNode($propertyDefault);
		}
		$this->addCommentsFromAttributes($propertyObject, $propertyNode);
		return $propertyObject;
	}

	/**
	 * @param \PHPParser_Node_Stmt_Namespace $nameSpaceNode
	 * @return \EBT\ExtensionBuilder\Domain\Model\NamespaceObject
	 */
	public function buildNamespaceObject(\PHPParser_Node_Stmt_Namespace $nameSpaceNode) {
		$nameSpaceObject = new Model\NamespaceObject(NodeConverter::getValueFromNode($nameSpaceNode));
		$this->addCommentsFromAttributes($nameSpaceObject, $nameSpaceNode);
		return $nameSpaceObject;
	}

	/**
	 * @param \PHPParser_Node_Stmt $node
	 * @param \EBT\ExtensionBuilder\Domain\Model\FunctionObject $object
	 * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject
	 */
	protected function setFunctionProperties(\PHPParser_Node_Stmt $node, Model\FunctionObject $object) {
		if (property_exists($node,'type')) {
			$object->setModifiers($node->type);
		}
		$object->setBodyStmts($node->stmts);
		$position = 0;
		$object->setStartLine($node->getAttribute('startLine'));
		$object->setEndLine($node->getAttribute('endLine'));
		$getVarTypeFromParamTag = FALSE;
		$paramTags = array();
		if ($object->isTaggedWith('param') && is_array($object->getTagValues('param'))) {
			$paramTags = $object->getTagValues('param');
			if (count($paramTags) == count($node->params)) {
				$getVarTypeFromParamTag = TRUE;
			}
		}
		/** @var $param \PHPParser_NodeAbstract */
		foreach($node->params as $param) {
			$parameter = new Model\ClassObject\MethodParameter($param->name);
			$parameter->setPosition($position);
			$parameter->setStartLine($param->getAttribute('startLine'));
			$parameter->setEndLine($param->getAttribute('endLine'));
			if (!is_null($param->type)){
				$parameter->setTypeHint(NodeConverter::getValueFromNode($param->type));
				if (!$getVarTypeFromParamTag) {
					$parameter->setVarType(NodeConverter::getValueFromNode($param->type));
				}
			} elseif ($getVarTypeFromParamTag) {
				// if there is not type hint but a varType in the param tag,
				// we set the varType of the parameter
				$paramTag = explode(' ', $paramTags[$position]);
				if ($paramTag[0] !== '$' . $param->name) {
					$parameter->setVarType($paramTag[0]);
					$parameter->setTypeForParamTag($paramTag[0]);
				}
			}
			if (!is_null($param->default)) {
				$parameter->setDefaultValue($param->default);
			}
			$object->setParameter($parameter);
			$position++;
		}
		$object->updateParamTags();
		return $object;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\AbstractObject $object
	 * @param \PHPParser_Node_Stmt $node
	 */
	protected function addCommentsFromAttributes(Model\AbstractObject $object, \PHPParser_Node_Stmt $node) {
		$comments = $node->getAttribute('comments');
		if (is_array($comments)) {
			foreach ($comments as $comment) {
				if ($comment instanceof \PHPParser_Comment_Doc) {
					$object->setDocComment($comment->getReformattedText());
				} elseif ($comment instanceof \PHPParser_Comment) {
					$object->addComment($comment->getText());
				}
			}
		}
	}
}
