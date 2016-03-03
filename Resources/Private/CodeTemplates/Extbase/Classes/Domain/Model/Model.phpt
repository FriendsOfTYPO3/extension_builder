<?php
namespace VENDOR\Package\Domain\Model;

/**
 * Class Model
 */
class Model extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * property
     *
     * @var string
     */
    protected $property;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Child> $children
     */
    protected $children = null;

    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->children = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Sets the property
     *
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * Returns the property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return bool
     */
    public function isProperty()
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
     * @return void
     */
    public function addChild(\VENDOR\Package\Domain\Model\Child $child)
    {
        $this->children->attach($child);
    }

    /**
     * Removes a Child
     *
     * @param \VENDOR\Package\Domain\Model\Child $child
     * @return void
     */
    public function removeChild(\VENDOR\Package\Domain\Model\Child $childToRemove)
    {
        $this->children->detach($childToRemove);
    }

}
