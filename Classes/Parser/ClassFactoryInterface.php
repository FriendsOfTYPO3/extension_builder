<?php
namespace EBT\ExtensionBuilder\Parser;
/*                                                                        *
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

/**
 * interface for class factory
 *
 */

interface ClassFactoryInterface {

	public function buildClassObject(\PhpParser\Node\Stmt\Class_ $node);

	public function buildClassMethodObject(\PhpParser\Node\Stmt\ClassMethod $node);

	public function buildPropertyObject(\PhpParser\Node\Stmt\Property $node);

	public function buildFunctionObject(\PhpParser\Node\Stmt\Function_ $node);

	public function buildNamespaceObject(\PhpParser\Node\Stmt\Namespace_ $node);
}
