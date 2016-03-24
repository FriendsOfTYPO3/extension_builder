<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author John Doe <mail@typo3.com>
 */
class Child3Test extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child3
     */
    protected $subject = null;

    protected function setUp()
    {
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
    public function getPasswordReturnsInitialValueForString()
    {
        $this->assertSame(
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

        $this->assertAttributeEquals(
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
        $this->assertEquals(
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

        $this->assertAttributeEquals(
            $fileReferenceFixture,
            'imageProperty',
            $this->subject
        );
    }
}
