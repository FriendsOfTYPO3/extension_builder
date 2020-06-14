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

use EBT\ExtensionBuilder\Domain\Model\Person;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PersonTest extends BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Person
     */
    protected $person;

    protected function setUp()
    {
        parent::setUp();
        $this->person = GeneralUtility::makeInstance(Person::class);
    }

    /**
     * @test
     */
    public function gettersSettersTest(): void
    {
        $name = 'John Doe';
        $role = 'Tester';
        $email = 'e@mail.com';
        $company = 'none';

        $this->person->setName($name);
        $this->person->setRole($role);
        $this->person->setEmail($email);
        $this->person->setCompany($company);

        self::assertEquals($name, $this->person->getName(), 'Persons name was set wrong.');
        self::assertEquals($role, $this->person->getRole(), 'Persons role was set wrong.');
        self::assertEquals($email, $this->person->getEmail(), 'Persons email was set wrong.');
        self::assertEquals($company, $this->person->getCompany(), 'Persons company was set wrong.');
    }
}
