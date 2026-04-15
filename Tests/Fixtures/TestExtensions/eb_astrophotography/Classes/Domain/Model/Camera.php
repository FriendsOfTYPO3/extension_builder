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
 * Camera
 */
class Camera extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
     * sensorType
     *
     * @var string
     */
    protected $sensorType = '';

    /**
     * sensorWidth
     *
     * @var float
     */
    protected $sensorWidth = 0.0;

    /**
     * sensorHeight
     *
     * @var float
     */
    protected $sensorHeight = 0.0;

    /**
     * pixelSize
     *
     * @var float
     */
    protected $pixelSize = 0.0;

    /**
     * megapixels
     *
     * @var float
     */
    protected $megapixels = 0.0;

    /**
     * cooled
     *
     * @var bool
     */
    protected $cooled = false;

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
     * Returns the sensorType
     *
     * @return string
     */
    public function getSensorType()
    {
        return $this->sensorType;
    }

    /**
     * Sets the sensorType
     *
     * @param string $sensorType
     * @return void
     */
    public function setSensorType(string $sensorType)
    {
        $this->sensorType = $sensorType;
    }

    /**
     * Returns the sensorWidth
     *
     * @return float
     */
    public function getSensorWidth()
    {
        return $this->sensorWidth;
    }

    /**
     * Sets the sensorWidth
     *
     * @param float $sensorWidth
     * @return void
     */
    public function setSensorWidth(float $sensorWidth)
    {
        $this->sensorWidth = $sensorWidth;
    }

    /**
     * Returns the sensorHeight
     *
     * @return float
     */
    public function getSensorHeight()
    {
        return $this->sensorHeight;
    }

    /**
     * Sets the sensorHeight
     *
     * @param float $sensorHeight
     * @return void
     */
    public function setSensorHeight(float $sensorHeight)
    {
        $this->sensorHeight = $sensorHeight;
    }

    /**
     * Returns the pixelSize
     *
     * @return float
     */
    public function getPixelSize()
    {
        return $this->pixelSize;
    }

    /**
     * Sets the pixelSize
     *
     * @param float $pixelSize
     * @return void
     */
    public function setPixelSize(float $pixelSize)
    {
        $this->pixelSize = $pixelSize;
    }

    /**
     * Returns the megapixels
     *
     * @return float
     */
    public function getMegapixels()
    {
        return $this->megapixels;
    }

    /**
     * Sets the megapixels
     *
     * @param float $megapixels
     * @return void
     */
    public function setMegapixels(float $megapixels)
    {
        $this->megapixels = $megapixels;
    }

    /**
     * Returns the cooled
     *
     * @return bool
     */
    public function getCooled()
    {
        return $this->cooled;
    }

    /**
     * Sets the cooled
     *
     * @param bool $cooled
     * @return void
     */
    public function setCooled(bool $cooled)
    {
        $this->cooled = $cooled;
    }

    /**
     * Returns the boolean state of cooled
     *
     * @return bool
     */
    public function isCooled()
    {
        return (bool) $this->cooled;
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
}
