<?php
namespace EBT\ExtensionBuilder\Parser\Visitor;
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

use \PhpParser\Node;

class FormatVisitor extends \PhpParser\NodeVisitorAbstract {
	/**
	 * @var bool
	 */
	public static $first = TRUE;

    public function enterNode(\PhpParser\Node $node){
        if (self::$first && $node instanceof Node\Expr\FuncCall) {
            self::$first = FALSE;
            return new Node\Expr\Array_(array(
                new Node\Expr\ArrayItem(
                    self::parseArgs($node),
                    new Node\Scalar\String_($node->name)
                )
            ));
        }
        if (!self::$first && $node instanceof Node\Expr\FuncCall) {
            return new Node\Expr\ArrayItem(
                self::parseArgs($node),
                new Node\Scalar\String_($node->name)
            );
        }
    }

    public static function parseArgs(&$node){
        if (count($node->args) > 1){
            foreach($node->args as $k2=>&$arg){
                if ($arg->value instanceof Node\Expr\FuncCall){
                    $arg = new Node\Expr\Array_(array($arg));
                }
            }
        }
        return new Node\Expr\Array_($node->args);
    }
}
