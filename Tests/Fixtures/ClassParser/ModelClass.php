<?php
namespace DUMMY\Dummy\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Model extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * This is the property
     *
     * @var string
     * @validate NotEmpty
     */
    protected $property;

    /**
     * children
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Child>
     */
    protected $children;

    /**
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Returns the children
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Child> $children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets the children
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Child> $children
     * @return void
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Adds a Child
     *
     * @param \VENDOR\Package\Domain\Model\Child $child
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\VENDOR\Package\Domain\Model\Child> children
     */
    public function addChild(\VENDOR\Package\Domain\Model\Child $child)
    {
        $this->children->attach($child);
    }

    /**
     * Removes a Child
     *
     * @param \VENDOR\Package\Domain\Model\Child $child
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\VENDOR\Package\Domain\Model\Child> children
     */
    public function removeChild(\VENDOR\Package\Domain\Model\Child $child)
    {
        $this->children->detach($child);
    }
}
