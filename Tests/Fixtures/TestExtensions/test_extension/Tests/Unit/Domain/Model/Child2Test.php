<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author John Doe <mail@typo3.com>
 */
class Child2Test extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child2
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Child2();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateProperty1ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            null,
            $this->subject->getDateProperty1()
        );
    }

    /**
     * @test
     */
    public function setDateProperty1ForDateTimeSetsDateProperty1()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty1($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty1',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateProperty2ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            null,
            $this->subject->getDateProperty2()
        );
    }

    /**
     * @test
     */
    public function setDateProperty2ForDateTimeSetsDateProperty2()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty2($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty2',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateProperty3ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            null,
            $this->subject->getDateProperty3()
        );
    }

    /**
     * @test
     */
    public function setDateProperty3ForDateTimeSetsDateProperty3()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty3($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty3',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateProperty4ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            null,
            $this->subject->getDateProperty4()
        );
    }

    /**
     * @test
     */
    public function setDateProperty4ForDateTimeSetsDateProperty4()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty4($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty4',
            $this->subject
        );
    }
}
