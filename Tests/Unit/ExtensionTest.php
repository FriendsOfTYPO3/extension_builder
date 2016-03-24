<?php
namespace EBT\ExtensionBuilder\Tests\Unit;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * TODO testcase doesn't cover whole class
 *
 */
class  ExtensionTest extends \EBT\ExtensionBuilder\Tests\BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Person[]
     */
    protected $persons = array();
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;

    protected function setUp()
    {
        parent::setUp();
        $this->extension = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\EBT\ExtensionBuilder\Domain\Model\Extension::class);
        $this->persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\EBT\ExtensionBuilder\Domain\Model\Person::class);
        $this->persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\EBT\ExtensionBuilder\Domain\Model\Person::class);
        $this->persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\EBT\ExtensionBuilder\Domain\Model\Person::class);
        $this->persons[0]->setName('0');
        $this->persons[1]->setName('1');
        $this->persons[2]->setName('2');
    }

    /**
     * @test
     */
    public function testGetPersonsSetPersons()
    {
        $this->extension->setPersons($this->persons);
        self::assertEquals($this->extension->getPersons(), $this->persons, 'Extensions Persons have been set wrong.');
    }

    /**
     * @test
     */
    public function testAddPerson()
    {
        self::assertEquals($this->extension->getPersons(), array(), 'Extensions Persons are not empty.');
        $this->extension->addPerson($this->persons[0]);
        $this->extension->addPerson($this->persons[1]);
        $this->extension->addPerson($this->persons[2]);
        self::assertEquals(count($this->extension->getPersons()), 3, 'To many Persons in Extension.');
        $persons = $this->extension->getPersons();
        self::assertEquals($persons[0]->getName(), '0', 'Wrong ordering of Persons in Extension.');
        self::assertEquals($persons[1]->getName(), '1', 'Wrong ordering of Persons in Extension.');
        self::assertEquals($persons[2]->getName(), '2', 'Wrong ordering of Persons in Extension.');
    }
}
