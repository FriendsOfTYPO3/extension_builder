<?php

declare(strict_types=1);

namespace AcmeCorp\EbAstrophotography\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class AwardTest extends UnitTestCase
{
    /**
     * @var \AcmeCorp\EbAstrophotography\Domain\Model\Award|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \AcmeCorp\EbAstrophotography\Domain\Model\Award::class,
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
    public function getOrganizationReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getOrganization()
        );
    }

    /**
     * @test
     */
    public function setOrganizationForStringSetsOrganization(): void
    {
        $this->subject->setOrganization('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('organization'));
    }

    /**
     * @test
     */
    public function getAwardDateReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getAwardDate()
        );
    }

    /**
     * @test
     */
    public function setAwardDateForDateTimeSetsAwardDate(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setAwardDate($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('awardDate'));
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
    public function getCertificateFileReturnsInitialValueForFileReference(): void
    {
        self::assertEquals(
            null,
            $this->subject->getCertificateFile()
        );
    }

    /**
     * @test
     */
    public function setCertificateFileForFileReferenceSetsCertificateFile(): void
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setCertificateFile($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('certificateFile'));
    }

    /**
     * @test
     */
    public function getSourceUrlReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getSourceUrl()
        );
    }

    /**
     * @test
     */
    public function setSourceUrlForStringSetsSourceUrl(): void
    {
        $this->subject->setSourceUrl('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('sourceUrl'));
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
