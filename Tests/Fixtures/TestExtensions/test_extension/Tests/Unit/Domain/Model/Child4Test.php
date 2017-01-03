<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author John Doe <mail@typo3.com>
 */
class Child4Test extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child4
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Child4();
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
    public function getFilePropertyReturnsInitialValueForFileReference()
    {
        self::assertEquals(
            null,
            $this->subject->getFileProperty()
        );
    }

    /**
     * @test
     */
    public function setFilePropertyForFileReferenceSetsFileProperty()
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setFileProperty($fileReferenceFixture);

        self::assertAttributeEquals(
            $fileReferenceFixture,
            'fileProperty',
            $this->subject
        );
    }
}
