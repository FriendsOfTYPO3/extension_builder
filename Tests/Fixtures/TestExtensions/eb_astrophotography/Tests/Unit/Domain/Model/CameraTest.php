<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class CameraTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\Camera|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\Camera::class,
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
    public function getSensorTypeReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getSensorType()
        );
    }

    /**
     * @test
     */
    public function setSensorTypeForIntSetsSensorType(): void
    {
        $this->subject->setSensorType(12);

        self::assertEquals(12, $this->subject->_get('sensorType'));
    }

    /**
     * @test
     */
    public function getSensorWidthReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getSensorWidth()
        );
    }

    /**
     * @test
     */
    public function setSensorWidthForFloatSetsSensorWidth(): void
    {
        $this->subject->setSensorWidth(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('sensorWidth'));
    }

    /**
     * @test
     */
    public function getSensorHeightReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getSensorHeight()
        );
    }

    /**
     * @test
     */
    public function setSensorHeightForFloatSetsSensorHeight(): void
    {
        $this->subject->setSensorHeight(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('sensorHeight'));
    }

    /**
     * @test
     */
    public function getPixelSizeReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getPixelSize()
        );
    }

    /**
     * @test
     */
    public function setPixelSizeForFloatSetsPixelSize(): void
    {
        $this->subject->setPixelSize(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('pixelSize'));
    }

    /**
     * @test
     */
    public function getMegapixelsReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getMegapixels()
        );
    }

    /**
     * @test
     */
    public function setMegapixelsForFloatSetsMegapixels(): void
    {
        $this->subject->setMegapixels(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('megapixels'));
    }

    /**
     * @test
     */
    public function getCooledReturnsInitialValueForBool(): void
    {
        self::assertFalse($this->subject->getCooled());
    }

    /**
     * @test
     */
    public function setCooledForBoolSetsCooled(): void
    {
        $this->subject->setCooled(true);

        self::assertEquals(true, $this->subject->_get('cooled'));
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
}
