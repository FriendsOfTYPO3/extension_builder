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

class NamespaceObject extends Container
{
    /**
     * array with alias declarations
     *
     * Each declaration is an array of the following type:
     * array(name => alias)
     *
     * @var string[]
     */
    protected $aliasDeclarations = array();

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    public function getFirstClass()
    {
        $classes = $this->getClasses();
        return reset($classes);
    }

    /**
     * @param string $aliasDeclaration
     * @return void
     */
    public function addAliasDeclaration($aliasDeclaration)
    {
        $this->aliasDeclarations[] = $aliasDeclaration;
    }

    /**
     * @return string[]
     */
    public function getAliasDeclarations()
    {
        return $this->aliasDeclarations;
    }

    /**
     * @param array $preIncludes
     * @return void
     */
    public function setPreIncludes($preIncludes)
    {
        $this->preIncludes = $preIncludes;
    }

    /**
     * @return array
     */
    public function getPreIncludes()
    {
        return $this->preIncludes;
    }

    /**
     * @param array $preInclude
     * @return void
     */
    public function addPreInclude($preInclude)
    {
        $this->preIncludes[] = $preInclude;
    }

    /**
     * @param array $postIncludes
     * @return void
     */
    public function setPostIncludes($postIncludes)
    {
        $this->postIncludes = $postIncludes;
    }

    /**
     * @return array
     */
    public function getPostIncludes()
    {
        return $this->postIncludes;
    }

    /**
     * @param array $postInclude
     * @return void
     */
    public function addPostInclude($postInclude)
    {
        $this->postIncludes[] = $postInclude;
    }
}
