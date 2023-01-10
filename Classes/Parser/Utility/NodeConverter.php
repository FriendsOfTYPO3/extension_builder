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

namespace EBT\ExtensionBuilder\Parser\Utility;

use LogicException;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeAbstract;

class NodeConverter
{
    /**
     * @var int[]
     */
    public static array $accessorModifiers = [
        Class_::MODIFIER_PUBLIC,
        Class_::MODIFIER_PROTECTED,
        Class_::MODIFIER_PRIVATE
    ];

    public static function getTypeHintFromVarType($varType)
    {
        if (in_array(strtolower($varType), ['integer', 'int', 'double', 'float', 'boolean', 'bool', 'string'])) {
            return '';
        }

        if (preg_match_all('/^[^a-zA-Z]|[^\w_]/', $varType, $matches) === 0) {
            // has to be an allowed classname or 'array'
            return $varType;
        }
        return '';
    }

    /**
     * @static
     * @param int $modifiers
     * @return array with names as strings
     */
    public static function modifierToNames($modifiers): array
    {
        $modifierString = ($modifiers & Class_::MODIFIER_PUBLIC ? 'public ' : '') .
            ($modifiers & Class_::MODIFIER_PROTECTED ? 'protected ' : '') .
            ($modifiers & Class_::MODIFIER_PRIVATE ? 'private ' : '') .
            ($modifiers & Class_::MODIFIER_STATIC ? 'static ' : '') .
            ($modifiers & Class_::MODIFIER_ABSTRACT ? 'abstract ' : '') .
            ($modifiers & Class_::MODIFIER_FINAL ? 'final ' : '');
        return explode(' ', trim($modifierString));
    }

    /**
     * Convert various \PhpParser\Nodes to the value they represent
     * //TODO: support more node types?
     *
     * @static
     * @param NodeAbstract|string $node
     * @return array|string|null
     */
    public static function getValueFromNode($node)
    {
        if (is_string($node) || is_numeric($node)) {
            return $node;
        }

        if ($node instanceof Namespace_) {
            return implode('\\', $node->name->parts);
        }

        if ($node instanceof FullyQualified) {
            return '\\' . implode('\\', $node->parts);
        }

        if ($node instanceof Name) {
            return implode('\\', $node->parts);
        }

        if ($node instanceof ConstFetch) {
            return self::getValueFromNode($node->name);
        }

        if ($node instanceof UnaryMinus) {
            return -1 * self::getValueFromNode($node->expr);
        }

        if ($node instanceof Array_) {
            $value = [];
            $arrayItems = $node->items;
            foreach ($arrayItems as $arrayItemNode) {
                $itemKey = $arrayItemNode->key;
                $itemValue = $arrayItemNode->value;
                if (null === $itemKey) {
                    $value[] = self::normalizeValue($itemValue);
                } else {
                    $value[self::getValueFromNode($itemKey)] = self::normalizeValue($itemValue);
                }
            }
            return $value;
        }

        if ($node instanceof NullableType) {
            return '?' . self::getValueFromNode($node->type);
        }

        if ($node instanceof ClassConstFetch) {
            return $node->name;
        }
        
        if ($node instanceof Node\Identifier) {
            return $node->name;
        }

        if ($node instanceof Node) {
            return $node->value;
        }

        return null;
    }

    /**
     * Normalizes a value: Converts nulls, booleans, integers,
     * floats, strings and arrays into their respective nodes
     *
     * @param mixed $value The value to normalize
     *
     * @return Expr The normalized value
     */
    public static function normalizeValue($value): Node
    {
        if ($value instanceof Node) {
            return $value;
        }

        if (null === $value) {
            return new ConstFetch(new Name('null'));
        }

        if (is_bool($value)) {
            return new ConstFetch(new Name($value ? 'true' : 'false'));
        }

        if (is_int($value)) {
            return new LNumber($value);
        }

        if (is_float($value)) {
            return new DNumber($value);
        }

        if (is_string($value)) {
            return new String_($value);
        }

        if (is_array($value)) {
            $items = [];
            $lastKey = -1;
            foreach ($value as $itemKey => $itemValue) {
                // for consecutive, numeric keys don't generate keys
                if (null !== $lastKey && ++$lastKey === $itemKey) {
                    $items[] = new ArrayItem(
                        self::normalizeValue($itemValue)
                    );
                } else {
                    $lastKey = null;
                    $items[] = new ArrayItem(
                        self::normalizeValue($itemValue),
                        self::normalizeValue($itemKey)
                    );
                }
            }

            return new Array_($items);
        }

        throw new LogicException('Invalid value');
    }

    /**
     * Constants consist of a simple key => value array in the API
     * This methods converts  \PhpParser\Node\Stmt\Class_Const or
     * \PhpParser\Node\Stmt\Class_Const
     *
     * @static
     * @param Node $node
     * @return array
     */
    public static function convertClassConstantNodeToArray(Node $node): array
    {
        $constantsArray = [];
        $constants = $node->consts;
        foreach ($constants as $const) {
            $constantsArray[] = [
                'name' => $const->name,
                'value' => self::getValueFromNode($const->value)
            ];
        }
        return $constantsArray;
    }

    /**
     * helper function
     * This methods converts a USE statement node to an array
     * with keys name and alias
     *
     * @static
     * @param Use_ $node
     * @return array
     */
    public static function convertUseAliasStatementNodeToArray(Use_ $node): array
    {
        return [
            'name' => self::getValueFromNode($node->uses[0]->name),
            'alias' => self::getValueFromNode($node->uses[0]->alias)
        ];
    }

    public static function getVarTypeFromValue($value): string
    {
        if (null === $value) {
            return '';
        }

        if ($value == 'false' || $value == 'true') {
            return 'bool';
        }

        return gettype($value);
    }

    public static function getPropertyValueFromNode(Node $node, $property)
    {
        if (is_string($node->$property)) {
            return $node->$property;
        }
        if (is_object($node->$property) && property_exists($node->$property, $property)) {
            return $node->$property->$property;
        }
        return null;
    }

    public static function getNameFromNode(Node $node)
    {
        if (property_exists($node, 'name') && is_string($node->name)) {
            return $node->name;
        }
        if (property_exists($node, 'var') && property_exists($node->var, 'name')) {
            return $node->var->name;
        }
        return self::getValueFromNode($node);
    }
}
