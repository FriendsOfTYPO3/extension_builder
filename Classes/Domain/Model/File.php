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

namespace EBT\ExtensionBuilder\Domain\Model;

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;

class File extends Container
{
    protected string $filePathAndName = '';

    /**
     * @var NamespaceObject[]
     */
    protected array $namespaces = [];

    /**
     * @var array all statements
     */
    protected array $stmts = [];

    protected string $comment = '';

    public function __clone()
    {
        $clonedClasses = [];
        foreach ($this->classes as $class) {
            $clonedClasses = clone $class;
        }
        $this->classes = $clonedClasses;
    }

    public function getClassByName(string $className): ?ClassObject
    {
        foreach ($this->getClasses() as $class) {
            if ($class->getName() === $className) {
                return $class;
            }
        }
        return null;
    }

    /**
     * @return ClassObject[]
     */
    public function getClasses(): array
    {
        if (count($this->namespaces) > 0) {
            return reset($this->namespaces)->getClasses();
        }

        return $this->classes;
    }

    /**
     * @return ClassObject
     */
    public function getFirstClass()
    {
        if ($this->hasNamespaces()) {
            return reset($this->namespaces)->getFirstClass();
        }
        $classes = $this->getClasses();
        return reset($classes);
    }

    public function addNamespace(NamespaceObject $namespace): void
    {
        $this->namespaces[] = $namespace;
    }

    /**
     * @return NamespaceObject[]
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * get the first namespace of this file
     * (only for convenience, most files only use one namespace)
     * @return NamespaceObject|false
     */
    public function getNamespace()
    {
        return current($this->namespaces);
    }

    public function hasNamespaces(): bool
    {
        return count($this->namespaces) > 0;
    }

    public function setFilePathAndName(string $filePathAndName): void
    {
        $this->filePathAndName = $filePathAndName;
    }
}
