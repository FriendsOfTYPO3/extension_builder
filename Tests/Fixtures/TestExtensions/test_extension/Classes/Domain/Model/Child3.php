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
 * Child3
 */
class Child3 extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     * @validate NotEmpty
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
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @validate NotEmpty
     * @cascade remove
     */
    protected $imageProperty = null;

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
     * Returns the password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the imageProperty
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $imageProperty
     */
    public function getImageProperty()
    {
        return $this->imageProperty;
    }

    /**
     * Sets the imageProperty
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $imageProperty
     * @return void
     */
    public function setImageProperty(\TYPO3\CMS\Extbase\Domain\Model\FileReference $imageProperty)
    {
        $this->imageProperty = $imageProperty;
    }
}