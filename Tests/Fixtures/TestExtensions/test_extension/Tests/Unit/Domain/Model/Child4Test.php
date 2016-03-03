<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case for class \FIXTURE\TestExtension\Domain\Model\Child4.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author John Doe <mail@typo3.com>
 */
class Child4Test extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child4
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Child4();
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
    public function getFilePropertyReturnsInitialValueForFileReference()
    {
        $this->assertEquals(
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

        $this->assertAttributeEquals(
            $fileReferenceFixture,
            'fileProperty',
            $this->subject
        );
    }
}
