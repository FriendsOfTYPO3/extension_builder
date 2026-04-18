<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Tests\Unit\Domain\Model;

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
     * @var Person[]
     */
    protected array $persons = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = GeneralUtility::makeInstance(Extension::class);
        $this->persons[] = (new Person())->setName('0');
        $this->persons[] = (new Person())->setName('1');
        $this->persons[] = (new Person())->setName('2');
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

    /**
     * @test
     */
    public function generateSiteSetDefaultValueIsFalse(): void
    {
        self::assertFalse($this->extension->getGenerateSiteSet());
    }

    /**
     * @test
     */
    public function getComposerInfoReturnsCorrectConstraintsForV13(): void
    {
        $this->extension->setExtensionKey('test_extension');
        $this->extension->setVendorName('TestVendor');
        $this->extension->setTargetVersion(13.4);

        $composerInfo = $this->extension->getComposerInfo();

        self::assertSame('>=8.2', $composerInfo['require']['php']);
        self::assertSame('^13.4', $composerInfo['require']['typo3/cms-core']);
        self::assertSame('^13.4', $composerInfo['require']['typo3/cms-extbase']);
        self::assertSame('^9.0', $composerInfo['require-dev']['typo3/testing-framework']);
    }

    /**
     * @test
     */
    public function getDependenciesAlwaysContainsExtbase(): void
    {
        $dependencies = $this->extension->getDependencies();

        self::assertArrayHasKey('extbase', $dependencies);
        self::assertSame('13.4.0-13.4.99', $dependencies['extbase']);
    }

    /**
     * @test
     */
    public function getDependenciesDoesNotOverrideExistingExtbaseConstraint(): void
    {
        $this->extension->setDependencies(['extbase' => '12.0.0-12.4.99']);

        $dependencies = $this->extension->getDependencies();

        self::assertSame('12.0.0-12.4.99', $dependencies['extbase']);
    }

    /**
     * @test
     */
    public function getComposerInfoDoesNotContainTerReplace(): void
    {
        $this->extension->setExtensionKey('test_extension');
        $this->extension->setVendorName('TestVendor');
        $this->extension->setTargetVersion(13.4);

        $composerInfo = $this->extension->getComposerInfo();

        self::assertArrayNotHasKey('replace', $composerInfo);
    }

    /**
     * @test
     */
    public function getComposerInfoContainsPhpConstraint(): void
    {
        $this->extension->setExtensionKey('test_extension');
        $this->extension->setVendorName('TestVendor');

        $composerInfo = $this->extension->getComposerInfo();

        self::assertArrayHasKey('php', $composerInfo['require']);
    }

    /**
     * @test
     */
    public function getComposerInfoIncludesAuthorEmailWhenSet(): void
    {
        $this->extension->setExtensionKey('test_extension');
        $this->extension->setVendorName('TestVendor');
        $person = (new Person())->setName('John Doe')->setEmail('john@example.com');
        $this->extension->setPersons([$person]);

        $composerInfo = $this->extension->getComposerInfo();

        self::assertSame('John Doe', $composerInfo['authors'][0]['name']);
        self::assertSame('john@example.com', $composerInfo['authors'][0]['email']);
    }

    /**
     * @test
     */
    public function getComposerInfoOmitsEmailKeyWhenEmpty(): void
    {
        $this->extension->setExtensionKey('test_extension');
        $this->extension->setVendorName('TestVendor');
        $person = (new Person())->setName('Jane Doe');
        $this->extension->setPersons([$person]);

        $composerInfo = $this->extension->getComposerInfo();

        self::assertArrayNotHasKey('email', $composerInfo['authors'][0]);
    }

    /**
     * @test
     */
    public function getComposerInfoIncludesHomepageFromCompanyWhenSet(): void
    {
        $this->extension->setExtensionKey('test_extension');
        $this->extension->setVendorName('TestVendor');
        $person = (new Person())->setName('Jane Doe')->setCompany('https://example.com');
        $this->extension->setPersons([$person]);

        $composerInfo = $this->extension->getComposerInfo();

        self::assertSame('https://example.com', $composerInfo['authors'][0]['homepage']);
    }

    /**
     * @test
     */
    public function getComposerInfoIncludesAllAuthors(): void
    {
        $this->extension->setExtensionKey('test_extension');
        $this->extension->setVendorName('TestVendor');
        $person1 = (new Person())->setName('Alice')->setEmail('alice@example.com');
        $person2 = (new Person())->setName('Bob')->setEmail('bob@example.com');
        $this->extension->setPersons([$person1, $person2]);

        $composerInfo = $this->extension->getComposerInfo();

        self::assertCount(2, $composerInfo['authors']);
        self::assertSame('Alice', $composerInfo['authors'][0]['name']);
        self::assertSame('alice@example.com', $composerInfo['authors'][0]['email']);
        self::assertSame('Bob', $composerInfo['authors'][1]['name']);
        self::assertSame('bob@example.com', $composerInfo['authors'][1]['email']);
    }
}
