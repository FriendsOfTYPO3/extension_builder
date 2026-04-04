<?php

declare(strict_types=1);

namespace VENDOR\Package\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Model
 */
class Model extends AbstractEntity
{
    protected string $property = '';

    protected ObjectStorage $children;

    public function __construct()
    {
        // Do not remove the next line: It would break the functionality
        $this->initializeObject();
    }

    /**
     * Initializes all ObjectStorage properties when model is reconstructed from DB (where __construct is not called)
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     */
    public function initializeObject(): void
    {
        $this->children ??= new ObjectStorage();
    }

    /**
     * Sets the property
     *
     * @param string $property
     */
    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    /**
     * Returns the property
     *
     * @return string
     */
    public function getProperty(): string
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
     */
    public function getChildren(): ObjectStorage
    {
        return $this->children;
    }

    /**
     * Sets the children
     */
    public function setChildren(ObjectStorage $children): void
    {
        $this->children = $children;
    }

    /**
     * Adds a Child
     *
     * @param \VENDOR\Package\Domain\Model\Child $child
     * @return void
     */
    public function addChild(\VENDOR\Package\Domain\Model\Child $child): void
    {
        $this->children->attach($child);
    }

    /**
     * Removes a Child
     *
     * @param \VENDOR\Package\Domain\Model\Child $child
     * @return void
     */
    public function removeChild(\VENDOR\Package\Domain\Model\Child $childToRemove): void
    {
        $this->children->detach($childToRemove);
    }

}
