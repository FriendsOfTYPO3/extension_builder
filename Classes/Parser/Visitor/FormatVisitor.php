<?php
namespace EBT\ExtensionBuilder\Parser\Visitor;

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

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class FormatVisitor extends NodeVisitorAbstract
{
    /**
     * @var bool
     */
    public static $first = true;

    public function enterNode(Node $node)
    {
        if (self::$first && $node instanceof Node\Expr\FuncCall) {
            self::$first = false;
            return new Node\Expr\Array_([
                new Node\Expr\ArrayItem(
                    self::parseArgs($node),
                    new Node\Scalar\String_($node->name)
                )
            ]);
        }
        if (!self::$first && $node instanceof Node\Expr\FuncCall) {
            return new Node\Expr\ArrayItem(
                self::parseArgs($node),
                new Node\Scalar\String_($node->name)
            );
        }
        return $node;
    }

    public static function parseArgs(&$node)
    {
        if (count($node->args) > 1) {
            foreach ($node->args as $k2 => &$arg) {
                if ($arg->value instanceof Node\Expr\FuncCall) {
                    $arg = new Node\Expr\Array_([$arg]);
                }
            }
        }
        return new Node\Expr\Array_($node->args);
    }
}
