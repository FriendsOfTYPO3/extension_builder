<?php
/***************************************************************
 *  Copyright notice
 *
 * (c) 2012 Rens Admiraal
 * All rights reserved
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
 */
class Tx_ExtensionBuilder_Validation_MethodRegularExpressionTest extends Tx_ExtensionBuilder_Tests_BaseTest {

	/**
	 * @var Tx_ExtensionBuilder_Utility_ClassParser
	 */
	protected $classParser;

	public function setUp() {
		$this->classParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Utility_ClassParser');
	}

	/**
	 * @test
	 */
	public function testSimpleStringValue() {
		$string = "This is a simple string which mentions the word function but should of course not match";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertEmpty($matches);
	}

	/**
	 * @test
	 */
	public function testMethodDeclarationWithoutVisibility() {
		$string = "function foo(\$param) {";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches);
	}

	/**
	 * @test
	 */
	public function testMethodDeclarationWithLeadingWhitespace() {
		$string = "          	function    	foo  	 	( 		 \$param 	) 	 	{";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches);
	}

	/**
	 * @test
	 */
	public function testMethodDeclarationWithMultipleArguments() {
		$string = "protected function foo(\$bar, \$baz) {";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches);
	}

	/**
	 * @test
	 */
	public function testMethodDeclarationWithVisibility() {
		$string = "protected function (\$param) {";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches, "Protected methods are not recognized");

		$string = "private function (\$param) {";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches, "Private functions are not recognized");

		$string = "public function (\$param) {";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches, "Public functions are not recognized");
	}

	/**
	 * @test
	 */
	public function testStaticMethodDeclaration() {
		$string = "static protected function (\$param) {";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches, "Static declaration before visibility is not recognized");

		$string = "protected static function (\$param) {";
		preg_match($this->classParser->methodRegex, $string, $matches);
		$this->assertNotEmpty($matches, "Static declaration after visibility is not recognized");
	}
}

?>