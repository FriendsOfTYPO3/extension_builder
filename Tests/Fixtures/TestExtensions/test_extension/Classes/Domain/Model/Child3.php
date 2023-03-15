<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
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
 * Child3
 */
class Child3 extends AbstractEntity
{

    /**
     * name
     *
     * @var string
     * @Validate("NotEmpty")
     */
    protected $name = '';

    /**
     * password
     *
     * @var string
     */
    protected $password = '';

    /**
     * imageProperty
     *
     * @var FileReference
     * @Validate("NotEmpty")
     * @Cascade("remove")
     */
    protected $imageProperty;

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
     * Returns the password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password
     *
     * @return void
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * Returns the imageProperty
     *
     * @return FileReference
     */
    public function getImageProperty()
    {
        return $this->imageProperty;
    }

    /**
     * Sets the imageProperty
     *
     * @return void
     */
    public function setImageProperty(FileReference $imageProperty)
    {
        $this->imageProperty = $imageProperty;
    }
}
