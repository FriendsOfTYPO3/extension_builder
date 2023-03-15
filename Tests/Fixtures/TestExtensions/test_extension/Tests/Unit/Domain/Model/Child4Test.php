<?php
declare(strict_types=1);

namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

use FIXTURE\TestExtension\Domain\Model\Child4;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author John Doe <mail@typo3.com>
 */
class Child4Test extends UnitTestCase
{
    /**
     * @var Child4|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->getAccessibleMock(
            Child4::class,
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
    public function getFilePropertyReturnsInitialValueForFileReference(): void
    {
        self::assertEquals(
            null,
            $this->subject->getFileProperty()
        );
    }

    /**
     * @test
     */
    public function setFilePropertyForFileReferenceSetsFileProperty(): void
    {
        $fileReferenceFixture = new FileReference();
        $this->subject->setFileProperty($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('fileProperty'));
    }
}
