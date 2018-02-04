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

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;

/**
 * interface for class factory
 *
 */
interface ClassFactoryInterface
{
    public function buildClassObject(Class_ $node);

    public function buildClassMethodObject(ClassMethod $node);

    public function buildPropertyObject(Property $node);

    public function buildFunctionObject(Function_ $node);

    public function buildNamespaceObject(Namespace_ $node);
}
