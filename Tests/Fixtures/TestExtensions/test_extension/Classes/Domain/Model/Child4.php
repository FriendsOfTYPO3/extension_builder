<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Domain\Model;


/**
 * This file is part of the "Extension Builder Test Extension" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) ###YEAR### John Doe <mail@typo3.com>, TYPO3
 */

/**
 * Child4
 */
class Child4 extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * fileProperty
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $fileProperty = null;

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
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the fileProperty
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getFileProperty()
    {
        return $this->fileProperty;
    }

    /**
     * Sets the fileProperty
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileProperty
     * @return void
     */
    public function setFileProperty(\TYPO3\CMS\Extbase\Domain\Model\FileReference $fileProperty)
    {
        $this->fileProperty = $fileProperty;
    }
}
