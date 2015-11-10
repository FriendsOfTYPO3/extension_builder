<?php

namespace FIXTURE\TestExtension\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 John Doe <mail@typo3.com>, TYPO3
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class \FIXTURE\TestExtension\Domain\Model\Child2.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author John Doe <mail@typo3.com>
 */
class Child2Test extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \FIXTURE\TestExtension\Domain\Model\Child2
     */
    protected $subject = NULL;

    public function setUp()
    {
        $this->subject = new \FIXTURE\TestExtension\Domain\Model\Child2();
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
    public function getDateProperty1ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            NULL,
            $this->subject->getDateProperty1()
        );
    }

    /**
     * @test
     */
    public function setDateProperty1ForDateTimeSetsDateProperty1()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty1($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty1',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateProperty2ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            NULL,
            $this->subject->getDateProperty2()
        );
    }

    /**
     * @test
     */
    public function setDateProperty2ForDateTimeSetsDateProperty2()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty2($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty2',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateProperty3ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            NULL,
            $this->subject->getDateProperty3()
        );
    }

    /**
     * @test
     */
    public function setDateProperty3ForDateTimeSetsDateProperty3()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty3($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty3',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateProperty4ReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            NULL,
            $this->subject->getDateProperty4()
        );
    }

    /**
     * @test
     */
    public function setDateProperty4ForDateTimeSetsDateProperty4()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDateProperty4($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'dateProperty4',
            $this->subject
        );
    }
}
