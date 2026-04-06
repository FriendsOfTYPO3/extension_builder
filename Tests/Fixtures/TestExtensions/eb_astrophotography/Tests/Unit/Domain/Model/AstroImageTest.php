<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class AstroImageTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\AstroImage|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\AstroImage::class,
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
    public function getSlugReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getSlug()
        );
    }

    /**
     * @test
     */
    public function setSlugForStringSetsSlug(): void
    {
        $this->subject->setSlug('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('slug'));
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
    public function getImageReturnsInitialValueForFileReference(): void
    {
        self::assertEquals(
            null,
            $this->subject->getImage()
        );
    }

    /**
     * @test
     */
    public function setImageForFileReferenceSetsImage(): void
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setImage($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('image'));
    }

    /**
     * @test
     */
    public function getCaptureDateTimeReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getCaptureDateTime()
        );
    }

    /**
     * @test
     */
    public function setCaptureDateTimeForDateTimeSetsCaptureDateTime(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setCaptureDateTime($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('captureDateTime'));
    }

    /**
     * @test
     */
    public function getPublishDateReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getPublishDate()
        );
    }

    /**
     * @test
     */
    public function setPublishDateForDateTimeSetsPublishDate(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setPublishDate($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('publishDate'));
    }

    /**
     * @test
     */
    public function getFeaturedReturnsInitialValueForBool(): void
    {
        self::assertFalse($this->subject->getFeatured());
    }

    /**
     * @test
     */
    public function setFeaturedForBoolSetsFeatured(): void
    {
        $this->subject->setFeatured(true);

        self::assertEquals(true, $this->subject->_get('featured'));
    }

    /**
     * @test
     */
    public function getStackCountReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getStackCount()
        );
    }

    /**
     * @test
     */
    public function setStackCountForIntSetsStackCount(): void
    {
        $this->subject->setStackCount(12);

        self::assertEquals(12, $this->subject->_get('stackCount'));
    }

    /**
     * @test
     */
    public function getCelestialObjectsReturnsInitialValueForCelestialObject(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getCelestialObjects()
        );
    }

    /**
     * @test
     */
    public function setCelestialObjectsForObjectStorageContainingCelestialObjectSetsCelestialObjects(): void
    {
        $celestialObject = new \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject();
        $objectStorageHoldingExactlyOneCelestialObjects = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneCelestialObjects->attach($celestialObject);
        $this->subject->setCelestialObjects($objectStorageHoldingExactlyOneCelestialObjects);

        self::assertEquals($objectStorageHoldingExactlyOneCelestialObjects, $this->subject->_get('celestialObjects'));
    }

    /**
     * @test
     */
    public function addCelestialObjectToObjectStorageHoldingCelestialObjects(): void
    {
        $celestialObject = new \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject();
        $celestialObjectsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $celestialObjectsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($celestialObject));
        $this->subject->_set('celestialObjects', $celestialObjectsObjectStorageMock);

        $this->subject->addCelestialObject($celestialObject);
    }

    /**
     * @test
     */
    public function removeCelestialObjectFromObjectStorageHoldingCelestialObjects(): void
    {
        $celestialObject = new \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject();
        $celestialObjectsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $celestialObjectsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($celestialObject));
        $this->subject->_set('celestialObjects', $celestialObjectsObjectStorageMock);

        $this->subject->removeCelestialObject($celestialObject);
    }

    /**
     * @test
     */
    public function getImagingSessionsReturnsInitialValueForImagingSession(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getImagingSessions()
        );
    }

    /**
     * @test
     */
    public function setImagingSessionsForObjectStorageContainingImagingSessionSetsImagingSessions(): void
    {
        $imagingSession = new \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession();
        $objectStorageHoldingExactlyOneImagingSessions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneImagingSessions->attach($imagingSession);
        $this->subject->setImagingSessions($objectStorageHoldingExactlyOneImagingSessions);

        self::assertEquals($objectStorageHoldingExactlyOneImagingSessions, $this->subject->_get('imagingSessions'));
    }

    /**
     * @test
     */
    public function addImagingSessionToObjectStorageHoldingImagingSessions(): void
    {
        $imagingSession = new \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession();
        $imagingSessionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $imagingSessionsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($imagingSession));
        $this->subject->_set('imagingSessions', $imagingSessionsObjectStorageMock);

        $this->subject->addImagingSession($imagingSession);
    }

    /**
     * @test
     */
    public function removeImagingSessionFromObjectStorageHoldingImagingSessions(): void
    {
        $imagingSession = new \AcmeCorp\EbAstrophotography\Domain\Model\ImagingSession();
        $imagingSessionsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $imagingSessionsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($imagingSession));
        $this->subject->_set('imagingSessions', $imagingSessionsObjectStorageMock);

        $this->subject->removeImagingSession($imagingSession);
    }

    /**
     * @test
     */
    public function getProcessingRecipesReturnsInitialValueForProcessingRecipe(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getProcessingRecipes()
        );
    }

    /**
     * @test
     */
    public function setProcessingRecipesForObjectStorageContainingProcessingRecipeSetsProcessingRecipes(): void
    {
        $processingRecipe = new \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe();
        $objectStorageHoldingExactlyOneProcessingRecipes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneProcessingRecipes->attach($processingRecipe);
        $this->subject->setProcessingRecipes($objectStorageHoldingExactlyOneProcessingRecipes);

        self::assertEquals($objectStorageHoldingExactlyOneProcessingRecipes, $this->subject->_get('processingRecipes'));
    }

    /**
     * @test
     */
    public function addProcessingRecipeToObjectStorageHoldingProcessingRecipes(): void
    {
        $processingRecipe = new \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe();
        $processingRecipesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $processingRecipesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($processingRecipe));
        $this->subject->_set('processingRecipes', $processingRecipesObjectStorageMock);

        $this->subject->addProcessingRecipe($processingRecipe);
    }

    /**
     * @test
     */
    public function removeProcessingRecipeFromObjectStorageHoldingProcessingRecipes(): void
    {
        $processingRecipe = new \AcmeCorp\EbAstrophotography\Domain\Model\ProcessingRecipe();
        $processingRecipesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $processingRecipesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($processingRecipe));
        $this->subject->_set('processingRecipes', $processingRecipesObjectStorageMock);

        $this->subject->removeProcessingRecipe($processingRecipe);
    }

    /**
     * @test
     */
    public function getAwardsReturnsInitialValueForAward(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getAwards()
        );
    }

    /**
     * @test
     */
    public function setAwardsForObjectStorageContainingAwardSetsAwards(): void
    {
        $award = new \AcmeCorp\EbAstrophotography\Domain\Model\Award();
        $objectStorageHoldingExactlyOneAwards = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneAwards->attach($award);
        $this->subject->setAwards($objectStorageHoldingExactlyOneAwards);

        self::assertEquals($objectStorageHoldingExactlyOneAwards, $this->subject->_get('awards'));
    }

    /**
     * @test
     */
    public function addAwardToObjectStorageHoldingAwards(): void
    {
        $award = new \AcmeCorp\EbAstrophotography\Domain\Model\Award();
        $awardsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $awardsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($award));
        $this->subject->_set('awards', $awardsObjectStorageMock);

        $this->subject->addAward($award);
    }

    /**
     * @test
     */
    public function removeAwardFromObjectStorageHoldingAwards(): void
    {
        $award = new \AcmeCorp\EbAstrophotography\Domain\Model\Award();
        $awardsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $awardsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($award));
        $this->subject->_set('awards', $awardsObjectStorageMock);

        $this->subject->removeAward($award);
    }
}
