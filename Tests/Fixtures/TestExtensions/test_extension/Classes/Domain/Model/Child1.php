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
 * Child1
 */
class Child1 extends AbstractEntity
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
     * Returns the flag
     *
     * @return bool
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Sets the flag
     *
     * @return void
     */
    public function setFlag(bool $flag)
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
