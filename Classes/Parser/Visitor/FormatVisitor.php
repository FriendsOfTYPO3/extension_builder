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

namespace EBT\ExtensionBuilder\Parser\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;

class FormatVisitor extends NodeVisitorAbstract
{
    public static bool $first = true;

    public function enterNode(Node $node)
    {
        if (self::$first && $node instanceof FuncCall) {
            self::$first = false;
            return new Array_([
                new ArrayItem(
                    self::parseArgs($node),
                    new String_($node->name)
                )
            ]);
        }
        if (!self::$first && $node instanceof FuncCall) {
            return new ArrayItem(
                self::parseArgs($node),
                new String_($node->name)
            );
        }
        return $node;
    }

    public static function parseArgs(&$node): Array_
    {
        if (count($node->args) > 1) {
            foreach ($node->args as $k2 => &$arg) {
                if ($arg->value instanceof FuncCall) {
                    $arg = new Array_([$arg]);
                }
            }
        }
        return new Array_($node->args);
    }
}
