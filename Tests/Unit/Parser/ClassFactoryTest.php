<?php

declare(strict_types=1);

namespace EBT\ExtensionBuilder\Tests\Unit\Parser;

use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use PhpParser\BuilderFactory;

class ClassFactoryTest extends BaseUnitTest
{
    /**
     * @var ClassFactory
     */
    protected $classFactory;

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
