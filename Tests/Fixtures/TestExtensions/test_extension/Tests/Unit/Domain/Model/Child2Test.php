<?php
declare(strict_types=1);

namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

use FIXTURE\TestExtension\Domain\Model\Child2;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author John Doe <mail@typo3.com>
 */
class Child2Test extends UnitTestCase
{
    /**
     * @var Child2|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->getAccessibleMock(
            Child2::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
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
    public function getDateProperty1ReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getDateProperty1()
        );
    }

    /**
     * @test
     */
    public function setDateProperty1ForDateTimeSetsDateProperty1(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty1($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('dateProperty1'));
    }

    /**
     * @test
     */
    public function getDateProperty2ReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getDateProperty2()
        );
    }

    /**
     * @test
     */
    public function setDateProperty2ForDateTimeSetsDateProperty2(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty2($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('dateProperty2'));
    }

    /**
     * @test
     */
    public function getDateProperty3ReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getDateProperty3()
        );
    }

    /**
     * @test
     */
    public function setDateProperty3ForDateTimeSetsDateProperty3(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty3($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('dateProperty3'));
    }

    /**
     * @test
     */
    public function getDateProperty4ReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getDateProperty4()
        );
    }

    /**
     * @test
     */
    public function setDateProperty4ForDateTimeSetsDateProperty4(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty4($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('dateProperty4'));
    }
}
