<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class ProcessingRecipeTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe::class,
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
    public function getTitleReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle(): void
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('title'));
    }

    /**
     * @test
     */
    public function getSoftwareReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getSoftware()
        );
    }

    /**
     * @test
     */
    public function setSoftwareForStringSetsSoftware(): void
    {
        $this->subject->setSoftware('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('software'));
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription(): void
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('description'));
    }

    /**
     * @test
     */
    public function getStackingMethodReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getStackingMethod()
        );
    }

    /**
     * @test
     */
    public function setStackingMethodForStringSetsStackingMethod(): void
    {
        $this->subject->setStackingMethod('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('stackingMethod'));
    }

    /**
     * @test
     */
    public function getTotalIntegrationTimeReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getTotalIntegrationTime()
        );
    }

    /**
     * @test
     */
    public function setTotalIntegrationTimeForFloatSetsTotalIntegrationTime(): void
    {
        $this->subject->setTotalIntegrationTime(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('totalIntegrationTime'));
    }

    /**
     * @test
     */
    public function getProcessingDateReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getProcessingDate()
        );
    }

    /**
     * @test
     */
    public function setProcessingDateForDateTimeSetsProcessingDate(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setProcessingDate($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('processingDate'));
    }

    /**
     * @test
     */
    public function getRecipeFileReturnsInitialValueForFileReference(): void
    {
        self::assertEquals(
            null,
            $this->subject->getRecipeFile()
        );
    }

    /**
     * @test
     */
    public function setRecipeFileForFileReferenceSetsRecipeFile(): void
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setRecipeFile($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('recipeFile'));
    }

    /**
     * @test
     */
    public function getActiveReturnsInitialValueForBool(): void
    {
        self::assertFalse($this->subject->getActive());
    }

    /**
     * @test
     */
    public function setActiveForBoolSetsActive(): void
    {
        $this->subject->setActive(true);

        self::assertEquals(true, $this->subject->_get('active'));
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
}
