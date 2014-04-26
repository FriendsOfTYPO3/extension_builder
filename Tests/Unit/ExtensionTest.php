<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
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
 * TODO testcase doesn't cover whole class
 *
 * @author Christoph Dhen
 *
 */
class  ExtensionTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\Person[]
	 */
	protected $persons = array();

	/**
	 * @var \EBT\ExtensionBuilder\Domain\Model\Extension
	 */
	protected $extension = NULL;

	protected function setUp() {
		$this->extension = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\Extension');
		$this->persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\Person');
		$this->persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\Person');
		$this->persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\Person');
		$this->persons[0]->setName("0");
		$this->persons[1]->setName("1");
		$this->persons[2]->setName("2");
	}

	/**
	 * @test
	 */
	function testGetPersonsSetPersons() {
		$this->extension->setPersons($this->persons);
		$this->assertEquals($this->extension->getPersons(), $this->persons, 'Extensions Persons have been set wrong.');
	}

	/**
	 * @test
	 */
	function testAddPerson() {
		$this->assertEquals($this->extension->getPersons(), array(), 'Extensions Persons are not empty.');
		$this->extension->addPerson($this->persons[0]);
		$this->extension->addPerson($this->persons[1]);
		$this->extension->addPerson($this->persons[2]);
		$this->assertEquals(count($this->extension->getPersons()), 3, 'To many Persons in Extension.');
		$persons = $this->extension->getPersons();
		$this->assertEquals($persons[0]->getName(), "0", 'Wrong ordering of Persons in Extension.');
		$this->assertEquals($persons[1]->getName(), "1", 'Wrong ordering of Persons in Extension.');
		$this->assertEquals($persons[2]->getName(), "2", 'Wrong ordering of Persons in Extension.');

	}
}
