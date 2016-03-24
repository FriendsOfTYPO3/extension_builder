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

class PersonTest extends \EBT\ExtensionBuilder\Tests\BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Person
     */
    protected $person = null;

    protected function setUp()
    {
        parent::setUp();
        $this->person = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\EBT\ExtensionBuilder\Domain\Model\Person::class);
    }

    /**
     * @test
     */
    public function GettersSettersTest()
    {
        $name = 'John Doe';
        $role = 'Tester';
        $email = 'e@mail.com';
        $company = 'none';

        $this->person->setName($name);
        $this->person->setRole($role);
        $this->person->setEmail($email);
        $this->person->setCompany($company);

        self::assertEquals($this->person->getName(), $name, 'Persons name was set wrong.');
        self::assertEquals($this->person->getRole(), $role, 'Persons role was set wrong.');
        self::assertEquals($this->person->getEmail(), $email, 'Persons email was set wrong.');
        self::assertEquals($this->person->getCompany(), $company, 'Persons company was set wrong.');
    }
}
