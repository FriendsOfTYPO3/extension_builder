<?php
namespace EBT\ExtensionBuilder\Tests\Functional;
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

/**
 * Some tests to compare the parsed and the generated source code
 * The are only equal if the source follows the same coding conventions
 * as the printer
 *
 * Class ParseAndPrintTest
 * @package EBT\ExtensionBuilder\Tests\Functional
 */
class ParseAndPrintTest extends \EBT\ExtensionBuilder\Tests\BaseTest {

	protected function setUp() {
		parent::setUp();
		$this->fixturesPath .= 'ClassParser/';
	}

	/**
	 * @test
	 */
	public function parseAndPrintSimplePropertyClass() {
		$fileName = 'SimpleProperty.php';
		$this->parseAndPrint($fileName);
	}

	/**
	 * @test
	 */
	public function parseAndPrintSimpleClassMethodWithManyParameter() {
		$fileName = 'ClassMethodWithManyParameter.php';
		$this->parseAndPrint($fileName);
	}

	/**
	 * @test
	 */
	public function parseAndPrintClassWithIncludeStatement() {
		$fileName = 'ClassWithIncludeStatement.php';
		$this->parseAndPrint($fileName);

	}

	/**
	 * @test
	 */
	public function parseAndPrintSimpleNamespacedClass() {
		$fileName = 'SimpleNamespace.php';
		$this->parseAndPrint($fileName, 'Namespaces/');
	}

	/**
	 * @test
	 */
	public function parseAndPrintSimpleNamespacedClassExtendingOtherClass() {
		$fileName = 'SimpleNamespaceExtendingOtherClass.php';
		$this->parseAndPrint($fileName, 'Namespaces/');
	}


	/**
	 * @test
	 */
	public function parseAndPrintSimpleNamespaceWithUseStatement() {
		$fileName = 'SimpleNamespaceWithUseStatement.php';
		$this->parseAndPrint($fileName, 'Namespaces/');
	}


	/**
	 * @test
	 */
	public function parseAndPrintMultiLineArray() {
		$fileName = 'ClassWithArrayProperty.php';
		$this->parseAndPrint($fileName);
	}


	/**
	 * @test
	 */
	public function parseAndPrintsNamespacedClassMethodWitNamespacedParameter() {
		$fileName = 'ClassMethodWithManyParameter.php';
		$this->parseAndPrint($fileName);
	}

	/**
	 * @test
	 */
	public function parseAndPrintsClassMethodWithMultilineParameter() {
		$fileName = 'ClassMethodWithMultilineParameter.php';
		$this->parseAndPrint($fileName);
	}

	/**
	 * @test
	 */
	public function parseAndPrintsClassMethodWithSwitchStatement() {
		$fileName = 'ClassMethodWithSwitchStatement.php';
		$this->parseAndPrint($fileName);
	}


	/**
	 * @param $fileName
	 * @param string $subFolder
	 * @return \EBT\ExtensionBuilder\Domain\Model\File
	 */
	protected function parseAndPrint($fileName, $subFolder = '') {
		$classFilePath = $this->fixturesPath . $subFolder . $fileName;
		$this->assertTrue(file_exists($classFilePath));
		$fileHandler = fopen($classFilePath, 'r');
		$code = fread($fileHandler, filesize($classFilePath));
		$fileObject = $this->parserService->parseCode($code);
		$printedCode = $this->printerService->renderFileObject($fileObject, TRUE);
		$this->assertEquals(
			explode(PHP_EOL, $code),
			explode(PHP_EOL, $printedCode)
		);

	}

}
