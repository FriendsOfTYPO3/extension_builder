<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class AstroFilterTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\AstroFilter::class,
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
    public function getFilterTypeReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getFilterType()
        );
    }

    /**
     * @test
     */
    public function setFilterTypeForStringSetsFilterType(): void
    {
        $this->subject->setFilterType('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('filterType'));
    }

    /**
     * @test
     */
    public function getCentralWavelengthReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getCentralWavelength()
        );
    }

    /**
     * @test
     */
    public function setCentralWavelengthForIntSetsCentralWavelength(): void
    {
        $this->subject->setCentralWavelength(12);

        self::assertEquals(12, $this->subject->_get('centralWavelength'));
    }

    /**
     * @test
     */
    public function getBandwidthReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getBandwidth()
        );
    }

    /**
     * @test
     */
    public function setBandwidthForFloatSetsBandwidth(): void
    {
        $this->subject->setBandwidth(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('bandwidth'));
    }

    /**
     * @test
     */
    public function getColorReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getColor()
        );
    }

    /**
     * @test
     */
    public function setColorForStringSetsColor(): void
    {
        $this->subject->setColor('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('color'));
    }

    /**
     * @test
     */
    public function getManufacturerReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getManufacturer()
        );
    }

    /**
     * @test
     */
    public function setManufacturerForStringSetsManufacturer(): void
    {
        $this->subject->setManufacturer('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('manufacturer'));
    }

    /**
     * @test
     */
    public function getDiameterReturnsInitialValueForFloat(): void
    {
        self::assertSame(
            0.0,
            $this->subject->getDiameter()
        );
    }

    /**
     * @test
     */
    public function setDiameterForFloatSetsDiameter(): void
    {
        $this->subject->setDiameter(3.14159265);

        self::assertEquals(3.14159265, $this->subject->_get('diameter'));
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
