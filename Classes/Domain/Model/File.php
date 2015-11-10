<?php
namespace EBT\ExtensionBuilder\Domain\Model;

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

class File extends Container
{
    /**
     * @var string
     */
    protected $filePathAndName = '';
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\NamespaceObject[]
     */
    protected $namespaces = array();
    /**
     * @var array all statements
     */
    protected $stmts = array();
    /**
     * @var \PhpParser\Node\Stmt[]
     */
    protected $aliasDeclarations = array();
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\FunctionObject[]
     */
    protected $functions = array();
    /**
     * @var string
     */
    protected $comment = '';

    /**
     */
    public function __clone()
    {
        $clonedClasses = array();
        foreach ($this->classes as $class) {
            $clonedClasses = clone($class);
        }
        $this->classes = $clonedClasses;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $class
     */
    public function addClass(ClassObject\ClassObject $class)
    {
        $this->classes[] = $class;
    }

    /**
     * @param string $className
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject|null
     */
    public function getClassByName($className)
    {
        foreach ($this->getClasses() as $class) {
            if ($class->getName() == $className) {
                return $class;
            }
        }
        return null;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject[]
     */
    public function getClasses()
    {
        if (count($this->namespaces) > 0) {
            return reset($this->namespaces)->getClasses();
        } else {
            return $this->classes;
        }
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    public function getFirstClass()
    {
        if ($this->hasNamespaces()) {
            return reset($this->namespaces)->getFirstClass();
        }
        $classes = $this->getClasses();
        return reset($classes);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\NamespaceObject $namespace
     */
    public function addNamespace(NamespaceObject $namespace)
    {
        $this->namespaces[] = $namespace;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\NamespaceObject[]
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * get the first namespace of this file
     * (only for convenience, most files only use one namespace)
     * @return \EBT\ExtensionBuilder\Domain\Model\NamespaceObject
     */
    public function getNamespace()
    {
        return current($this->namespaces);
    }

    /**
     * @return bool
     */
    public function hasNamespaces()
    {
        return (count($this->namespaces) > 0);
    }

    /**
     * @param string $filePathAndName
     */
    public function setFilePathAndName($filePathAndName)
    {
        $this->filePathAndName = $filePathAndName;
    }

    /**
     * @param array $aliasDeclarations PhpParser\Node\Stmt
     */
    public function addAliasDeclarations($aliasDeclarations)
    {
        $this->aliasDeclarations = $aliasDeclarations;
    }

    /**
     * @return array PhpParser\Node\Stmt
     */
    public function getAliasDeclarations()
    {
        return $this->aliasDeclarations;
    }
}
