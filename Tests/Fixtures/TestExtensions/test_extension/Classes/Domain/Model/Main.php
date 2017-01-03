<?php
namespace FIXTURE\TestExtension\Domain\Model;

/***
 *
 * This file is part of the "ExtensionBuilder Test Extension" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017 John Doe <mail@typo3.com>, TYPO3
 *
 ***/

/**
 * Main
 */
class Main extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * This is not required
     *
     * @var string
     */
    protected $name = '';

    /**
     * This is required
     *
     * @var string
     * @validate NotEmpty
     */
    protected $identifier = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Just a date
     *
     * @var \DateTime
     */
    protected $myDate = null;

    /**
     * This is a 1:1 relation
     *
     * @var \FIXTURE\TestExtension\Domain\Model\Child1
     */
    protected $child1 = null;

    /**
     * This is a 1:n relation
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FIXTURE\TestExtension\Domain\Model\Child2>
     * @cascade remove
     */
    protected $children2 = null;

    /**
     * This is a n:1 relation
     *
     * @var \FIXTURE\TestExtension\Domain\Model\Child3
     */
    protected $child3 = null;

    /**
     * This is a m:n relation
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FIXTURE\TestExtension\Domain\Model\Child4>
     */
    protected $children4 = null;

    /**
     * __construct
     */
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
        $this->children2 = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->children4 = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the identifier
     *
     * @return string $identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the identifier
     *
     * @param string $identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the myDate
     *
     * @return \DateTime $myDate
     */
    public function getMyDate()
    {
        return $this->myDate;
    }

    /**
     * Sets the myDate
     *
     * @param \DateTime $myDate
     * @return void
     */
    public function setMyDate(\DateTime $myDate)
    {
        $this->myDate = $myDate;
    }

    /**
     * Returns the child1
     *
     * @return \FIXTURE\TestExtension\Domain\Model\Child1 $child1
     */
    public function getChild1()
    {
        return $this->child1;
    }

    /**
     * Sets the child1
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Child1 $child1
     * @return void
     */
    public function setChild1(\FIXTURE\TestExtension\Domain\Model\Child1 $child1)
    {
        $this->child1 = $child1;
    }

    /**
     * Adds a Child2
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Child2 $children2
     * @return void
     */
    public function addChildren2(\FIXTURE\TestExtension\Domain\Model\Child2 $children2)
    {
        $this->children2->attach($children2);
    }

    /**
     * Removes a Child2
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Child2 $children2ToRemove The Child2 to be removed
     * @return void
     */
    public function removeChildren2(\FIXTURE\TestExtension\Domain\Model\Child2 $children2ToRemove)
    {
        $this->children2->detach($children2ToRemove);
    }

    /**
     * Returns the children2
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FIXTURE\TestExtension\Domain\Model\Child2> $children2
     */
    public function getChildren2()
    {
        return $this->children2;
    }

    /**
     * Sets the children2
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FIXTURE\TestExtension\Domain\Model\Child2> $children2
     * @return void
     */
    public function setChildren2(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $children2)
    {
        $this->children2 = $children2;
    }

    /**
     * Returns the child3
     *
     * @return \FIXTURE\TestExtension\Domain\Model\Child3 $child3
     */
    public function getChild3()
    {
        return $this->child3;
    }

    /**
     * Sets the child3
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Child3 $child3
     * @return void
     */
    public function setChild3(\FIXTURE\TestExtension\Domain\Model\Child3 $child3)
    {
        $this->child3 = $child3;
    }

    /**
     * Adds a Child4
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Child4 $children4
     * @return void
     */
    public function addChildren4(\FIXTURE\TestExtension\Domain\Model\Child4 $children4)
    {
        $this->children4->attach($children4);
    }

    /**
     * Removes a Child4
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Child4 $children4ToRemove The Child4 to be removed
     * @return void
     */
    public function removeChildren4(\FIXTURE\TestExtension\Domain\Model\Child4 $children4ToRemove)
    {
        $this->children4->detach($children4ToRemove);
    }

    /**
     * Returns the children4
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FIXTURE\TestExtension\Domain\Model\Child4> $children4
     */
    public function getChildren4()
    {
        return $this->children4;
    }

    /**
     * Sets the children4
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FIXTURE\TestExtension\Domain\Model\Child4> $children4
     * @return void
     */
    public function setChildren4(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $children4)
    {
        $this->children4 = $children4;
    }

}