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
 * AstroImage
 */
class AstroImage extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * slug
     *
     * @var string
     */
    protected $slug = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $image = null;

    /**
     * captureDateTime
     *
     * @var \DateTime
     */
    protected $captureDateTime = null;

    /**
     * publishDate
     *
     * @var \DateTime
     */
    protected $publishDate = null;

    /**
     * featured
     *
     * @var bool
     */
    protected $featured = false;

    /**
     * stackCount
     *
     * @var int
     */
    protected $stackCount = 0;

    /**
     * celestialObjects
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $celestialObjects = null;

    /**
     * imagingSessions
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $imagingSessions = null;

    /**
     * processingRecipes
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $processingRecipes = null;

    /**
     * awards
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Award>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $awards = null;

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
        $this->celestialObjects ??= new ObjectStorage();
        $this->imagingSessions ??= new ObjectStorage();
        $this->processingRecipes ??= new ObjectStorage();
        $this->awards ??= new ObjectStorage();
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
     * Returns the slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the slug
     *
     * @param string $slug
     * @return void
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
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

    /**
     * Returns the captureDateTime
     *
     * @return \DateTime
     */
    public function getCaptureDateTime()
    {
        return $this->captureDateTime;
    }

    /**
     * Sets the captureDateTime
     *
     * @param \DateTime $captureDateTime
     * @return void
     */
    public function setCaptureDateTime(\DateTime $captureDateTime)
    {
        $this->captureDateTime = $captureDateTime;
    }

    /**
     * Returns the publishDate
     *
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Sets the publishDate
     *
     * @param \DateTime $publishDate
     * @return void
     */
    public function setPublishDate(\DateTime $publishDate)
    {
        $this->publishDate = $publishDate;
    }

    /**
     * Returns the featured
     *
     * @return bool
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Sets the featured
     *
     * @param bool $featured
     * @return void
     */
    public function setFeatured(bool $featured)
    {
        $this->featured = $featured;
    }

    /**
     * Returns the boolean state of featured
     *
     * @return bool
     */
    public function isFeatured()
    {
        return (bool) $this->featured;
    }

    /**
     * Returns the stackCount
     *
     * @return int
     */
    public function getStackCount()
    {
        return $this->stackCount;
    }

    /**
     * Sets the stackCount
     *
     * @param int $stackCount
     * @return void
     */
    public function setStackCount(int $stackCount)
    {
        $this->stackCount = $stackCount;
    }

    /**
     * Adds a CelestialObject
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $celestialObject
     * @return void
     */
    public function addCelestialObject(\AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $celestialObject)
    {
        $this->celestialObjects->attach($celestialObject);
    }

    /**
     * Removes a CelestialObject
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $celestialObjectToRemove The CelestialObject to be removed
     * @return void
     */
    public function removeCelestialObject(\AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject $celestialObjectToRemove)
    {
        $this->celestialObjects->detach($celestialObjectToRemove);
    }

    /**
     * Returns the celestialObjects
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject>
     */
    public function getCelestialObjects()
    {
        return $this->celestialObjects;
    }

    /**
     * Sets the celestialObjects
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject> $celestialObjects
     * @return void
     */
    public function setCelestialObjects(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $celestialObjects)
    {
        $this->celestialObjects = $celestialObjects;
    }

    /**
     * Adds a ImagingSession
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession $imagingSession
     * @return void
     */
    public function addImagingSession(\AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession $imagingSession)
    {
        $this->imagingSessions->attach($imagingSession);
    }

    /**
     * Removes a ImagingSession
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession $imagingSessionToRemove The ImagingSession to be removed
     * @return void
     */
    public function removeImagingSession(\AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession $imagingSessionToRemove)
    {
        $this->imagingSessions->detach($imagingSessionToRemove);
    }

    /**
     * Returns the imagingSessions
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession>
     */
    public function getImagingSessions()
    {
        return $this->imagingSessions;
    }

    /**
     * Sets the imagingSessions
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession> $imagingSessions
     * @return void
     */
    public function setImagingSessions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $imagingSessions)
    {
        $this->imagingSessions = $imagingSessions;
    }

    /**
     * Adds a ProcessingRecipe
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe $processingRecipe
     * @return void
     */
    public function addProcessingRecipe(\AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe $processingRecipe)
    {
        $this->processingRecipes->attach($processingRecipe);
    }

    /**
     * Removes a ProcessingRecipe
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe $processingRecipeToRemove The ProcessingRecipe to be removed
     * @return void
     */
    public function removeProcessingRecipe(\AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe $processingRecipeToRemove)
    {
        $this->processingRecipes->detach($processingRecipeToRemove);
    }

    /**
     * Returns the processingRecipes
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe>
     */
    public function getProcessingRecipes()
    {
        return $this->processingRecipes;
    }

    /**
     * Sets the processingRecipes
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe> $processingRecipes
     * @return void
     */
    public function setProcessingRecipes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $processingRecipes)
    {
        $this->processingRecipes = $processingRecipes;
    }

    /**
     * Adds a Award
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Award $award
     * @return void
     */
    public function addAward(\AcmeCorp\EbAstrophotography\Domain\Model\Award $award)
    {
        $this->awards->attach($award);
    }

    /**
     * Removes a Award
     *
     * @param \AcmeCorp\EbAstrophotography\Domain\Model\Award $awardToRemove The Award to be removed
     * @return void
     */
    public function removeAward(\AcmeCorp\EbAstrophotography\Domain\Model\Award $awardToRemove)
    {
        $this->awards->detach($awardToRemove);
    }

    /**
     * Returns the awards
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Award>
     */
    public function getAwards()
    {
        return $this->awards;
    }

    /**
     * Sets the awards
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AcmeCorp\EbAstrophotography\Domain\Model\Award> $awards
     * @return void
     */
    public function setAwards(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $awards)
    {
        $this->awards = $awards;
    }
}
