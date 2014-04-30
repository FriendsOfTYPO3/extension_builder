<?php
namespace EBT\ExtensionBuilder\Parser\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
 *  All rights reserved
 *
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

/**
 * @author Nico de Haen
 */

class NodeConverter {
	/**
	 * @var int[]
	 */
	public static $accessorModifiers = array(
		\PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC,
		\PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED,
		\PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE

	);

	public static function getTypeHintFromVarType ($varType) {
		if (in_array(strtolower($varType), array('integer', 'int', 'double', 'float', 'boolean', 'bool', 'string'))) {
			return '';
		} else {
			if (preg_match_all('/^[^a-zA-Z]|[^\w_]/', $varType, $matches) === 0) {
			// has to be an allowed classname or 'array'
				return $varType;
			}
			return '';
		}
	}


	/**
	 * @static
	 * @param int $modifiers
	 * @return array with names as strings
	 */
	public static function modifierToNames($modifiers) {
		$modifierString = ($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC    ? 'public '    : '') .
			($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED ? 'protected ' : '') .
			($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE   ? 'private '   : '') .
			($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_STATIC    ? 'static '    : '') .
			($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT  ? 'abstract '  : '') .
			($modifiers & \PHPParser_Node_Stmt_Class::MODIFIER_FINAL     ? 'final '     : '');
		return explode(' ',trim($modifierString));
	}


	/**
	 * Convert various \PHPParser_Nodes to the value they represent
	 * //TODO: support more node types?
	 *
	 * @static
	 * @param $node
	 * @return array|null|string
	 */
	public static function getValueFromNode($node) {
		if (\is_string($node) || \is_numeric($node)) {
			return $node;
		}
		if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
			return implode('\\', $node->name->parts);
		} elseif ($node instanceof \PHPParser_Node_Name_FullyQualified) {
			return '\\' . implode('\\', $node->parts);
		} elseif ($node instanceof \PHPParser_Node_Name) {
			return implode('\\', $node->parts);
		} elseif ($node instanceof \PHPParser_Node_Expr_ConstFetch) {
			return self::getValueFromNode($node->name);
		} elseif ($node instanceof \PHPParser_Node_Expr_UnaryMinus) {
			return -1 * self::getValueFromNode($node->expr);
		} elseif ($node instanceof \PHPParser_Node_Expr_Array) {
			$value = array();
			$arrayItems = $node->items;
			foreach ($arrayItems as $arrayItemNode) {
				$itemKey = $arrayItemNode->key;
				$itemValue = $arrayItemNode->value;
				if (is_null($itemKey)) {
					$value[] = self::normalizeValue($itemValue);
				} else {
					$value[self::getValueFromNode($itemKey)] = self::normalizeValue($itemValue);
				}
			}
			return $value;
		} elseif ($node instanceof \PHPParser_Node) {
			return $node->value;
		} else {
			return NULL;
		}
	}

	/**
    * Normalizes a value: Converts nulls, booleans, integers,
    * floats, strings and arrays into their respective nodes
    *
    * @param mixed $value The value to normalize
    *
    * @return \PHPParser_Node_Expr The normalized value
    */
   public static function normalizeValue($value) {
       if ($value instanceof \PHPParser_Node) {
           return $value;
       } elseif (is_null($value)) {
           return new \PHPParser_Node_Expr_ConstFetch(
               new \PHPParser_Node_Name('NULL')
           );
       } elseif (is_bool($value)) {
           return new \PHPParser_Node_Expr_ConstFetch(
               new \PHPParser_Node_Name($value ? 'TRUE' : 'FALSE')
           );
       } elseif (is_int($value)) {
           return new \PHPParser_Node_Scalar_LNumber($value);
       } elseif (is_float($value)) {
           return new \PHPParser_Node_Scalar_DNumber($value);
       } elseif (is_string($value)) {
           return new \PHPParser_Node_Scalar_String($value);
       } elseif (is_array($value)) {
           $items = array();
           $lastKey = -1;
           foreach ($value as $itemKey => $itemValue) {
               // for consecutive, numeric keys don't generate keys
               if (NULL !== $lastKey && ++$lastKey === $itemKey) {
                   $items[] = new \PHPParser_Node_Expr_ArrayItem(
                       self::normalizeValue($itemValue)
                   );
               } else {
                   $lastKey = NULL;
                   $items[] = new \PHPParser_Node_Expr_ArrayItem(
                       self::normalizeValue($itemValue),
                       self::normalizeValue($itemKey)
                   );
               }
           }

           return new \PHPParser_Node_Expr_Array($items);
       } else {
           throw new \LogicException('Invalid value');
       }
   }

	/**
	 * Constants consist of a simple key => value array in the API
	 * This methods converts  \PHPParser_Node_Stmt_ClassConst or
	 * \PHPParser_Node_Stmt_Const
	 *
	 * @static
	 * @param \PHPParser_Node
	 * @return array
	 */
	public static function convertClassConstantNodeToArray(\PHPParser_Node $node) {
		$constantsArray = array();
		$consts = $node->consts;
		foreach ($consts as $const) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('$const1: ', 'extension_builder', 1, (array)$const);
			$constantsArray[] = array('name' => $const->name,'value' => self::getValueFromNode($const->value));
		}
		\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('$const2: ', 'extension_builder', 1, (array)$constantsArray);
		return $constantsArray;
	}

	/**
	 * Constants consist of a simple key => value array in the API
	 * This methods converts  \PHPParser_Node_Stmt_ClassConst or
	 * \PHPParser_Node_Stmt_Const
	 *
	 * @static
	 * @param \PHPParser_Node
	 * @return array
	 */
	public static function convertUseAliasStatementNodeToArray(\PHPParser_Node_Stmt_Use $node) {
		$subNodes = $node->__get('uses');
		return array('name' => self::getValueFromNode($subNodes[0]->__get('name')), 'alias' => $subNodes[0]->__get('alias'));
	}

	public static function getVarTypeFromValue($value) {
		if (is_null($value)) {
			return '';
		} elseif ($value == 'FALSE' || $value == 'TRUE') {
			return 'boolean';
		} else {
			return gettype($value);
		}
	}



}
