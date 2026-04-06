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
 * Award
 */
class Award extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * organization
     *
     * @var string
     */
    protected $organization = '';

    /**
     * awardDate
     *
     * @var \DateTime
     */
    protected $awardDate = null;

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * certificateFile
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $certificateFile = null;

    /**
     * sourceUrl
     *
     * @var string
     */
    protected $sourceUrl = '';

    /**
     * active
     *
     * @var bool
     */
    protected $active = false;

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
     * Returns the organization
     *
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Sets the organization
     *
     * @param string $organization
     * @return void
     */
    public function setOrganization(string $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Returns the awardDate
     *
     * @return \DateTime
     */
    public function getAwardDate()
    {
        return $this->awardDate;
    }

    /**
     * Sets the awardDate
     *
     * @param \DateTime $awardDate
     * @return void
     */
    public function setAwardDate(\DateTime $awardDate)
    {
        $this->awardDate = $awardDate;
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
     * Returns the certificateFile
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getCertificateFile()
    {
        return $this->certificateFile;
    }

    /**
     * Sets the certificateFile
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $certificateFile
     * @return void
     */
    public function setCertificateFile(\TYPO3\CMS\Extbase\Domain\Model\FileReference $certificateFile)
    {
        $this->certificateFile = $certificateFile;
    }

    /**
     * Returns the sourceUrl
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * Sets the sourceUrl
     *
     * @param string $sourceUrl
     * @return void
     */
    public function setSourceUrl(string $sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
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
