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
 * ImagingSession
 */
class ImagingSession extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * sessionDate
     *
     * @var \DateTime
     */
    protected $sessionDate = null;

    /**
     * startTime
     *
     * @var \DateTime
     */
    protected $startTime = null;

    /**
     * endTime
     *
     * @var int
     */
    protected $endTime = 0;

    /**
     * frameExposure
     *
     * @var int
     */
    protected $frameExposure = 0;

    /**
     * temperature
     *
     * @var float
     */
    protected $temperature = 0.0;

    /**
     * humidity
     *
     * @var int
     */
    protected $humidity = 0;

    /**
     * seeingConditions
     *
     * @var int
     */
    protected $seeingConditions = 0;

    /**
     * transparency
     *
     * @var int
     */
    protected $transparency = 0;

    /**
     * moonPhase
     *
     * @var int
     */
    protected $moonPhase = 0;

    /**
     * totalFrames
     *
     * @var int
     */
    protected $totalFrames = 0;

    /**
     * usableFrames
     *
     * @var int
     */
    protected $usableFrames = 0;

    /**
     * notes
     *
     * @var string
     */
    protected $notes = '';

    /**
     * observingSites
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $observingSites = null;

    /**
     * telescopes
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Telescope>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $telescopes = null;

    /**
     * cameras
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Camera>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $cameras = null;

    /**
     * astroFilters
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $astroFilters = null;

    /**
     * __construct
     */
    public function __construct()
    {

        // Do not remove the next line: It would break the functionality
        $this->initializeObject();
    }

    /**
     * Initializes all ObjectStorage properties when model is reconstructed from DB (where __construct is not called)
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     */
    public function initializeObject()
    {
        $this->observingSites ??= new ObjectStorage();
        $this->telescopes ??= new ObjectStorage();
        $this->cameras ??= new ObjectStorage();
        $this->astroFilters ??= new ObjectStorage();
    }

    /**
     * Returns the sessionDate
     *
     * @return \DateTime
     */
    public function getSessionDate()
    {
        return $this->sessionDate;
    }

    /**
     * Sets the sessionDate
     *
     * @param \DateTime $sessionDate
     * @return void
     */
    public function setSessionDate(\DateTime $sessionDate)
    {
        $this->sessionDate = $sessionDate;
    }

    /**
     * Returns the startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Sets the startTime
     *
     * @param \DateTime $startTime
     * @return void
     */
    public function setStartTime(\DateTime $startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Returns the endTime
     *
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Sets the endTime
     *
     * @param int $endTime
     * @return void
     */
    public function setEndTime(int $endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * Returns the frameExposure
     *
     * @return int
     */
    public function getFrameExposure()
    {
        return $this->frameExposure;
    }

    /**
     * Sets the frameExposure
     *
     * @param int $frameExposure
     * @return void
     */
    public function setFrameExposure(int $frameExposure)
    {
        $this->frameExposure = $frameExposure;
    }

    /**
     * Returns the temperature
     *
     * @return float
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * Sets the temperature
     *
     * @param float $temperature
     * @return void
     */
    public function setTemperature(float $temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * Returns the humidity
     *
     * @return int
     */
    public function getHumidity()
    {
        return $this->humidity;
    }

    /**
     * Sets the humidity
     *
     * @param int $humidity
     * @return void
     */
    public function setHumidity(int $humidity)
    {
        $this->humidity = $humidity;
    }

    /**
     * Returns the seeingConditions
     *
     * @return int
     */
    public function getSeeingConditions()
    {
        return $this->seeingConditions;
    }

    /**
     * Sets the seeingConditions
     *
     * @param int $seeingConditions
     * @return void
     */
    public function setSeeingConditions(int $seeingConditions)
    {
        $this->seeingConditions = $seeingConditions;
    }

    /**
     * Returns the transparency
     *
     * @return int
     */
    public function getTransparency()
    {
        return $this->transparency;
    }

    /**
     * Sets the transparency
     *
     * @param int $transparency
     * @return void
     */
    public function setTransparency(int $transparency)
    {
        $this->transparency = $transparency;
    }

    /**
     * Returns the moonPhase
     *
     * @return int
     */
    public function getMoonPhase()
    {
        return $this->moonPhase;
    }

    /**
     * Sets the moonPhase
     *
     * @param int $moonPhase
     * @return void
     */
    public function setMoonPhase(int $moonPhase)
    {
        $this->moonPhase = $moonPhase;
    }

    /**
     * Returns the totalFrames
     *
     * @return int
     */
    public function getTotalFrames()
    {
        return $this->totalFrames;
    }

    /**
     * Sets the totalFrames
     *
     * @param int $totalFrames
     * @return void
     */
    public function setTotalFrames(int $totalFrames)
    {
        $this->totalFrames = $totalFrames;
    }

    /**
     * Returns the usableFrames
     *
     * @return int
     */
    public function getUsableFrames()
    {
        return $this->usableFrames;
    }

    /**
     * Sets the usableFrames
     *
     * @param int $usableFrames
     * @return void
     */
    public function setUsableFrames(int $usableFrames)
    {
        $this->usableFrames = $usableFrames;
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
     * Adds a ObservingSite
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite $observingSite
     * @return void
     */
    public function addObservingSite(\AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite $observingSite)
    {
        $this->observingSites->attach($observingSite);
    }

    /**
     * Removes a ObservingSite
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite $observingSiteToRemove The ObservingSite to be removed
     * @return void
     */
    public function removeObservingSite(\AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite $observingSiteToRemove)
    {
        $this->observingSites->detach($observingSiteToRemove);
    }

    /**
     * Returns the observingSites
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite>
     */
    public function getObservingSites()
    {
        return $this->observingSites;
    }

    /**
     * Sets the observingSites
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite> $observingSites
     * @return void
     */
    public function setObservingSites(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $observingSites)
    {
        $this->observingSites = $observingSites;
    }

    /**
     * Adds a Telescope
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Telescope $telescope
     * @return void
     */
    public function addTelescope(\AcmeCorp\EbAstrophotography\Domain\Model\Telescope $telescope)
    {
        $this->telescopes->attach($telescope);
    }

    /**
     * Removes a Telescope
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Telescope $telescopeToRemove The Telescope to be removed
     * @return void
     */
    public function removeTelescope(\AcmeCorp\EbAstrophotography\Domain\Model\Telescope $telescopeToRemove)
    {
        $this->telescopes->detach($telescopeToRemove);
    }

    /**
     * Returns the telescopes
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Telescope>
     */
    public function getTelescopes()
    {
        return $this->telescopes;
    }

    /**
     * Sets the telescopes
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Telescope> $telescopes
     * @return void
     */
    public function setTelescopes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $telescopes)
    {
        $this->telescopes = $telescopes;
    }

    /**
     * Adds a Camera
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Camera $camera
     * @return void
     */
    public function addCamera(\AcmeCorp\EbAstrophotography\Domain\Model\Camera $camera)
    {
        $this->cameras->attach($camera);
    }

    /**
     * Removes a Camera
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Camera $cameraToRemove The Camera to be removed
     * @return void
     */
    public function removeCamera(\AcmeCorp\EbAstrophotography\Domain\Model\Camera $cameraToRemove)
    {
        $this->cameras->detach($cameraToRemove);
    }

    /**
     * Returns the cameras
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Camera>
     */
    public function getCameras()
    {
        return $this->cameras;
    }

    /**
     * Sets the cameras
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Camera> $cameras
     * @return void
     */
    public function setCameras(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $cameras)
    {
        $this->cameras = $cameras;
    }

    /**
     * Adds a AstroFilter
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter $astroFilter
     * @return void
     */
    public function addAstroFilter(\AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter $astroFilter)
    {
        $this->astroFilters->attach($astroFilter);
    }

    /**
     * Removes a AstroFilter
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter $astroFilterToRemove The AstroFilter to be removed
     * @return void
     */
    public function removeAstroFilter(\AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter $astroFilterToRemove)
    {
        $this->astroFilters->detach($astroFilterToRemove);
    }

    /**
     * Returns the astroFilters
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter>
     */
    public function getAstroFilters()
    {
        return $this->astroFilters;
    }

    /**
     * Sets the astroFilters
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter> $astroFilters
     * @return void
     */
    public function setAstroFilters(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $astroFilters)
    {
        $this->astroFilters = $astroFilters;
    }
}
