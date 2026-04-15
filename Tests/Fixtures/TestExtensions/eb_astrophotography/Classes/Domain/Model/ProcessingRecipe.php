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
 * ProcessingRecipe
 */
class ProcessingRecipe extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * software
     *
     * @var string
     */
    protected $software = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * stackingMethod
     *
     * @var string
     */
    protected $stackingMethod = '';

    /**
     * totalIntegrationTime
     *
     * @var float
     */
    protected $totalIntegrationTime = 0.0;

    /**
     * processingDate
     *
     * @var \DateTime
     */
    protected $processingDate = null;

    /**
     * recipeFile
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $recipeFile = null;

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
     * cameras
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Camera>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $cameras = null;

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
        $this->cameras ??= new ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the software
     *
     * @return string
     */
    public function getSoftware()
    {
        return $this->software;
    }

    /**
     * Sets the software
     *
     * @param string $software
     * @return void
     */
    public function setSoftware(string $software)
    {
        $this->software = $software;
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
     * Returns the stackingMethod
     *
     * @return string
     */
    public function getStackingMethod()
    {
        return $this->stackingMethod;
    }

    /**
     * Sets the stackingMethod
     *
     * @param string $stackingMethod
     * @return void
     */
    public function setStackingMethod(string $stackingMethod)
    {
        $this->stackingMethod = $stackingMethod;
    }

    /**
     * Returns the totalIntegrationTime
     *
     * @return float
     */
    public function getTotalIntegrationTime()
    {
        return $this->totalIntegrationTime;
    }

    /**
     * Sets the totalIntegrationTime
     *
     * @param float $totalIntegrationTime
     * @return void
     */
    public function setTotalIntegrationTime(float $totalIntegrationTime)
    {
        $this->totalIntegrationTime = $totalIntegrationTime;
    }

    /**
     * Returns the processingDate
     *
     * @return \DateTime
     */
    public function getProcessingDate()
    {
        return $this->processingDate;
    }

    /**
     * Sets the processingDate
     *
     * @param \DateTime $processingDate
     * @return void
     */
    public function setProcessingDate(\DateTime $processingDate)
    {
        $this->processingDate = $processingDate;
    }

    /**
     * Returns the recipeFile
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getRecipeFile()
    {
        return $this->recipeFile;
    }

    /**
     * Sets the recipeFile
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $recipeFile
     * @return void
     */
    public function setRecipeFile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $recipeFile)
    {
        $this->recipeFile = $recipeFile;
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
}
