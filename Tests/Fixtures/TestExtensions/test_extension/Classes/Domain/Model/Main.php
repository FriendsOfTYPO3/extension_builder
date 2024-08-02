<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
/**
 * This file is part of the "Extension Builder Test Extension" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) ###YEAR### John Doe <mail@typo3.com>, TYPO3
 */
/**
 * Main
 */
class Main extends AbstractEntity
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
     * @Validate("NotEmpty")
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
    protected $myDate;

    /**
     * mail
     *
     * @var string
     */
    protected $mail = '';

    /**
     * This is a 1:1 relation
     *
     * @var Child1
     */
    protected $child1;

    /**
     * This is a 1:n relation
     *
     * @var ObjectStorage<Child2>
     * @Cascade("remove")
     */
    protected $children2;

    /**
     * This is a n:1 relation
     *
     * @var Child3
     */
    protected $child3;

    /**
     * This is a m:n relation
     *
     * @var ObjectStorage<Child4>
     */
    protected $children4;

    /**
     * __construct
     */
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
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->children2 = $this->children2 ?: new ObjectStorage();
        $this->children4 = $this->children4 ?: new ObjectStorage();
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the identifier
     *
     * @return void
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns the myDate
     *
     * @return \DateTime
     */
    public function getMyDate()
    {
        return $this->myDate;
    }

    /**
     * Sets the myDate
     *
     * @return void
     */
    public function setMyDate(\DateTime $myDate)
    {
        $this->myDate = $myDate;
    }

    /**
     * Returns the mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Sets the mail
     *
     * @return void
     */
    public function setMail(string $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Returns the child1
     *
     * @return Child1
     */
    public function getChild1()
    {
        return $this->child1;
    }

    /**
     * Sets the child1
     *
     * @return void
     */
    public function setChild1(Child1 $child1)
    {
        $this->child1 = $child1;
    }

    /**
     * Adds a Child2
     *
     * @return void
     */
    public function addChildren2(Child2 $children2)
    {
        $this->children2->attach($children2);
    }

    /**
     * Removes a Child2
     *
     * @param Child2 $children2ToRemove The Child2 to be removed
     * @return void
     */
    public function removeChildren2(Child2 $children2ToRemove)
    {
        $this->children2->detach($children2ToRemove);
    }

    /**
     * Returns the children2
     *
     * @return ObjectStorage<Child2>
     */
    public function getChildren2()
    {
        return $this->children2;
    }

    /**
     * Sets the children2
     *
     * @param ObjectStorage<Child2> $children2
     * @return void
     */
    public function setChildren2(ObjectStorage $children2)
    {
        $this->children2 = $children2;
    }

    /**
     * Returns the child3
     *
     * @return Child3
     */
    public function getChild3()
    {
        return $this->child3;
    }

    /**
     * Sets the child3
     *
     * @return void
     */
    public function setChild3(Child3 $child3)
    {
        $this->child3 = $child3;
    }

    /**
     * Adds a Child4
     *
     * @return void
     */
    public function addChildren4(Child4 $children4)
    {
        $this->children4->attach($children4);
    }

    /**
     * Removes a Child4
     *
     * @param Child4 $children4ToRemove The Child4 to be removed
     * @return void
     */
    public function removeChildren4(Child4 $children4ToRemove)
    {
        $this->children4->detach($children4ToRemove);
    }

    /**
     * Returns the children4
     *
     * @return ObjectStorage<Child4>
     */
    public function getChildren4()
    {
        return $this->children4;
    }

    /**
     * Sets the children4
     *
     * @param ObjectStorage<Child4> $children4
     * @return void
     */
    public function setChildren4(ObjectStorage $children4)
    {
        $this->children4 = $children4;
    }
}
