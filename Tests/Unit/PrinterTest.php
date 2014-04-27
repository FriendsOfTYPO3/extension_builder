<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
use org\bovigo\vfs\vfsStream;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen
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

class PrinterTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * @var string
	 */
	protected $tmpDir = '';

	protected function setUp() {
		parent::setUp();
		$this->fixturesPath = PATH_typo3conf . 'ext/extension_builder/Tests/Fixtures/ClassParser/';
		vfsStream::setup('tmpDir');
		$this->tmpDir = vfsStream::url('tmpDir').'/';
	}

	/**
	 * @test
	 */
	public function printSimplePropertyClass() {
		$this->assertTrue(is_writable($this->tmpDir), 'Directory not writable: Tests/Fixtures/tmp. Can\'t compare rendered files');
		$fileName = 'SimpleProperty.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printClassWithMultipleProperties() {
		$fileName = 'ClassWithMultipleProperties.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printSimpleClassMethodWithManyParameter() {
		$fileName = 'ClassMethodWithManyParameter.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printSimpleClassMethodWithMissingParameterTag() {
		$fileName = 'ClassMethodWithMissingParameterTag.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$reflectedClass = $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		// No way to detect the typeHint with Reflection...

	}

	/**
	 * @test
	 */
	public function printClassWithIncludeStatement() {
		$fileName = 'ClassWithIncludeStatement.php';
		$this->assertTrue(copy($this->fixturesPath.'DummyIncludeFile1.php',$this->tmpDir.'DummyIncludeFile1.php'));
		$this->assertTrue(copy($this->fixturesPath.'DummyIncludeFile2.php',$this->tmpDir.'DummyIncludeFile2.php'));
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printClassWithDefaultValuesInProperties() {
		$fileName = 'BasicClassWithDefaultValuesInProperties.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printClassWithPreStatements() {
		$fileName = 'ClassWithPreStatements.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->assertEquals(TX_PHPPARSER_TEST_FOO,'BAR');
		$this->assertEquals('FOO',TX_PHPPARSER_TEST_BAR);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 *
	 */
	public function printClassWithPostStatements() {
		$fileName = 'ClassWithPostStatements.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->assertEquals(TX_PHPPARSER_TEST_FOO_POST,'BAR');
		$this->assertEquals('FOO',TX_PHPPARSER_TEST_BAR_POST);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 *
	 */
	public function printClassWithPreAndPostStatements() {
		$fileName = 'ClassWithPreAndPostStatements.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->assertEquals(TX_PHPPARSER_TEST_FOO_PRE2,'BAR');
		$this->assertEquals('FOO',TX_PHPPARSER_TEST_BAR_POST2);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}


	/**
	 * @test
	 */
	public function printSimpleNamespacedClass() {
		$fileName = 'SimpleNamespace.php';
		$classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printSimpleNamespacedClassExtendingOtherClass() {
		$fileName = 'SimpleNamespaceExtendingOtherClass.php';
		$classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
	}


	/**
	 * @test
	 */
	public function printSimpleNamespaceWithUseStatement() {
		$fileName = 'SimpleNamespaceWithUseStatement.php';
		$classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printMultipleNamespacedClass() {
		$fileName = 'MultipleNamespaces.php';
		$classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->assertTrue(class_exists('PhpParser\Test\Model\MultipleNamespaces'));
		$this->assertTrue(class_exists('PhpParser\Test\Model2\MultipleNamespaces'));
		$this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
	}


	/**
	 * @test
	 */
	public function printMultipleBracedNamespacedClass() {
		$fileName = 'MultipleBracedNamespaces.php';
		$classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->assertTrue(class_exists('PhpParser\Test\Model\MultipleBracedNamespaces'));
		$this->assertTrue(class_exists('PhpParser\Test\Model2\MultipleBracedNamespaces'));
	}

	/**
	 * @test
	 */
	public function printMultiLineArray() {
		$fileName = 'ClassWithArrayProperty.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printsClassMethodWithMissingParameterTag() {
		$fileName = 'ClassMethodWithMissingParameterTag.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$tags = $classFileObject->getFirstClass()->getMethod('testMethod')->getTagValues('param');
		$this->assertEquals(count($tags), 3);
		$this->assertSame($tags, array('$string', 'array $arr', '\\EBT\\ExtensionBuilder\\Parser\\Utility\\NodeConverter $n'));
	}


	/**
	 * @test
	 */
	public function printsNamespacedClassMethodWitNamespacedParameter() {
		$fileName = 'ClassMethodWithManyParameter.php';
		$classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
		$testMethod = $classFileObject->getFirstClass()->getMethod('testMethod');
		$tags = $testMethod->getTagValues('param');
		$this->assertEquals(count($tags), 2);
		$this->assertSame(
			$tags,
			array (
				0 => '\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject',
				1 => '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TOOOL\Projects\Domain\Model\Calculation> $tests'
			)
		);
		$this->assertSame(
			$testMethod->getParameterByPosition(0)->getTypeHint(),
			'\EBT\ExtensionBuilder\Domain\Model\DomainObject'
		);
		$this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
	}

	/**
	 * @test
	 */
	public function printsClassMethodWithMultilineParameter() {
		$fileName = 'ClassMethodWithMultilineParameter.php';
		$classFileObject = $this->parseAndWrite($fileName);
		$this->assertSame(
			$classFileObject->getFirstClass()->getMethod('testMethod')->getParameterNames(),
			array(
				0 => 'number',
				1 => 'stringParam',
				2 => 'arr',
				3 => 'booleanParam',
				4 => 'float',
				5 => 'n',
			)
		);
		$this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
	}


	/**
	 * @param $fileName
	 * @param string $subFolder
	 * @return \EBT\ExtensionBuilder\Domain\Model\File
	 */
	protected function parseAndWrite($fileName, $subFolder = '') {
		$classFilePath = $this->fixturesPath . $subFolder . $fileName;
		$this->assertTrue(file_exists($classFilePath));

		$fileHandler = fopen($classFilePath, 'r');
		$classFileObject = $this->parserService->parseFile($classFilePath);
		$newClassFilePath = $this->tmpDir . $fileName;
		file_put_contents($newClassFilePath,$this->printerService->renderFileObject($classFileObject, TRUE));
		return $classFileObject;
	}


	/**
	 * includes the generated file and compares the reflection class
	 * with the class object
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\File $classFileObject
	 * @param string $pathToGeneratedFile
	 * @return \ReflectionClass
	 */
	protected function compareClasses($classFileObject, $pathToGeneratedFile) {
		$this->assertTrue(file_exists($pathToGeneratedFile), $pathToGeneratedFile . 'not exists');
		$classObject = $classFileObject->getFirstClass();
		$this->assertTrue($classObject instanceof \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject);
		$className = $classObject->getQualifiedName();
		if(!class_exists($className)) {
			require_once($pathToGeneratedFile);
		}
		$this->assertTrue(class_exists($className), 'Class "' . $className . '" does not exist! Tried ' . $pathToGeneratedFile);
		$reflectedClass = new \ReflectionClass($className);
		$this->assertEquals(count($reflectedClass->getMethods()), count($classObject->getMethods()), 'Method count does not match');
		$this->assertEquals(count($reflectedClass->getProperties()), count($classObject->getProperties()));
		$this->assertEquals(count($reflectedClass->getConstants()), count($classObject->getConstants()));
		if(strlen($classObject->getNamespaceName()) > 0 ) {
			$this->assertEquals( $reflectedClass->getNamespaceName(), $classObject->getNamespaceName());
		}
		return $reflectedClass;
	}

	protected function parseFile($relativeFilePath) {
		return $this->parserService->parseFile($this->fixturesPath . $relativeFilePath);
	}

	/**
	 * @param string $originalFile
	 * @param string $pathToGeneratedFile
	 */
	protected function compareGeneratedCodeWithOriginal($originalFile, $pathToGeneratedFile) {
		$originalLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, file_get_contents($this->fixturesPath . $originalFile), TRUE);
		$generatedLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, file_get_contents($pathToGeneratedFile), TRUE);
		$this->assertEquals(
			$originalLines,
			$generatedLines,
			'File ' . $originalFile . ' was not equal to original file.'
		);

	}
}