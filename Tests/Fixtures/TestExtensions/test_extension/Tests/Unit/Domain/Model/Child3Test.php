<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author John Doe <mail@typo3.com>
 */
class Child3Test extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child3
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Child3();
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
    public function getPasswordReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPassword()
        );
    }

    /**
     * @test
     */
    public function setPasswordForStringSetsPassword()
    {
        $this->subject->setPassword('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'password',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getImagePropertyReturnsInitialValueForFileReference()
    {
        self::assertEquals(
            null,
            $this->subject->getImageProperty()
        );
    }

    /**
     * @test
     */
    public function setImagePropertyForFileReferenceSetsImageProperty()
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setImageProperty($fileReferenceFixture);

        self::assertAttributeEquals(
            $fileReferenceFixture,
            'imageProperty',
            $this->subject
        );
    }
}
