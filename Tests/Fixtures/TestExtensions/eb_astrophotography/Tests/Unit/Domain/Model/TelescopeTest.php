<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class TelescopeTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\Telescope|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\Telescope::class,
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
    public function getNameReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName(): void
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('name'));
    }

    /**
     * @test
     */
    public function getBrandReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getBrand()
        );
    }

    /**
     * @test
     */
    public function setBrandForStringSetsBrand(): void
    {
        $this->subject->setBrand('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('brand'));
    }

    /**
     * @test
     */
    public function getTelescopeTypeReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTelescopeType()
        );
    }

    /**
     * @test
     */
    public function setTelescopeTypeForStringSetsTelescopeType(): void
    {
        $this->subject->setTelescopeType('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('telescopeType'));
    }

    /**
     * @test
     */
    public function getFocalLengthReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getFocalLength()
        );
    }

    /**
     * @test
     */
    public function setFocalLengthForIntSetsFocalLength(): void
    {
        $this->subject->setFocalLength(12);

        self::assertEquals(12, $this->subject->_get('focalLength'));
    }

    /**
     * @test
     */
    public function getApertureReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getAperture()
        );
    }

    /**
     * @test
     */
    public function setApertureForIntSetsAperture(): void
    {
        $this->subject->setAperture(12);

        self::assertEquals(12, $this->subject->_get('aperture'));
    }

    /**
     * @test
     */
    public function getFocalRatioReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getFocalRatio()
        );
    }

    /**
     * @test
     */
    public function setFocalRatioForFloatSetsFocalRatio(): void
    {
        $this->subject->setFocalRatio(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('focalRatio'));
    }

    /**
     * @test
     */
    public function getPurchaseDateReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getPurchaseDate()
        );
    }

    /**
     * @test
     */
    public function setPurchaseDateForDateTimeSetsPurchaseDate(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setPurchaseDate($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('purchaseDate'));
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
}
