<?php
namespace EBT\ExtensionBuilder\Parser;

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

/**
 * interface for class factory
 *
 */
interface ClassFactoryInterface
{
    public function buildClassObject(\PhpParser\Node\Stmt\Class_ $node);

    public function buildClassMethodObject(\PhpParser\Node\Stmt\ClassMethod $node);

    public function buildPropertyObject(\PhpParser\Node\Stmt\Property $node);

    public function buildFunctionObject(\PhpParser\Node\Stmt\Function_ $node);

    public function buildNamespaceObject(\PhpParser\Node\Stmt\Namespace_ $node);
}
