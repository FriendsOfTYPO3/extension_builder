<?php
declare(strict_types=1);

namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

use FIXTURE\TestExtension\Domain\Model\Child1;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author John Doe <mail@typo3.com>
 */
class Child1Test extends UnitTestCase
{
    /**
     * @var Child1|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->getAccessibleMock(
            Child1::class,
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
    public function getFlagReturnsInitialValueForBool(): void
    {
        self::assertFalse($this->subject->getFlag());
    }

    /**
     * @test
     */
    public function setFlagForBoolSetsFlag(): void
    {
        $this->subject->setFlag(true);

        self::assertEquals(true, $this->subject->_get('flag'));
    }
}
