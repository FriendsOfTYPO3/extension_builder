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
 * An object with various date format properties
 */
class Child2 extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';
    /**
     * A date which is stored as Native Date
     *
     * @var \DateTime
     */
    protected $dateProperty1 = null;
    /**
     * DateTime which is stored as Native DateTime
     *
     * @var \DateTime
     */
    protected $dateProperty2 = null;
    /**
     * A date which is stored as Timestamp
     *
     * @var \DateTime
     */
    protected $dateProperty3 = null;
    /**
     * DateTime stores as Timestamp
     *
     * @var \DateTime
     */
    protected $dateProperty4 = null;

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
     * Returns the dateProperty1
     *
     * @return \DateTime $dateProperty1
     */
    public function getDateProperty1()
    {
        return $this->dateProperty1;
    }

    /**
     * Sets the dateProperty1
     *
     * @param \DateTime $dateProperty1
     * @return void
     */
    public function setDateProperty1(\DateTime $dateProperty1)
    {
        $this->dateProperty1 = $dateProperty1;
    }

    /**
     * Returns the dateProperty2
     *
     * @return \DateTime $dateProperty2
     */
    public function getDateProperty2()
    {
        return $this->dateProperty2;
    }

    /**
     * Sets the dateProperty2
     *
     * @param \DateTime $dateProperty2
     * @return void
     */
    public function setDateProperty2(\DateTime $dateProperty2)
    {
        $this->dateProperty2 = $dateProperty2;
    }

    /**
     * Returns the dateProperty3
     *
     * @return \DateTime $dateProperty3
     */
    public function getDateProperty3()
    {
        return $this->dateProperty3;
    }

    /**
     * Sets the dateProperty3
     *
     * @param \DateTime $dateProperty3
     * @return void
     */
    public function setDateProperty3(\DateTime $dateProperty3)
    {
        $this->dateProperty3 = $dateProperty3;
    }

    /**
     * Returns the dateProperty4
     *
     * @return \DateTime $dateProperty4
     */
    public function getDateProperty4()
    {
        return $this->dateProperty4;
    }

    /**
     * Sets the dateProperty4
     *
     * @param \DateTime $dateProperty4
     * @return void
     */
    public function setDateProperty4(\DateTime $dateProperty4)
    {
        $this->dateProperty4 = $dateProperty4;
    }
}