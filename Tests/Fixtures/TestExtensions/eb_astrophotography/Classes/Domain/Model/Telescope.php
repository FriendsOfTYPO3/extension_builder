<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * This file is part of the "EB Astrophotography" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2026 
 */

/**
 * Telescope
 */
class Telescope extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * brand
     *
     * @var string
     */
    protected $brand = '';

    /**
     * telescopeType
     *
     * @var string
     */
    protected $telescopeType = '';

    /**
     * focalLength
     *
     * @var int
     */
    protected $focalLength = 0;

    /**
     * aperture
     *
     * @var int
     */
    protected $aperture = 0;

    /**
     * focalRatio
     *
     * @var float
     */
    protected $focalRatio = 0.0;

    /**
     * purchaseDate
     *
     * @var \DateTime
     */
    protected $purchaseDate = null;

    /**
     * active
     *
     * @var bool
     */
    protected $active = false;

    /**
     * notes
     *
     * @var string
     */
    protected $notes = '';

    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $image = null;

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
     * Returns the brand
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Sets the brand
     *
     * @param string $brand
     * @return void
     */
    public function setBrand(string $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Returns the telescopeType
     *
     * @return string
     */
    public function getTelescopeType()
    {
        return $this->telescopeType;
    }

    /**
     * Sets the telescopeType
     *
     * @param string $telescopeType
     * @return void
     */
    public function setTelescopeType(string $telescopeType)
    {
        $this->telescopeType = $telescopeType;
    }

    /**
     * Returns the focalLength
     *
     * @return int
     */
    public function getFocalLength()
    {
        return $this->focalLength;
    }

    /**
     * Sets the focalLength
     *
     * @param int $focalLength
     * @return void
     */
    public function setFocalLength(int $focalLength)
    {
        $this->focalLength = $focalLength;
    }

    /**
     * Returns the aperture
     *
     * @return int
     */
    public function getAperture()
    {
        return $this->aperture;
    }

    /**
     * Sets the aperture
     *
     * @param int $aperture
     * @return void
     */
    public function setAperture(int $aperture)
    {
        $this->aperture = $aperture;
    }

    /**
     * Returns the focalRatio
     *
     * @return float
     */
    public function getFocalRatio()
    {
        return $this->focalRatio;
    }

    /**
     * Sets the focalRatio
     *
     * @param float $focalRatio
     * @return void
     */
    public function setFocalRatio(float $focalRatio)
    {
        $this->focalRatio = $focalRatio;
    }

    /**
     * Returns the purchaseDate
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Sets the purchaseDate
     *
     * @param \DateTime $purchaseDate
     * @return void
     */
    public function setPurchaseDate(\DateTime $purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;
    }

    /**
     * Returns the active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Sets the active
     *
     * @param bool $active
     * @return void
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * Returns the boolean state of active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * Returns the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Sets the notes
     *
     * @param string $notes
     * @return void
     */
    public function setNotes(string $notes)
    {
        $this->notes = $notes;
    }

    /**
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->image = $image;
    }
}
