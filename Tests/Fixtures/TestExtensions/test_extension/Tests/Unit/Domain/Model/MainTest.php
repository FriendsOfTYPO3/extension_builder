<?php
declare(strict_types=1);

namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author John Doe <mail@typo3.com>
 */
class MainTest extends UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Main|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \FIXTURE\TestExtension\Domain\Model\Main::class,
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
    public function getIdentifierReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getIdentifier()
        );
    }

    /**
     * @test
     */
    public function setIdentifierForStringSetsIdentifier(): void
    {
        $this->subject->setIdentifier('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('identifier'));
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
    public function getMyDateReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getMyDate()
        );
    }

    /**
     * @test
     */
    public function setMyDateForDateTimeSetsMyDate(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setMyDate($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('myDate'));
    }

    /**
     * @test
     */
    public function getMailReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getMail()
        );
    }

    /**
     * @test
     */
    public function setMailForStringSetsMail(): void
    {
        $this->subject->setMail('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('mail'));
    }

    /**
     * @test
     */
    public function getChild1ReturnsInitialValueForChild1(): void
    {
        self::assertEquals(
            null,
            $this->subject->getChild1()
        );
    }

    /**
     * @test
     */
    public function setChild1ForChild1SetsChild1(): void
    {
        $child1Fixture = new \FIXTURE\TestExtension\Domain\Model\Child1();
        $this->subject->setChild1($child1Fixture);

        self::assertEquals($child1Fixture, $this->subject->_get('child1'));
    }

    /**
     * @test
     */
    public function getChildren2ReturnsInitialValueForChild2(): void
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
    public function setChildren2ForObjectStorageContainingChild2SetsChildren2(): void
    {
        $children2 = new \FIXTURE\TestExtension\Domain\Model\Child2();
        $objectStorageHoldingExactlyOneChildren2 = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneChildren2->attach($children2);
        $this->subject->setChildren2($objectStorageHoldingExactlyOneChildren2);

        self::assertEquals($objectStorageHoldingExactlyOneChildren2, $this->subject->_get('children2'));
    }

    /**
     * @test
     */
    public function addChildren2ToObjectStorageHoldingChildren2(): void
    {
        $children2 = new \FIXTURE\TestExtension\Domain\Model\Child2();
        $children2ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children2ObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($children2));
        $this->subject->_set('children2', $children2ObjectStorageMock);

        $this->subject->addChildren2($children2);
    }

    /**
     * @test
     */
    public function removeChildren2FromObjectStorageHoldingChildren2(): void
    {
        $children2 = new \FIXTURE\TestExtension\Domain\Model\Child2();
        $children2ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children2ObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($children2));
        $this->subject->_set('children2', $children2ObjectStorageMock);

        $this->subject->removeChildren2($children2);
    }

    /**
     * @test
     */
    public function getChild3ReturnsInitialValueForChild3(): void
    {
        self::assertEquals(
            null,
            $this->subject->getChild3()
        );
    }

    /**
     * @test
     */
    public function setChild3ForChild3SetsChild3(): void
    {
        $child3Fixture = new \FIXTURE\TestExtension\Domain\Model\Child3();
        $this->subject->setChild3($child3Fixture);

        self::assertEquals($child3Fixture, $this->subject->_get('child3'));
    }

    /**
     * @test
     */
    public function getChildren4ReturnsInitialValueForChild4(): void
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
    public function setChildren4ForObjectStorageContainingChild4SetsChildren4(): void
    {
        $children4 = new \FIXTURE\TestExtension\Domain\Model\Child4();
        $objectStorageHoldingExactlyOneChildren4 = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneChildren4->attach($children4);
        $this->subject->setChildren4($objectStorageHoldingExactlyOneChildren4);

        self::assertEquals($objectStorageHoldingExactlyOneChildren4, $this->subject->_get('children4'));
    }

    /**
     * @test
     */
    public function addChildren4ToObjectStorageHoldingChildren4(): void
    {
        $children4 = new \FIXTURE\TestExtension\Domain\Model\Child4();
        $children4ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children4ObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($children4));
        $this->subject->_set('children4', $children4ObjectStorageMock);

        $this->subject->addChildren4($children4);
    }

    /**
     * @test
     */
    public function removeChildren4FromObjectStorageHoldingChildren4(): void
    {
        $children4 = new \FIXTURE\TestExtension\Domain\Model\Child4();
        $children4ObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $children4ObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($children4));
        $this->subject->_set('children4', $children4ObjectStorageMock);

        $this->subject->removeChildren4($children4);
    }
}
