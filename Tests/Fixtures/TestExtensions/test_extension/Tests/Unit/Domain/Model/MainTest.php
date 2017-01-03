<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author John Doe <mail@typo3.com>
 */
class MainTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Main
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Main();
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
        self::assertSame(
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

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getIdentifierReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getIdentifier()
        );
    }

    /**
     * @test
     */
    public function setIdentifierForStringSetsIdentifier()
    {
        $this->subject->setIdentifier('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'identifier',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'description',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getMyDateReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getMyDate()
        );
    }

    /**
     * @test
     */
    public function setMyDateForDateTimeSetsMyDate()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setMyDate($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'myDate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getChild1ReturnsInitialValueForChild1()
    {
        self::assertEquals(
            null,
            $this->subject->getChild1()
        );
    }

    /**
     * @test
     */
    public function setChild1ForChild1SetsChild1()
    {
        $child1Fixture = new \FIXTURE\TestExtension\Domain\Model\Child1();
        $this->subject->setChild1($child1Fixture);

        self::assertAttributeEquals(
            $child1Fixture,
            'child1',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getChildren2ReturnsInitialValueForChild2()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getChildren2()
        );
    }

    /**
     * @test
     */
    public function setChildren2ForObjectStorageContainingChild2SetsChildren2()
    {
        $children2 = new \FIXTURE\TestExtension\Domain\Model\Child2();
        $objectStorageHoldingExactlyOneChildren2 = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneChildren2->attach($children2);
        $this->subject->setChildren2($objectStorageHoldingExactlyOneChildren2);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneChildren2,
            'children2',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addChildren2ToObjectStorageHoldingChildren2()
    {
        $children2 = new \FIXTURE\TestExtension\Domain\Model\Child2();
        $children2ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children2ObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($children2));
        $this->inject($this->subject, 'children2', $children2ObjectStorageMock);

        $this->subject->addChildren2($children2);
    }

    /**
     * @test
     */
    public function removeChildren2FromObjectStorageHoldingChildren2()
    {
        $children2 = new \FIXTURE\TestExtension\Domain\Model\Child2();
        $children2ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children2ObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($children2));
        $this->inject($this->subject, 'children2', $children2ObjectStorageMock);

        $this->subject->removeChildren2($children2);
    }

    /**
     * @test
     */
    public function getChild3ReturnsInitialValueForChild3()
    {
        self::assertEquals(
            null,
            $this->subject->getChild3()
        );
    }

    /**
     * @test
     */
    public function setChild3ForChild3SetsChild3()
    {
        $child3Fixture = new \FIXTURE\TestExtension\Domain\Model\Child3();
        $this->subject->setChild3($child3Fixture);

        self::assertAttributeEquals(
            $child3Fixture,
            'child3',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getChildren4ReturnsInitialValueForChild4()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getChildren4()
        );
    }

    /**
     * @test
     */
    public function setChildren4ForObjectStorageContainingChild4SetsChildren4()
    {
        $children4 = new \FIXTURE\TestExtension\Domain\Model\Child4();
        $objectStorageHoldingExactlyOneChildren4 = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneChildren4->attach($children4);
        $this->subject->setChildren4($objectStorageHoldingExactlyOneChildren4);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneChildren4,
            'children4',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addChildren4ToObjectStorageHoldingChildren4()
    {
        $children4 = new \FIXTURE\TestExtension\Domain\Model\Child4();
        $children4ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children4ObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($children4));
        $this->inject($this->subject, 'children4', $children4ObjectStorageMock);

        $this->subject->addChildren4($children4);
    }

    /**
     * @test
     */
    public function removeChildren4FromObjectStorageHoldingChildren4()
    {
        $children4 = new \FIXTURE\TestExtension\Domain\Model\Child4();
        $children4ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children4ObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($children4));
        $this->inject($this->subject, 'children4', $children4ObjectStorageMock);

        $this->subject->removeChildren4($children4);
    }
}
