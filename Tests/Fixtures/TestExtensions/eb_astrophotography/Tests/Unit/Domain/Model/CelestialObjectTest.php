<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class CelestialObjectTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\CelestialObject::class,
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
    public function getCatalogIdReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getCatalogId()
        );
    }

    /**
     * @test
     */
    public function setCatalogIdForStringSetsCatalogId(): void
    {
        $this->subject->setCatalogId('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('catalogId'));
    }

    /**
     * @test
     */
    public function getObjectTypeReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getObjectType()
        );
    }

    /**
     * @test
     */
    public function setObjectTypeForStringSetsObjectType(): void
    {
        $this->subject->setObjectType('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('objectType'));
    }

    /**
     * @test
     */
    public function getConstellationReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getConstellation()
        );
    }

    /**
     * @test
     */
    public function setConstellationForStringSetsConstellation(): void
    {
        $this->subject->setConstellation('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('constellation'));
    }

    /**
     * @test
     */
    public function getRightAscensionReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getRightAscension()
        );
    }

    /**
     * @test
     */
    public function setRightAscensionForStringSetsRightAscension(): void
    {
        $this->subject->setRightAscension('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('rightAscension'));
    }

    /**
     * @test
     */
    public function getDeclinationReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getDeclination()
        );
    }

    /**
     * @test
     */
    public function setDeclinationForStringSetsDeclination(): void
    {
        $this->subject->setDeclination('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('declination'));
    }

    /**
     * @test
     */
    public function getMagnitudeReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getMagnitude()
        );
    }

    /**
     * @test
     */
    public function setMagnitudeForFloatSetsMagnitude(): void
    {
        $this->subject->setMagnitude(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('magnitude'));
    }

    /**
     * @test
     */
    public function getDistanceLightyearsReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getDistanceLightyears()
        );
    }

    /**
     * @test
     */
    public function setDistanceLightyearsForFloatSetsDistanceLightyears(): void
    {
        $this->subject->setDistanceLightyears(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('distanceLightyears'));
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
    public function getPreviewImageReturnsInitialValueForFileReference(): void
    {
        self::assertEquals(
            null,
            $this->subject->getPreviewImage()
        );
    }

    /**
     * @test
     */
    public function setPreviewImageForFileReferenceSetsPreviewImage(): void
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setPreviewImage($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('previewImage'));
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
}
