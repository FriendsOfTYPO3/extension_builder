<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class ImagingSessionTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getSessionDateReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getSessionDate()
        );
    }

    /**
     * @test
     */
    public function setSessionDateForDateTimeSetsSessionDate(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setSessionDate($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('sessionDate'));
    }

    /**
     * @test
     */
    public function getStartTimeReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getStartTime()
        );
    }

    /**
     * @test
     */
    public function setStartTimeForDateTimeSetsStartTime(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setStartTime($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('startTime'));
    }

    /**
     * @test
     */
    public function getEndTimeReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getEndTime()
        );
    }

    /**
     * @test
     */
    public function setEndTimeForIntSetsEndTime(): void
    {
        $this->subject->setEndTime(12);

        self::assertEquals(12, $this->subject->_get('endTime'));
    }

    /**
     * @test
     */
    public function getFrameExposureReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getFrameExposure()
        );
    }

    /**
     * @test
     */
    public function setFrameExposureForIntSetsFrameExposure(): void
    {
        $this->subject->setFrameExposure(12);

        self::assertEquals(12, $this->subject->_get('frameExposure'));
    }

    /**
     * @test
     */
    public function getTemperatureReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getTemperature()
        );
    }

    /**
     * @test
     */
    public function setTemperatureForFloatSetsTemperature(): void
    {
        $this->subject->setTemperature(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('temperature'));
    }

    /**
     * @test
     */
    public function getHumidityReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getHumidity()
        );
    }

    /**
     * @test
     */
    public function setHumidityForIntSetsHumidity(): void
    {
        $this->subject->setHumidity(12);

        self::assertEquals(12, $this->subject->_get('humidity'));
    }

    /**
     * @test
     */
    public function getSeeingConditionsReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getSeeingConditions()
        );
    }

    /**
     * @test
     */
    public function setSeeingConditionsForIntSetsSeeingConditions(): void
    {
        $this->subject->setSeeingConditions(12);

        self::assertEquals(12, $this->subject->_get('seeingConditions'));
    }

    /**
     * @test
     */
    public function getTransparencyReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getTransparency()
        );
    }

    /**
     * @test
     */
    public function setTransparencyForIntSetsTransparency(): void
    {
        $this->subject->setTransparency(12);

        self::assertEquals(12, $this->subject->_get('transparency'));
    }

    /**
     * @test
     */
    public function getMoonPhaseReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getMoonPhase()
        );
    }

    /**
     * @test
     */
    public function setMoonPhaseForIntSetsMoonPhase(): void
    {
        $this->subject->setMoonPhase(12);

        self::assertEquals(12, $this->subject->_get('moonPhase'));
    }

    /**
     * @test
     */
    public function getTotalFramesReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getTotalFrames()
        );
    }

    /**
     * @test
     */
    public function setTotalFramesForIntSetsTotalFrames(): void
    {
        $this->subject->setTotalFrames(12);

        self::assertEquals(12, $this->subject->_get('totalFrames'));
    }

    /**
     * @test
     */
    public function getUsableFramesReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getUsableFrames()
        );
    }

    /**
     * @test
     */
    public function setUsableFramesForIntSetsUsableFrames(): void
    {
        $this->subject->setUsableFrames(12);

        self::assertEquals(12, $this->subject->_get('usableFrames'));
    }

    /**
     * @test
     */
    public function getNotesReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getNotes()
        );
    }

    /**
     * @test
     */
    public function setNotesForStringSetsNotes(): void
    {
        $this->subject->setNotes('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('notes'));
    }

    /**
     * @test
     */
    public function getObservingSitesReturnsInitialValueForObservingSite(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getObservingSites()
        );
    }

    /**
     * @test
     */
    public function setObservingSitesForObjectStorageContainingObservingSiteSetsObservingSites(): void
    {
        $observingSite = new \AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite();
        $objectStorageHoldingExactlyOneObservingSites = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneObservingSites->attach($observingSite);
        $this->subject->setObservingSites($objectStorageHoldingExactlyOneObservingSites);

        self::assertEquals($objectStorageHoldingExactlyOneObservingSites, $this->subject->_get('observingSites'));
    }

    /**
     * @test
     */
    public function addObservingSiteToObjectStorageHoldingObservingSites(): void
    {
        $observingSite = new \AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite();
        $observingSitesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $observingSitesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($observingSite));
        $this->subject->_set('observingSites', $observingSitesObjectStorageMock);

        $this->subject->addObservingSite($observingSite);
    }

    /**
     * @test
     */
    public function removeObservingSiteFromObjectStorageHoldingObservingSites(): void
    {
        $observingSite = new \AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite();
        $observingSitesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $observingSitesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($observingSite));
        $this->subject->_set('observingSites', $observingSitesObjectStorageMock);

        $this->subject->removeObservingSite($observingSite);
    }

    /**
     * @test
     */
    public function getTelescopesReturnsInitialValueForTelescope(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getTelescopes()
        );
    }

    /**
     * @test
     */
    public function setTelescopesForObjectStorageContainingTelescopeSetsTelescopes(): void
    {
        $telescope = new \AcmeCorp\EbAstrophotography\Domain\Model\Telescope();
        $objectStorageHoldingExactlyOneTelescopes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneTelescopes->attach($telescope);
        $this->subject->setTelescopes($objectStorageHoldingExactlyOneTelescopes);

        self::assertEquals($objectStorageHoldingExactlyOneTelescopes, $this->subject->_get('telescopes'));
    }

    /**
     * @test
     */
    public function addTelescopeToObjectStorageHoldingTelescopes(): void
    {
        $telescope = new \AcmeCorp\EbAstrophotography\Domain\Model\Telescope();
        $telescopesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $telescopesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($telescope));
        $this->subject->_set('telescopes', $telescopesObjectStorageMock);

        $this->subject->addTelescope($telescope);
    }

    /**
     * @test
     */
    public function removeTelescopeFromObjectStorageHoldingTelescopes(): void
    {
        $telescope = new \AcmeCorp\EbAstrophotography\Domain\Model\Telescope();
        $telescopesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $telescopesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($telescope));
        $this->subject->_set('telescopes', $telescopesObjectStorageMock);

        $this->subject->removeTelescope($telescope);
    }

    /**
     * @test
     */
    public function getCamerasReturnsInitialValueForCamera(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getCameras()
        );
    }

    /**
     * @test
     */
    public function setCamerasForObjectStorageContainingCameraSetsCameras(): void
    {
        $camera = new \AcmeCorp\EbAstrophotography\Domain\Model\Camera();
        $objectStorageHoldingExactlyOneCameras = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneCameras->attach($camera);
        $this->subject->setCameras($objectStorageHoldingExactlyOneCameras);

        self::assertEquals($objectStorageHoldingExactlyOneCameras, $this->subject->_get('cameras'));
    }

    /**
     * @test
     */
    public function addCameraToObjectStorageHoldingCameras(): void
    {
        $camera = new \AcmeCorp\EbAstrophotography\Domain\Model\Camera();
        $camerasObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $camerasObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($camera));
        $this->subject->_set('cameras', $camerasObjectStorageMock);

        $this->subject->addCamera($camera);
    }

    /**
     * @test
     */
    public function removeCameraFromObjectStorageHoldingCameras(): void
    {
        $camera = new \AcmeCorp\EbAstrophotography\Domain\Model\Camera();
        $camerasObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $camerasObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($camera));
        $this->subject->_set('cameras', $camerasObjectStorageMock);

        $this->subject->removeCamera($camera);
    }

    /**
     * @test
     */
    public function getAstroFiltersReturnsInitialValueForAstroFilter(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getAstroFilters()
        );
    }

    /**
     * @test
     */
    public function setAstroFiltersForObjectStorageContainingAstroFilterSetsAstroFilters(): void
    {
        $astroFilter = new \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter();
        $objectStorageHoldingExactlyOneAstroFilters = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneAstroFilters->attach($astroFilter);
        $this->subject->setAstroFilters($objectStorageHoldingExactlyOneAstroFilters);

        self::assertEquals($objectStorageHoldingExactlyOneAstroFilters, $this->subject->_get('astroFilters'));
    }

    /**
     * @test
     */
    public function addAstroFilterToObjectStorageHoldingAstroFilters(): void
    {
        $astroFilter = new \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter();
        $astroFiltersObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $astroFiltersObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($astroFilter));
        $this->subject->_set('astroFilters', $astroFiltersObjectStorageMock);

        $this->subject->addAstroFilter($astroFilter);
    }

    /**
     * @test
     */
    public function removeAstroFilterFromObjectStorageHoldingAstroFilters(): void
    {
        $astroFilter = new \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter();
        $astroFiltersObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $astroFiltersObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($astroFilter));
        $this->subject->_set('astroFilters', $astroFiltersObjectStorageMock);

        $this->subject->removeAstroFilter($astroFilter);
    }
}
