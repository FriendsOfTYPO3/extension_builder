<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
/**
 * This file is part of the "Extension Builder Test Extension" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) ###YEAR### John Doe <mail@typo3.com>, TYPO3
 */
/**
 * An object with various date format properties
 */
class Child2 extends AbstractEntity
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
    protected $dateProperty1;

    /**
     * DateTime which is stored as Native DateTime
     *
     * @var \DateTime
     */
    protected $dateProperty2;

    /**
     * A date which is stored as Timestamp
     *
     * @var \DateTime
     */
    protected $dateProperty3;

    /**
     * DateTime stores as Timestamp
     *
     * @var \DateTime
     */
    protected $dateProperty4;

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
     * Returns the dateProperty1
     *
     * @return \DateTime
     */
    public function getDateProperty1()
    {
        return $this->dateProperty1;
    }

    /**
     * Sets the dateProperty1
     *
     * @return void
     */
    public function setDateProperty1(\DateTime $dateProperty1)
    {
        $this->dateProperty1 = $dateProperty1;
    }

    /**
     * Returns the dateProperty2
     *
     * @return \DateTime
     */
    public function getDateProperty2()
    {
        return $this->dateProperty2;
    }

    /**
     * Sets the dateProperty2
     *
     * @return void
     */
    public function setDateProperty2(\DateTime $dateProperty2)
    {
        $this->dateProperty2 = $dateProperty2;
    }

    /**
     * Returns the dateProperty3
     *
     * @return \DateTime
     */
    public function getDateProperty3()
    {
        return $this->dateProperty3;
    }

    /**
     * Sets the dateProperty3
     *
     * @return void
     */
    public function setDateProperty3(\DateTime $dateProperty3)
    {
        $this->dateProperty3 = $dateProperty3;
    }

    /**
     * Returns the dateProperty4
     *
     * @return \DateTime
     */
    public function getDateProperty4()
    {
        return $this->dateProperty4;
    }

    /**
     * Sets the dateProperty4
     *
     * @return void
     */
    public function setDateProperty4(\DateTime $dateProperty4)
    {
        $this->dateProperty4 = $dateProperty4;
    }
}
