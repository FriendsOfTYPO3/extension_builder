<?php
declare(strict_types=1);

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

use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\Person;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TODO testcase doesn't cover whole class
 */
class ExtensionTest extends BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Person[]
     */
    protected $persons = [];
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension;

    protected function setUp()
    {
        parent::setUp();
        $this->extension = GeneralUtility::makeInstance(Extension::class);
        $this->persons[] = GeneralUtility::makeInstance(Person::class);
        $this->persons[] = GeneralUtility::makeInstance(Person::class);
        $this->persons[] = GeneralUtility::makeInstance(Person::class);
        $this->persons[0]->setName('0');
        $this->persons[1]->setName('1');
        $this->persons[2]->setName('2');
    }

    /**
     * @test
     */
    public function getPersonsSetPersons(): void
    {
        $this->extension->setPersons($this->persons);
        self::assertEquals($this->extension->getPersons(), $this->persons, 'Extensions Persons have been set wrong.');
    }

    /**
     * @test
     */
    public function addPerson(): void
    {
        self::assertEquals([], $this->extension->getPersons(), 'Extensions Persons are not empty.');

        $this->extension->addPerson($this->persons[0]);
        $this->extension->addPerson($this->persons[1]);
        $this->extension->addPerson($this->persons[2]);
        self::assertCount(3, $this->extension->getPersons(), 'To many Persons in Extension.');

        $persons = $this->extension->getPersons();
        self::assertEquals('0', $persons[0]->getName(), 'Wrong ordering of Persons in Extension.');
        self::assertEquals('1', $persons[1]->getName(), 'Wrong ordering of Persons in Extension.');
        self::assertEquals('2', $persons[2]->getName(), 'Wrong ordering of Persons in Extension.');
    }
}
