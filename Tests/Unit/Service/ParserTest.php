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

namespace EBT\ExtensionBuilder\Tests\Unit\Service;

use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Parser\Traverser;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use TYPO3\CMS\Core\Core\Environment;

class ParserTest extends BaseUnitTest
{
    protected ParserService $parserService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturesPath = Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Tests/Fixtures/ClassParser/';
        $this->parserService = new ParserService();
    }

    /**
     * @test
     */
    public function parseSimpleProperty(): void
    {
        $classFileObject = $this->parseFile('SimpleProperty.php');
        self::assertCount(1, $classFileObject->getClasses());

        $classObject = $classFileObject->getFirstClass();
        self::assertCount(0, $classObject->getMethods());
        self::assertCount(1, $classObject->getProperties());
        self::assertEquals('foo', $classObject->getProperty('property')->getValue());
        self::assertEquals(['protected'], $classObject->getProperty('property')->getModifierNames());
    }

    /**
     * @test
     */
    public function parseSimplePropertyWithGetterAndSetter(): void
    {
        $this->parserService->setTraverser(new Traverser());
        $classFileObject = $this->parseFile('SimplePropertyWithGetterAndSetter.php');
        self::assertCount(2, $classFileObject->getFirstClass()->getMethods());
        self::assertCount(1, $classFileObject->getFirstClass()->getProperties());
        self::assertEquals('foo', $classFileObject->getFirstClass()->getProperty('property')->getValue());
        self::assertEquals(
            ['protected'],
            $classFileObject->getFirstClass()->getProperty('property')->getModifierNames()
        );
    }

    /**
     * @test
     */
    public function parseDocComments(): void
    {
        $classFileObject = $this->parseFile('SimpleProperty.php');
        self::assertCount(1, $classFileObject->getClasses());

        $classObject = $classFileObject->getFirstClass();
        self::assertEquals('This is the class comment', $classObject->getDescription());
        self::assertEquals('Some simple property', $classObject->getProperty('property')->getDescription());
        self::assertTrue($classObject->isTaggedWith('author'));
        self::assertTrue($classObject->getProperty('property')->isTaggedWith('var'));
    }

    /**
     * @test
     */
    public function parseArrayProperty(): void
    {
        $this->parserService->setTraverser(new Traverser());
        $classFileObject = $this->parseFile('ClassWithArrayProperty.php');
        self::assertCount(1, $classFileObject->getFirstClass()->getProperties());
        self::assertEquals(
            ['protected'],
            $classFileObject->getFirstClass()->getProperty('arrProperty')->getModifierNames()
        );
    }

    /**
     * @test
     */
    public function parseSimpleNonBracedNamespace(): void
    {
        $classFileObject = $this->parseFile('Namespaces/SimpleNamespace.php');
        self::assertEquals('Parser\\Test\\Model', $classFileObject->getFirstClass()->getNamespaceName());
    }

    /**
     * @test
     */
    public function parseClassMethodWithManyParameter(): void
    {
        $classFileObject = $this->parseFile('ClassMethodWithManyParameter.php');
        $parameters = $classFileObject->getFirstClass()->getMethod('testMethod')->getParameters();
        self::assertCount(6, $parameters);
        self::assertEquals('booleanParam', $parameters[3]->getName());
        self::assertEquals('boolean', $parameters[3]->getVarType());
        self::assertEquals('\\' . NodeConverter::class, $parameters[5]->getTypeHint());
    }

    /**
     * @test
     */
    public function parseClassWithVariousModifiers(): void
    {
        $classFileObject = $this->parseFile('ClassWithVariousModifiers.php');
        $classObject = $classFileObject->getFirstClass();
        self::assertTrue($classObject->isAbstract(), 'Class is not abstract');

        self::assertTrue(
            $classObject->getProperty('publicProperty')->isPublic(),
            'publicProperty is not public'
        );
        self::assertTrue(
            $classObject->getProperty('protectedProperty')->isProtected(),
            'protectedProperty is not protected'
        );
        self::assertTrue(
            $classObject->getProperty('privateProperty')->isPrivate(),
            'privateProperty is not private'
        );
        self::assertFalse(
            $classObject->getProperty('publicProperty')->isProtected(),
            'Public property is is protected'
        );
        self::assertFalse(
            $classObject->getProperty('privateProperty')->isPublic(),
            'Public property is public'
        );
        self::assertTrue(
            $classObject->getMethod('abstractMethod')->isAbstract(),
            'abstract Method is not abstract'
        );
        self::assertTrue(
            $classObject->getMethod('staticFinalFunction')->isStatic(),
            'staticFinalFunction is not static'
        );
        self::assertTrue(
            $classObject->getMethod('staticFinalFunction')->isFinal(),
            'staticFinalFunction is not final'
        );
    }

    /**
     * @test
     */
    public function parserFindsFunction(): void
    {
        $fileObject = $this->parseFile('FunctionsWithoutClasses.php');
        $functions = $fileObject->getFunctions();
        self::assertCount(2, $functions);
        self::assertTrue(isset($functions['simpleFunction']));
        self::assertCount(2, $fileObject->getFunction('functionWithParameter')->getParameters());
        self::assertEquals(
            'bar',
            $fileObject->getFunction('functionWithParameter')->getParameterByPosition(1)->getName()
        );
    }

    /**
     * @test
     */
    public function parserFindsAliasDeclarations(): void
    {
        $fileObject = $this->parseFile('Namespaces/SimpleNamespaceWithUseStatement.php');
        self::assertSame(count($fileObject->getNamespace()->getAliasDeclarations()), 2, 'Alias declaration not found!');
    }

    protected function parseFile(string $fileName): File
    {
        return $this->parserService->parseFile($this->fixturesPath . $fileName);
    }
}
