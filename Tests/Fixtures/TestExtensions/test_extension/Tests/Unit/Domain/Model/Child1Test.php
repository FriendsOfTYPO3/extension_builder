<?php
namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/**
 * Test case for class \FIXTURE\TestExtension\Domain\Model\Child1.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author John Doe <mail@typo3.com>
 */
class Child1Test extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child1
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Child1();
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
    public function getFlagReturnsInitialValueForBool()
    {
        $this->assertSame(
            false,
            $this->subject->getFlag()
        );
    }

    /**
     * @test
     */
    public function setFlagForBoolSetsFlag()
    {
        $this->subject->setFlag(true);

        $this->assertAttributeEquals(
            true,
            'flag',
            $this->subject
        );
    }
}
