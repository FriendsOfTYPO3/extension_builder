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
 * Child1
 */
class Child1 extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * flag
     *
     * @var bool
     */
    protected $flag = false;

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
     * Returns the flag
     *
     * @return bool $flag
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Sets the flag
     *
     * @param bool $flag
     * @return void
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * Returns the boolean state of flag
     *
     * @return bool
     */
    public function isFlag()
    {
        return $this->flag;
    }
}