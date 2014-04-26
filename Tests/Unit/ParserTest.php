<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
 *
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


class ParserTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * set to true to see an overview of the parsed class objects in the backend
	 *
	 * @var bool
	 */
	protected $debugMode = FALSE;

	protected function setUp() {
		parent::setUp();
		$this->fixturesPath = PATH_typo3conf . 'ext/extension_builder/Tests/Fixtures/ClassParser/';
	}


	/**
	 * @test
	 */
	function parseSimpleProperty() {
		$classFileObject = $this->parseFile('SimpleProperty.php');
		$this->assertEquals(count($classFileObject->getClasses()),1);
		$classObject = $classFileObject->getFirstClass();
		$this->assertEquals(count($classObject->getMethods()), 0);
		$this->assertEquals(count($classObject->getProperties()), 1);
		$this->assertEquals($classObject->getProperty('property')->getValue(),'foo');
		$this->assertEquals($classObject->getProperty('property')->getModifierNames(), array('protected'));
	}

	/**
	 * @test
	 */
	function parseSimplePropertyWithGetterAndSetter() {
		$this->parserService->setTraverser(new \EBT\ExtensionBuilder\Parser\Traverser);
		$classFileObject = $this->parseFile('SimplePropertyWithGetterAndSetter.php');
		$this->assertEquals(count($classFileObject->getFirstClass()->getMethods()), 2);
		$this->assertEquals(count($classFileObject->getFirstClass()->getProperties()), 1);
		$this->assertEquals($classFileObject->getFirstClass()->getProperty('property')->getValue(),'foo');
		$this->assertEquals($classFileObject->getFirstClass()->getProperty('property')->getModifierNames(), array('protected'));
	}

	/**
	 * @test
	 */
	function parseDocComments() {
		$classFileObject = $this->parseFile('SimpleProperty.php');
		$this->assertEquals(count($classFileObject->getClasses()),1);
		$classObject = $classFileObject->getFirstClass();
		$this->assertEquals('This is the class comment', $classObject->getDescription());
		$this->assertEquals('Some simple property', $classObject->getProperty('property')->getDescription());
		$this->assertTrue($classObject->isTaggedWith('author'));
		$this->assertTrue($classObject->getProperty('property')->isTaggedWith('var'));
	}

	/**
	 * @test
	 */
	function parseArrayProperty() {
		$this->parserService->setTraverser(new \EBT\ExtensionBuilder\Parser\Traverser);
		$classFileObject = $this->parseFile('ClassWithArrayProperty.php');
		$this->assertEquals(count($classFileObject->getFirstClass()->getProperties()), 1);
		$this->assertEquals($classFileObject->getFirstClass()->getProperty('arrProperty')->getModifierNames(), array('protected'));
	}

	/**
	 * @test
	 */
	function parseSimpleNonBracedNamespace() {
		$classFileObject = $this->parseFile('Namespaces/SimpleNamespace.php');
		$this->assertEquals('PhpParser\\Test\\Model',$classFileObject->getFirstClass()->getNamespaceName());
	}

	/**
	 * @test
	 */
	function parseClassMethodWithManyParameter() {
		$classFileObject = $this->parseFile('ClassMethodWithManyParameter.php');
		$parameters = $classFileObject->getFirstClass()->getMethod('testMethod')->getParameters();
		$this->assertEquals( 6, count($parameters));
		$this->assertEquals($parameters[3]->getName(), 'booleanParam');
		$this->assertEquals($parameters[3]->getVarType(), 'boolean');
		$this->assertEquals($parameters[5]->getTypeHint(), '\\EBT\\ExtensionBuilder\\Parser\\Utility\\NodeConverter');
	}

	/**
	 * @test
	 */
	function parseClassWithVariousModifiers() {
		$classFileObject = $this->parseFile('ClassWithVariousModifiers.php');
		$classObject = $classFileObject->getFirstClass();
		$this->assertTrue($classObject->isAbstract(), 'Class is not abstract');

		$this->assertTrue($classObject->getProperty('publicProperty')->isPublic(), 'publicProperty is not public');
		$this->assertTrue($classObject->getProperty('protectedProperty')->isProtected(), 'protectedProperty is not protected');
		$this->assertTrue($classObject->getProperty('privateProperty')->isPrivate(), 'privateProperty is not private');
		$this->assertFalse($classObject->getProperty('publicProperty')->isProtected(), 'Public property is is protected');
		$this->assertFalse($classObject->getProperty('privateProperty')->isPublic(), 'Public property is public');
		$this->assertTrue($classObject->getMethod('abstractMethod')->isAbstract(), 'abstract Method is not abstract');
		$this->assertTrue($classObject->getMethod('staticFinalFunction')->isStatic(), 'staticFinalFunction is not static');
		$this->assertTrue($classObject->getMethod('staticFinalFunction')->isFinal(), 'staticFinalFunction is not final');
	}

	/**
	 * @test
	 */
	function parserFindsFunction() {
		$fileObject = $this->parseFile('FunctionsWithoutClasses.php');
		$functions = $fileObject->getFunctions();
		$this->assertEquals(count($functions),2);
		$this->assertTrue(isset($functions['simpleFunction']));
		$this->assertEquals(count($fileObject->getFunction('functionWithParameter')->getParameters()),2);
		$this->assertEquals($fileObject->getFunction('functionWithParameter')->getParameterByPosition(1)->getName(),'bar');
	}

	/**
	 * @test
	 */
	function parserFindsAliasDeclarations() {
		$fileObject = $this->parseFile('Namespaces/SimpleNamespaceWithUseStatement.php');
		$this->assertSame(count($fileObject->getNamespace()->getAliasDeclarations()), 2, 'Alias declaration not found!');
	}

	protected function parseFile($fileName) {
		$classFilePath = $this->fixturesPath . $fileName;
		$classFileObject = $this->parserService->parseFile($classFilePath);
		return $classFileObject;
	}

}
