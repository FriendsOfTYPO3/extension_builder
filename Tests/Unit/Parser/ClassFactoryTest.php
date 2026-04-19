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

use EBT\ExtensionBuilder\Domain\Model\ClassObject\Method;
use EBT\ExtensionBuilder\Domain\Model\ClassObject\MethodParameter;
use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Parser\NodeFactory;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use PhpParser\BuilderFactory;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;

class ClassFactoryTest extends BaseUnitTest
{
    protected ClassFactory $classFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classFactory = new ClassFactory();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function buildParameterNodePreservesPromotedPropertyFlags(): void
    {
        $nodeFactory = new NodeFactory();
        $parameter = new MethodParameter('someRepository');
        $parameter->setTypeHint('SomeRepository');
        // private readonly = MODIFIER_PRIVATE (4) | MODIFIER_READONLY (64)
        $parameter->setFlags(Class_::MODIFIER_PRIVATE | Class_::MODIFIER_READONLY);

        $paramNode = $nodeFactory->buildParameterNode($parameter);

        self::assertSame(
            Class_::MODIFIER_PRIVATE | Class_::MODIFIER_READONLY,
            $paramNode->flags
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function buildClassMethodObjectPreservesIdentifierReturnType(): void
    {
        $factory = new BuilderFactory();
        $methodNode = $factory->method('listAction')
            ->setReturnType('void')
            ->getNode();
        $methodNode->setAttribute('startLine', 1);
        $methodNode->setAttribute('endLine', 3);

        $methodObject = $this->classFactory->buildClassMethodObject($methodNode);

        self::assertSame('void', $methodObject->getReturnType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function buildMethodNodeUsesNameNodeForRelativeNamespacedReturnType(): void
    {
        $nodeFactory = new NodeFactory();
        $method = new Method('testAction');
        $method->setReturnType('Foo\\Bar');
        $method->setModifiers(Class_::MODIFIER_PUBLIC);

        $methodNode = $nodeFactory->buildMethodNode($method);

        self::assertInstanceOf(Name::class, $methodNode->getReturnType());
        self::assertNotInstanceOf(FullyQualified::class, $methodNode->getReturnType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function buildMethodNodeUsesFullyQualifiedNodeForLeadingBackslashReturnType(): void
    {
        $nodeFactory = new NodeFactory();
        $method = new Method('testAction');
        $method->setReturnType('\\Psr\\Http\\Message\\ResponseInterface');
        $method->setModifiers(Class_::MODIFIER_PUBLIC);

        $methodNode = $nodeFactory->buildMethodNode($method);

        self::assertInstanceOf(FullyQualified::class, $methodNode->getReturnType());
    }

    #[\PHPUnit\Framework\Attributes\Test]
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
