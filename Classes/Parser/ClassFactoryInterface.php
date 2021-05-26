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

namespace EBT\ExtensionBuilder\Parser;

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\Method;
use EBT\ExtensionBuilder\Domain\Model\FunctionObject;
use EBT\ExtensionBuilder\Domain\Model\NamespaceObject;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;

interface ClassFactoryInterface
{
    public function buildClassObject(Class_ $node): ClassObject;

    public function buildClassMethodObject(ClassMethod $node): Method;

    public function buildPropertyObject(Property $node): \EBT\ExtensionBuilder\Domain\Model\ClassObject\Property;

    public function buildFunctionObject(Function_ $node): FunctionObject;

    public function buildNamespaceObject(Namespace_ $node): NamespaceObject;
}
