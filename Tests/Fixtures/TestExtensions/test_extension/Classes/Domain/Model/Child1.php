<?php
namespace FIXTURE\TestExtension\Domain\Model;

    /***************************************************************
     *
     *  Copyright notice
     *
     *  (c) 2015 John Doe <mail@typo3.com>, TYPO3
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