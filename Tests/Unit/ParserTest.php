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

class ParserTest extends \EBT\ExtensionBuilder\Tests\BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Service\Parser
     */
    protected $parserService = null;

    protected function setUp()
    {
        parent::setUp();
        $this->fixturesPath = PATH_typo3conf . 'ext/extension_builder/Tests/Fixtures/ClassParser/';
        $this->parserService = new \EBT\ExtensionBuilder\Service\Parser(new \PhpParser\Lexer());
    }

    /**
     * @test
     */
    public function parseSimpleProperty()
    {
        $classFileObject = $this->parseFile('SimpleProperty.php');
        self::assertEquals(count($classFileObject->getClasses()), 1);
        $classObject = $classFileObject->getFirstClass();
        self::assertEquals(count($classObject->getMethods()), 0);
        self::assertEquals(count($classObject->getProperties()), 1);
        self::assertEquals($classObject->getProperty('property')->getValue(), 'foo');
        self::assertEquals($classObject->getProperty('property')->getModifierNames(), array('protected'));
    }

    /**
     * @test
     */
    public function parseSimplePropertyWithGetterAndSetter()
    {
        $this->parserService->setTraverser(new \EBT\ExtensionBuilder\Parser\Traverser);
        $classFileObject = $this->parseFile('SimplePropertyWithGetterAndSetter.php');
        self::assertEquals(count($classFileObject->getFirstClass()->getMethods()), 2);
        self::assertEquals(count($classFileObject->getFirstClass()->getProperties()), 1);
        self::assertEquals($classFileObject->getFirstClass()->getProperty('property')->getValue(), 'foo');
        self::assertEquals($classFileObject->getFirstClass()->getProperty('property')->getModifierNames(), array('protected'));
    }

    /**
     * @test
     */
    public function parseDocComments()
    {
        $classFileObject = $this->parseFile('SimpleProperty.php');
        self::assertEquals(count($classFileObject->getClasses()), 1);
        $classObject = $classFileObject->getFirstClass();
        self::assertEquals('This is the class comment', $classObject->getDescription());
        self::assertEquals('Some simple property', $classObject->getProperty('property')->getDescription());
        self::assertTrue($classObject->isTaggedWith('author'));
        self::assertTrue($classObject->getProperty('property')->isTaggedWith('var'));
    }

    /**
     * @test
     */
    public function parseArrayProperty()
    {
        $this->parserService->setTraverser(new \EBT\ExtensionBuilder\Parser\Traverser);
        $classFileObject = $this->parseFile('ClassWithArrayProperty.php');
        self::assertEquals(count($classFileObject->getFirstClass()->getProperties()), 1);
        self::assertEquals($classFileObject->getFirstClass()->getProperty('arrProperty')->getModifierNames(), array('protected'));
    }

    /**
     * @test
     */
    public function parseSimpleNonBracedNamespace()
    {
        $classFileObject = $this->parseFile('Namespaces/SimpleNamespace.php');
        self::assertEquals('Parser\\Test\\Model', $classFileObject->getFirstClass()->getNamespaceName());
    }

    /**
     * @test
     */
    public function parseClassMethodWithManyParameter()
    {
        $classFileObject = $this->parseFile('ClassMethodWithManyParameter.php');
        $parameters = $classFileObject->getFirstClass()->getMethod('testMethod')->getParameters();
        self::assertEquals(6, count($parameters));
        self::assertEquals($parameters[3]->getName(), 'booleanParam');
        self::assertEquals($parameters[3]->getVarType(), 'boolean');
        self::assertEquals($parameters[5]->getTypeHint(), '\\EBT\\ExtensionBuilder\\Parser\\Utility\\NodeConverter');
    }

    /**
     * @test
     */
    public function parseClassWithVariousModifiers()
    {
        $classFileObject = $this->parseFile('ClassWithVariousModifiers.php');
        $classObject = $classFileObject->getFirstClass();
        self::assertTrue($classObject->isAbstract(), 'Class is not abstract');

        self::assertTrue($classObject->getProperty('publicProperty')->isPublic(), 'publicProperty is not public');
        self::assertTrue($classObject->getProperty('protectedProperty')->isProtected(), 'protectedProperty is not protected');
        self::assertTrue($classObject->getProperty('privateProperty')->isPrivate(), 'privateProperty is not private');
        self::assertFalse($classObject->getProperty('publicProperty')->isProtected(), 'Public property is is protected');
        self::assertFalse($classObject->getProperty('privateProperty')->isPublic(), 'Public property is public');
        self::assertTrue($classObject->getMethod('abstractMethod')->isAbstract(), 'abstract Method is not abstract');
        self::assertTrue($classObject->getMethod('staticFinalFunction')->isStatic(), 'staticFinalFunction is not static');
        self::assertTrue($classObject->getMethod('staticFinalFunction')->isFinal(), 'staticFinalFunction is not final');
    }

    /**
     * @test
     */
    public function parserFindsFunction()
    {
        $fileObject = $this->parseFile('FunctionsWithoutClasses.php');
        $functions = $fileObject->getFunctions();
        self::assertEquals(count($functions), 2);
        self::assertTrue(isset($functions['simpleFunction']));
        self::assertEquals(count($fileObject->getFunction('functionWithParameter')->getParameters()), 2);
        self::assertEquals($fileObject->getFunction('functionWithParameter')->getParameterByPosition(1)->getName(), 'bar');
    }

    /**
     * @test
     */
    public function parserFindsAliasDeclarations()
    {
        $fileObject = $this->parseFile('Namespaces/SimpleNamespaceWithUseStatement.php');
        self::assertSame(count($fileObject->getNamespace()->getAliasDeclarations()), 2, 'Alias declaration not found!');
    }

    protected function parseFile($fileName)
    {
        $classFilePath = $this->fixturesPath . $fileName;
        $classFileObject = $this->parserService->parseFile($classFilePath);
        return $classFileObject;
    }
}
