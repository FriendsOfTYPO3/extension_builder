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
 * AstroFilter
 */
class AstroFilter extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * filterType
     *
     * @var int
     */
    protected $filterType = 0;

    /**
     * centralWavelength
     *
     * @var int
     */
    protected $centralWavelength = 0;

    /**
     * bandwidth
     *
     * @var float
     */
    protected $bandwidth = 0.0;

    /**
     * color
     *
     * @var string
     */
    protected $color = '';

    /**
     * manufacturer
     *
     * @var string
     */
    protected $manufacturer = '';

    /**
     * diameter
     *
     * @var float
     */
    protected $diameter = 0.0;

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
     * Returns the filterType
     *
     * @return int
     */
    public function getFilterType()
    {
        return $this->filterType;
    }

    /**
     * Sets the filterType
     *
     * @param int $filterType
     * @return void
     */
    public function setFilterType(int $filterType)
    {
        $this->filterType = $filterType;
    }

    /**
     * Returns the centralWavelength
     *
     * @return int
     */
    public function getCentralWavelength()
    {
        return $this->centralWavelength;
    }

    /**
     * Sets the centralWavelength
     *
     * @param int $centralWavelength
     * @return void
     */
    public function setCentralWavelength(int $centralWavelength)
    {
        $this->centralWavelength = $centralWavelength;
    }

    /**
     * Returns the bandwidth
     *
     * @return float
     */
    public function getBandwidth()
    {
        return $this->bandwidth;
    }

    /**
     * Sets the bandwidth
     *
     * @param float $bandwidth
     * @return void
     */
    public function setBandwidth(float $bandwidth)
    {
        $this->bandwidth = $bandwidth;
    }

    /**
     * Returns the color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Sets the color
     *
     * @param string $color
     * @return void
     */
    public function setColor(string $color)
    {
        $this->color = $color;
    }

    /**
     * Returns the manufacturer
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Sets the manufacturer
     *
     * @param string $manufacturer
     * @return void
     */
    public function setManufacturer(string $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * Returns the diameter
     *
     * @return float
     */
    public function getDiameter()
    {
        return $this->diameter;
    }

    /**
     * Sets the diameter
     *
     * @param float $diameter
     * @return void
     */
    public function setDiameter(float $diameter)
    {
        $this->diameter = $diameter;
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
