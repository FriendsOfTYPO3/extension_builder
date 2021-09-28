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

namespace EBT\ExtensionBuilder\Tests\Unit\Parser;

use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use PhpParser\BuilderFactory;

class ClassFactoryTest extends BaseUnitTest
{
    protected ClassFactory $classFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classFactory = new ClassFactory();
    }

    /**
     * @test
     */
    public function buildClassObject(): void
    {
        $builderFactory = new BuilderFactory();
        $classBuilder = $builderFactory->class('MyClass')
            ->extend('ParentClass')
            ->implement('MyInterface')
            ->setDocComment('My sweet comment' . PHP_EOL . 'with another line');
        $class = $classBuilder->getNode();

        $classObject = $this->classFactory->buildClassObject($class);

        self::assertSame(['MyInterface'], $classObject->getInterfaceNames());
        self::assertSame('ParentClass', $classObject->getParentClassName());
        self::assertSame('My sweet comment' . PHP_EOL . 'with another line', $classObject->getDescription());
        self::assertSame(['My sweet comment', 'with another line'], $classObject->getDescriptionLines());
    }
}
