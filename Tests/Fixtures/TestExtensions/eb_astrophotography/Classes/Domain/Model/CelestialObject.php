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
 * CelestialObject
 */
class CelestialObject extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * catalogId
     *
     * @var string
     */
    protected $catalogId = '';

    /**
     * objectType
     *
     * @var int
     */
    protected $objectType = 0;

    /**
     * constellation
     *
     * @var string
     */
    protected $constellation = '';

    /**
     * rightAscension
     *
     * @var string
     */
    protected $rightAscension = '';

    /**
     * declination
     *
     * @var string
     */
    protected $declination = '';

    /**
     * magnitude
     *
     * @var float
     */
    protected $magnitude = 0.0;

    /**
     * distanceLightyears
     *
     * @var float
     */
    protected $distanceLightyears = 0.0;

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * previewImage
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $previewImage = null;

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
     * Returns the catalogId
     *
     * @return string
     */
    public function getCatalogId()
    {
        return $this->catalogId;
    }

    /**
     * Sets the catalogId
     *
     * @param string $catalogId
     * @return void
     */
    public function setCatalogId(string $catalogId)
    {
        $this->catalogId = $catalogId;
    }

    /**
     * Returns the objectType
     *
     * @return int
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Sets the objectType
     *
     * @param int $objectType
     * @return void
     */
    public function setObjectType(int $objectType)
    {
        $this->objectType = $objectType;
    }

    /**
     * Returns the constellation
     *
     * @return string
     */
    public function getConstellation()
    {
        return $this->constellation;
    }

    /**
     * Sets the constellation
     *
     * @param string $constellation
     * @return void
     */
    public function setConstellation(string $constellation)
    {
        $this->constellation = $constellation;
    }

    /**
     * Returns the rightAscension
     *
     * @return string
     */
    public function getRightAscension()
    {
        return $this->rightAscension;
    }

    /**
     * Sets the rightAscension
     *
     * @param string $rightAscension
     * @return void
     */
    public function setRightAscension(string $rightAscension)
    {
        $this->rightAscension = $rightAscension;
    }

    /**
     * Returns the declination
     *
     * @return string
     */
    public function getDeclination()
    {
        return $this->declination;
    }

    /**
     * Sets the declination
     *
     * @param string $declination
     * @return void
     */
    public function setDeclination(string $declination)
    {
        $this->declination = $declination;
    }

    /**
     * Returns the magnitude
     *
     * @return float
     */
    public function getMagnitude()
    {
        return $this->magnitude;
    }

    /**
     * Sets the magnitude
     *
     * @param float $magnitude
     * @return void
     */
    public function setMagnitude(float $magnitude)
    {
        $this->magnitude = $magnitude;
    }

    /**
     * Returns the distanceLightyears
     *
     * @return float
     */
    public function getDistanceLightyears()
    {
        return $this->distanceLightyears;
    }

    /**
     * Sets the distanceLightyears
     *
     * @param float $distanceLightyears
     * @return void
     */
    public function setDistanceLightyears(float $distanceLightyears)
    {
        $this->distanceLightyears = $distanceLightyears;
    }

    /**
     * Returns the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns the previewImage
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }

    /**
     * Sets the previewImage
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $previewImage
     * @return void
     */
    public function setPreviewImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $previewImage)
    {
        $this->previewImage = $previewImage;
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
