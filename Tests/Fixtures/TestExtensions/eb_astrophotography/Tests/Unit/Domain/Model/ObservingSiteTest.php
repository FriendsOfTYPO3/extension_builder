<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class ObservingSiteTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\ObservingSite::class,
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
    public function getLatitudeReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getLatitude()
        );
    }

    /**
     * @test
     */
    public function setLatitudeForFloatSetsLatitude(): void
    {
        $this->subject->setLatitude(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('latitude'));
    }

    /**
     * @test
     */
    public function getLongitudeReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getLongitude()
        );
    }

    /**
     * @test
     */
    public function setLongitudeForFloatSetsLongitude(): void
    {
        $this->subject->setLongitude(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('longitude'));
    }

    /**
     * @test
     */
    public function getAltitudeReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getAltitude()
        );
    }

    /**
     * @test
     */
    public function setAltitudeForIntSetsAltitude(): void
    {
        $this->subject->setAltitude(12);

        self::assertEquals(12, $this->subject->_get('altitude'));
    }

    /**
     * @test
     */
    public function getBortleClassReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getBortleClass()
        );
    }

    /**
     * @test
     */
    public function setBortleClassForIntSetsBortleClass(): void
    {
        $this->subject->setBortleClass(12);

        self::assertEquals(12, $this->subject->_get('bortleClass'));
    }

    /**
     * @test
     */
    public function getWebsiteReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getWebsite()
        );
    }

    /**
     * @test
     */
    public function setWebsiteForStringSetsWebsite(): void
    {
        $this->subject->setWebsite('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('website'));
    }

    /**
     * @test
     */
    public function getContactEmailReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getContactEmail()
        );
    }

    /**
     * @test
     */
    public function setContactEmailForStringSetsContactEmail(): void
    {
        $this->subject->setContactEmail('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('contactEmail'));
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
