<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case for class \FIXTURE\TestExtension\Domain\Model\Child3.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author John Doe <mail@typo3.com>
 */
class Child3Test extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child3
     */
    protected $subject = NULL;

    public function setUp()
    {
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Child3();
    }

    public function tearDown()
    {
        unset($this->subject);
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
            NULL,
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
