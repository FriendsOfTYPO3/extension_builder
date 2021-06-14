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

namespace EBT\ExtensionBuilder\Tests\Functional\FileGenerator\Property;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\DateProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\DateTimeProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\EmailProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\FileProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\FloatProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\ImageProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\IntegerProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\NativeDateProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\NativeDateTimeProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\NativeTimeProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\PasswordProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\RichTextProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\SelectProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\TextProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\TimeProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\TimeSecProperty;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class GeneratedPropertyTest extends BaseFunctionalTest
{

    /**
     * Write a simple model class for a non aggregate root domain object with one boolean property
     *
     * @test
     */
    public function writeModelClassWithBooleanProperty(): void
    {
        $modelName = 'ModelWithBooleanProperty';
        $propertyName = 'active';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new BooleanProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var bool.*/',
            $classFileContent,
            'var tag for boolean property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$active = false;.*/',
            $classFileContent,
            'boolean property was not generated'
        );

        self::assertRegExp(
            '/.*public function getActive\(\).*/',
            $classFileContent,
            'Getter for boolean property was not generated'
        );
        self::assertRegExp(
            '/.*public function setActive\(bool \$active\).*/',
            $classFileContent,
            'Setter for boolean property was not generated'
        );
        self::assertRegExp(
            '/.*public function isActive\(\).*/',
            $classFileContent,
            'is method for boolean property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one date property
     *
     * @test
     */
    public function writeModelClassWithDateProperty(): void
    {
        $modelName = 'ModelWithDateProperty';
        $propertyName = 'birthday';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new DateProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var \\\\DateTime.*/',
            $classFileContent,
            'var tag for date property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$birthday = null;.*/',
            $classFileContent,
            'string property was not generated'
        );

        self::assertRegExp(
            '/.*public function getBirthday\(\).*/',
            $classFileContent,
            'Getter for string property was not generated'
        );
        self::assertRegExp(
            '/.*public function setBirthday\(\\\\DateTime \$birthday\).*/',
            $classFileContent,
            'Setter for string property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one date time property
     *
     * @test
     */
    public function writeModelClassWithDateTimeProperty(): void
    {
        $modelName = 'ModelWithDateTimeProperty';
        $propertyName = 'birthday';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new DateTimeProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var \\\\DateTime.*/',
            $classFileContent,
            'var tag for date property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$birthday = null;.*/',
            $classFileContent,
            'date property was not generated'
        );

        self::assertRegExp(
            '/.*public function getBirthday\(\).*/',
            $classFileContent,
            'Getter for date property was not generated'
        );
        self::assertRegExp(
            '/.*public function setBirthday\(\\\\DateTime \$birthday\).*/',
            $classFileContent,
            'Setter for date property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one email property
     *
     * @test
     */
    public function writeModelClassWithEmailProperty(): void
    {
        $modelName = 'ModelWithEmailProperty';
        $propertyName = 'email';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new EmailProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string.*/',
            $classFileContent,
            'var tag for email property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$email = \'\';.*/',
            $classFileContent,
            'email property was not generated'
        );

        self::assertRegExp(
            '/.*public function getEmail\(\).*/',
            $classFileContent,
            'Getter for email property was not generated'
        );
        self::assertRegExp(
            '/.*public function setEmail\(string \$email\).*/',
            $classFileContent,
            'Setter for email property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable email property
     *
     * @test
     */
    public function writeModelClassWithNullableEmailProperty(): void
    {
        $modelName = 'ModelWithNullableEmailProperty';
        $propertyName = 'email';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new EmailProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string|null.*/',
            $classFileContent,
            'var tag for email property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$email = null;.*/',
            $classFileContent,
            'email property was not generated'
        );

        self::assertRegExp(
            '/.*public function getEmail\(\).*/',
            $classFileContent,
            'Getter for email property was not generated'
        );
        self::assertRegExp(
            '/.*public function setEmail\(\?string \$email\).*/',
            $classFileContent,
            'Setter for email property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one file property
     *
     * @test
     */
    public function writeModelClassWithFileProperty(): void
    {
        $modelName = 'ModelWithFileProperty';
        $propertyName = 'file';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new FileProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var \\\\TYPO3\\\\CMS\\\\Extbase\\\\Domain\\\\Model\\\\FileReference.*/',
            $classFileContent,
            'var tag for file property was not generated'
        );
        self::assertRegExp(
            '/.*\* \@TYPO3\\\\CMS\\\\Extbase\\\\Annotation\\\\ORM\\\\Cascade\("remove"\).*/',
            $classFileContent,
            'annotation for file property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$file = null;.*/',
            $classFileContent,
            'file property was not generated'
        );

        self::assertRegExp(
            '/.*public function getFile\(\).*/',
            $classFileContent,
            'Getter for file property was not generated'
        );
        self::assertRegExp(
            '/.*public function setFile\(\\\\TYPO3\\\\CMS\\\\Extbase\\\\Domain\\\\Model\\\\FileReference \$file\).*/',
            $classFileContent,
            'Setter for file property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one float property
     *
     * @test
     */
    public function writeModelClassWithFloatProperty(): void
    {
        $modelName = 'ModelWithFloatProperty';
        $propertyName = 'floatingNumber';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new FloatProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var float.*/',
            $classFileContent,
            'var tag for float property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$floatingNumber = 0\.0;.*/',
            $classFileContent,
            'float property was not generated'
        );

        self::assertRegExp(
            '/.*public function getFloatingNumber\(\).*/',
            $classFileContent,
            'Getter for float property was not generated'
        );
        self::assertRegExp(
            '/.*public function setFloatingNumber\(float \$floatingNumber\).*/',
            $classFileContent,
            'Setter for float property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable float property
     *
     * @test
     */
    public function writeModelClassWithNullableFloatProperty(): void
    {
        $modelName = 'ModelWithNullableFloatProperty';
        $propertyName = 'floatingNumber';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new FloatProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var float|null.*/',
            $classFileContent,
            'var tag for float property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$floatingNumber = null;.*/',
            $classFileContent,
            'float property was not generated'
        );

        self::assertRegExp(
            '/.*public function getFloatingNumber\(\).*/',
            $classFileContent,
            'Getter for float property was not generated'
        );
        self::assertRegExp(
            '/.*public function setFloatingNumber\(\?float \$floatingNumber\).*/',
            $classFileContent,
            'Setter for float property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one image property
     *
     * @test
     */
    public function writeModelClassWithImageProperty(): void
    {
        $modelName = 'ModelWithImageProperty';
        $propertyName = 'image';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new ImageProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var \\\\TYPO3\\\\CMS\\\\Extbase\\\\Domain\\\\Model\\\\FileReference.*/',
            $classFileContent,
            'var tag for image property was not generated'
        );
        self::assertRegExp(
            '/.*\* \@TYPO3\\\\CMS\\\\Extbase\\\\Annotation\\\\ORM\\\\Cascade\("remove"\).*/',
            $classFileContent,
            'annotation for image property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$image = null;.*/',
            $classFileContent,
            'image property was not generated'
        );

        self::assertRegExp(
            '/.*public function getImage\(\).*/',
            $classFileContent,
            'Getter for image property was not generated'
        );
        self::assertRegExp(
            '/.*public function setImage\(\\\\TYPO3\\\\CMS\\\\Extbase\\\\Domain\\\\Model\\\\FileReference \$image\).*/',
            $classFileContent,
            'Setter for image property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one int property
     *
     * @test
     */
    public function writeModelClassWithIntegerProperty(): void
    {
        $modelName = 'ModelWithIntegerProperty';
        $propertyName = 'age';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new IntegerProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var int.*/',
            $classFileContent,
            'var tag for int property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$age = 0;.*/',
            $classFileContent,
            'int property was not generated'
        );

        self::assertRegExp(
            '/.*public function getAge\(\).*/',
            $classFileContent,
            'Getter for int property was not generated'
        );
        self::assertRegExp(
            '/.*public function setAge\(int \$age\).*/',
            $classFileContent,
            'Setter for int property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable int property
     *
     * @test
     */
    public function writeModelClassWithNullableIntegerProperty(): void
    {
        $modelName = 'ModelWithNullableIntegerProperty';
        $propertyName = 'age';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new IntegerProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var int|null.*/',
            $classFileContent,
            'var tag for int property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$age = null;.*/',
            $classFileContent,
            'int property was not generated'
        );

        self::assertRegExp(
            '/.*public function getAge\(\).*/',
            $classFileContent,
            'Getter for int property was not generated'
        );
        self::assertRegExp(
            '/.*public function setAge\(\?int \$age\).*/',
            $classFileContent,
            'Setter for int property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one native date property
     *
     * @test
     */
    public function writeModelClassWithNativeDateProperty(): void
    {
        $modelName = 'ModelWithNativeDateProperty';
        $propertyName = 'birthday';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new NativeDateProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var \\\\DateTime.*/',
            $classFileContent,
            'var tag for native date property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$birthday = null;.*/',
            $classFileContent,
            'native date property was not generated'
        );

        self::assertRegExp(
            '/.*public function getBirthday\(\).*/',
            $classFileContent,
            'Getter for native date property was not generated'
        );
        self::assertRegExp(
            '/.*public function setBirthday\(\\\\DateTime \$birthday\).*/',
            $classFileContent,
            'Setter for native date property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one native date time property
     *
     * @test
     */
    public function writeModelClassWithNativeDateTimeProperty(): void
    {
        $modelName = 'ModelWithNativeDateTimeProperty';
        $propertyName = 'birthday';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new NativeDateTimeProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var \\\\DateTime.*/',
            $classFileContent,
            'var tag for native date time property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$birthday = null;.*/',
            $classFileContent,
            'native date time property was not generated'
        );

        self::assertRegExp(
            '/.*public function getBirthday\(\).*/',
            $classFileContent,
            'Getter for native date time property was not generated'
        );
        self::assertRegExp(
            '/.*public function setBirthday\(\\\\DateTime \$birthday\).*/',
            $classFileContent,
            'Setter for native date time property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one native time property
     *
     * @test
     */
    public function writeModelClassWithNativeTimeProperty(): void
    {
        $modelName = 'ModelWithNativeTimeProperty';
        $propertyName = 'time';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new NativeTimeProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var \\\\DateTime.*/',
            $classFileContent,
            'var tag for native time property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$time = null;.*/',
            $classFileContent,
            'native time property was not generated'
        );

        self::assertRegExp(
            '/.*public function getTime\(\).*/',
            $classFileContent,
            'Getter for native time property was not generated'
        );
        self::assertRegExp(
            '/.*public function setTime\(\\\\DateTime \$time\).*/',
            $classFileContent,
            'Setter for native time property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one password property
     *
     * @test
     */
    public function writeModelClassWithPasswordProperty(): void
    {
        $modelName = 'ModelWithPasswordProperty';
        $propertyName = 'password';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new PasswordProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string.*/',
            $classFileContent,
            'var tag for password property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$password = \'\';.*/',
            $classFileContent,
            'password property was not generated'
        );

        self::assertRegExp(
            '/.*public function getPassword\(\).*/',
            $classFileContent,
            'Getter for password property was not generated'
        );
        self::assertRegExp(
            '/.*public function setPassword\(string \$password\).*/',
            $classFileContent,
            'Setter for password property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable password property
     *
     * @test
     */
    public function writeModelClassWithNullablePasswordProperty(): void
    {
        $modelName = 'ModelWithNullablePasswordProperty';
        $propertyName = 'password';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new PasswordProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string|null.*/',
            $classFileContent,
            'var tag for password property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$password = null;.*/',
            $classFileContent,
            'password property was not generated'
        );

        self::assertRegExp(
            '/.*public function getPassword\(\).*/',
            $classFileContent,
            'Getter for password property was not generated'
        );
        self::assertRegExp(
            '/.*public function setPassword\(\?string \$password\).*/',
            $classFileContent,
            'Setter for password property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one rich text property
     *
     * @test
     */
    public function writeModelClassWithRichTextProperty(): void
    {
        $modelName = 'ModelWithRichTextProperty';
        $propertyName = 'content';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new RichTextProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string.*/',
            $classFileContent,
            'var tag for rich text property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$content = \'\';.*/',
            $classFileContent,
            'rich text property was not generated'
        );

        self::assertRegExp(
            '/.*public function getContent\(\).*/',
            $classFileContent,
            'Getter for rich text property was not generated'
        );
        self::assertRegExp(
            '/.*public function setContent\(string \$content\).*/',
            $classFileContent,
            'Setter for rich text property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one select property
     *
     * @test
     */
    public function writeModelClassWithSelectProperty(): void
    {
        $modelName = 'ModelWithSelectProperty';
        $propertyName = 'color';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new SelectProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var int.*/',
            $classFileContent,
            'var tag for select property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$color = 0;.*/',
            $classFileContent,
            'select property was not generated'
        );

        self::assertRegExp(
            '/.*public function getColor\(\).*/',
            $classFileContent,
            'Getter for select property was not generated'
        );
        self::assertRegExp(
            '/.*public function setColor\(int \$color\).*/',
            $classFileContent,
            'Setter for select property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one string property
     *
     * @test
     */
    public function writeModelClassWithStringProperty(): void
    {
        $modelName = 'ModelWithStringProperty';
        $propertyName = 'title';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new StringProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string.*/',
            $classFileContent,
            'var tag for string property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$title = \'\';.*/',
            $classFileContent,
            'string property was not generated'
        );

        self::assertRegExp(
            '/.*public function getTitle\(\).*/',
            $classFileContent,
            'Getter for string property was not generated'
        );
        self::assertRegExp(
            '/.*public function setTitle\(string \$title\).*/',
            $classFileContent,
            'Setter for string property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable string property
     *
     * @test
     */
    public function writeModelClassWithNullableStringProperty(): void
    {
        $modelName = 'ModelWithNullableStringProperty';
        $propertyName = 'title';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new StringProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string|null.*/',
            $classFileContent,
            'var tag for string property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$title = null;.*/',
            $classFileContent,
            'string property was not generated'
        );

        self::assertRegExp(
            '/.*public function getTitle\(\).*/',
            $classFileContent,
            'Getter for string property was not generated'
        );
        self::assertRegExp(
            '/.*public function setTitle\(\?string \$title\).*/',
            $classFileContent,
            'Setter for string property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one text property
     *
     * @test
     */
    public function writeModelClassWithTextProperty(): void
    {
        $modelName = 'ModelWithTextProperty';
        $propertyName = 'text';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new TextProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string.*/',
            $classFileContent,
            'var tag for text property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$text = \'\';.*/',
            $classFileContent,
            'text property was not generated'
        );

        self::assertRegExp(
            '/.*public function getText\(\).*/',
            $classFileContent,
            'Getter for text property was not generated'
        );
        self::assertRegExp(
            '/.*public function setText\(string \$text\).*/',
            $classFileContent,
            'Setter for text property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable text property
     *
     * @test
     */
    public function writeModelClassWithNullableTextProperty(): void
    {
        $modelName = 'ModelWithNullableTextProperty';
        $propertyName = 'text';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new TextProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var string|null.*/',
            $classFileContent,
            'var tag for text property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$text = null;.*/',
            $classFileContent,
            'text property was not generated'
        );

        self::assertRegExp(
            '/.*public function getText\(\).*/',
            $classFileContent,
            'Getter for text property was not generated'
        );
        self::assertRegExp(
            '/.*public function setText\(\?string \$text\).*/',
            $classFileContent,
            'Setter for text property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one time property
     *
     * @test
     */
    public function writeModelClassWithTimeProperty(): void
    {
        $modelName = 'ModelWithTimeProperty';
        $propertyName = 'time';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new TimeProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var int.*/',
            $classFileContent,
            'var tag for time property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$time = 0;.*/',
            $classFileContent,
            'time property was not generated'
        );

        self::assertRegExp(
            '/.*public function getTime\(\).*/',
            $classFileContent,
            'Getter for time property was not generated'
        );
        self::assertRegExp(
            '/.*public function setTime\(int \$time\).*/',
            $classFileContent,
            'Setter for time property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable time property
     *
     * @test
     */
    public function writeModelClassWithNullableTimeProperty(): void
    {
        $modelName = 'ModelWithNullableTimeProperty';
        $propertyName = 'time';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new TimeProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var int|null.*/',
            $classFileContent,
            'var tag for time property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$time = null;.*/',
            $classFileContent,
            'time property was not generated'
        );

        self::assertRegExp(
            '/.*public function getTime\(\).*/',
            $classFileContent,
            'Getter for time property was not generated'
        );
        self::assertRegExp(
            '/.*public function setTime\(\?int \$time\).*/',
            $classFileContent,
            'Setter for time property was not generated'
        );
    }


    /**
     * Write a simple model class for a non aggregate root domain object with one time sec property
     *
     * @test
     */
    public function writeModelClassWithTimeSecProperty(): void
    {
        $modelName = 'ModelWithTimeSecProperty';
        $propertyName = 'timeSec';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new TimeSecProperty($propertyName);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var int.*/',
            $classFileContent,
            'var tag for timeSec property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$timeSec = 0;.*/',
            $classFileContent,
            'timeSec property was not generated'
        );

        self::assertRegExp(
            '/.*public function getTimeSec\(\).*/',
            $classFileContent,
            'Getter for timeSec property was not generated'
        );
        self::assertRegExp(
            '/.*public function setTimeSec\(int \$timeSec\).*/',
            $classFileContent,
            'Setter for timeSec property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one nullable time sec property
     *
     * @test
     */
    public function writeModelClassWithNullableTimeSecProperty(): void
    {
        $modelName = 'ModelWithNullableTimeSecProperty';
        $propertyName = 'timeSec';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new TimeSecProperty($propertyName);
        $property->setNullable(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertRegExp(
            '/.*\* \@var int|null.*/',
            $classFileContent,
            'var tag for timeSec property was not generated'
        );

        self::assertRegExp(
            '/.*protected \\$timeSec = null;.*/',
            $classFileContent,
            'timeSec property was not generated'
        );

        self::assertRegExp(
            '/.*public function getTimeSec\(\).*/',
            $classFileContent,
            'Getter for timeSec property was not generated'
        );
        self::assertRegExp(
            '/.*public function setTimeSec\(\?int \$timeSec\).*/',
            $classFileContent,
            'Setter for timeSec property was not generated'
        );
    }
}
