<?php
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


class Tx_ExtensionBuilder_ClassParserTest extends Tx_ExtensionBuilder_Tests_BaseTest {

	/**
	 * set to true to see an overview of the parsed class objects in the backend
	 */
	protected $debugMode = FALSE;

	public function setUp() {
		$this->extensionSchemaBuilder = $this->getMock($this->buildAccessibleProxy('Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder'), array('dummy'));
	}

	/**
	 * Parse a basic class from a file
	 * @test
	 */
	public function ParseBasicClass() {
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ClassParser/BasicClass.php');
		$this->parseClass('Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicClass');
	}

	/**
	 * Parse a basic class from a file
	 * @test
	 */
	public function ParseBasicNameSpacedClass() {
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ClassParser/BasicNameSpacedClass.php');
		$this->parseClass('\\Foo\\Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicNameSpacedClass');
	}


	/**
	 * Parse a complex class from a file
	 * @test
	 */
	public function ParseComplexClass() {
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ClassParser/ComplexClass.php');
		$classObject = $this->parseClass('Tx_ExtensionBuilder_Tests_Examples_ClassParser_ComplexClass');
		$getters = $classObject->getGetters();
		$this->assertEquals(1, count($getters));
		$firstGetter = array_pop($getters);
		$this->assertEquals('getName', $firstGetter->getName());

		$this->assertEquals(
			$classObject->getPrecedingBlock(),
			"\n/**\n * multiline comment test\n * @author Nico de Haen\n *" .
			"\n\tempty line in multiline comment\n	// single comment in multiline" .
			"\n\t *\n	some keywords: \$property  function\n\tstatic\n *" .
			"\n * @test testtag\n */" .
			"\nrequire_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') ." .
			" 'Tests/Examples/ClassParser/BasicClass.php');\n",
			'Preceding block in complex class not properly parsed');

		$defaultOrderingsPropertyValue = $classObject->getProperty('defaultOrderings')->getValue();
		$this->assertEquals(
			$defaultOrderingsPropertyValue,
			"array(\n\t\t'title' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING,\n\t\t'subtitle' =>  Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING,\n\t\t'test' => 'test;',\n\t)",
			'Failed to parse multiline property definition:'
		);
		$params2 = $classObject->getMethod('methodWithVariousParameter')->getParameters();
		$this->assertEquals(
			count($params2),
			4,
			'Wrong parameter count in parsed "methodWithVariousParameter"'
		);
		$this->assertEquals(
			$params2[3]->getName(),
			'param4',
			'Last parameter name was not correctly parsed'
		);
		$this->assertEquals(
			$params2[3]->getDefaultValue(),
			array('test' => array(1, 2, 3))
		);
		$this->assertEquals(
			$classObject->getAppendedBlock(),
			"\n/**\n *  dfg dfg dfg dfg\n */\nrequire_once(\\TYPO3\\CMS\\Core\\Utility\ExtensionManagementUtility:: extPath('extension_builder') . 'Tests/Examples/ClassParser/BasicClass.php');   include_once(\\TYPO3\\CMS\\Core\\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ComplexClass.php'); // test\n\ninclude_once(\\TYPO3\\CMS\\Core\\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ClassParser/ComplexClass.php'); // test\n\n",
			'Appended block was not properly parsed'
		);
	}

	/**
	 * Parse a basic class from a file
	 * @test
	 */
	public function ParseExtendedClass() {
		$this->parseClass('Tx_ExtensionBuilder_Controller_BuilderModuleController');
	}

	/**
	 * Parse a with interfaces
	 * @test
	 */
	public function ParseClassWithInterfaces() {
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ClassParser/ClassWithInterfaces.php');
		$classObject = $this->parseClass('Tx_ExtensionBuilder_Tests_Examples_ClassParser_ClassWithInterfaces');
		$this->assertEquals($classObject->getInterfaceNames(), array('PHPUnit_Framework_IncompleteTest','PHPUnit_Framework_MockObject_Stub','PHPUnit_Framework_SelfDescribing'));
		/**  here we could include some more tests
		$p = $classObject->getMethod('methodWithStrangePrecedingBlock')->getPrecedingBlock();
		$a = $classObject->getAppendedBlock();
		 */
	}

	/**
	 * Parse a complex class from a file
	 * @test
	 */
	public function ParseAnotherComplexClass() {
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ClassParser/AnotherComplexClass.php');
		$classObject = $this->parseClass('Tx_ExtensionBuilder_Tests_Examples_ClassParser_AnotherComplexClass');

		/**  here we could include some more tests
		$p = $classObject->getMethod('methodWithStrangePrecedingBlock')->getPrecedingBlock();
		$a = $classObject->getAppendedBlock();
		 */
	}

	/**
	 * Parse a big class from a file
	 * @test
	 */
	public function Parse_t3lib_div() {
		//require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/BasicClass.php');
		$this->parseClass('\\TYPO3\\CMS\\Core\\Utility\\GeneralUtility');
	}

	/**
	 *
	 * @param $className
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Class
	 */
	protected function parseClass($className) {
		$classParser = new Tx_ExtensionBuilder_Utility_ClassParser();
		$classParser->debugMode = $this->debugMode;
		$classObject = $classParser->parse($className);
		$this->assertTrue($classObject instanceof Tx_ExtensionBuilder_Domain_Model_Class_Class);
		$classReflection = new Tx_ExtensionBuilder_Reflection_ClassReflection($className);
		$this->ParserFindsAllConstants($classObject, $classReflection);
		$this->ParserFindsAllMethods($classObject, $classReflection);
		$this->ParserFindsAllProperties($classObject, $classReflection);
		$this->assertEquals($classReflection->getNamespaceName(), $classObject->getNameSpace());
		return $classObject;
	}

	/**
	 * compares the number of methods found by parsing with those retrieved from the reflection class
	 * @param Tx_ExtensionBuilder_Domain_Model_Class $classObject
	 * @param Tx_ExtensionBuilder_Reflection_ClassReflection $classReflection
	 * @return void
	 */
	public function ParserFindsAllConstants($classObject, $classReflection) {
		$reflectionConstantCount = count($classReflection->getConstants());
		if ($classReflection->getParentClass()) {
			$reflectionConstantCount -= count($classReflection->getParentClass()->getConstants());
		}
		$classObjectConstantCount = count($classObject->getConstants());
		$this->assertEquals($reflectionConstantCount, $classObjectConstantCount, 'Not all Constants were found: ' . $classObject->getName() . serialize($classReflection->getConstants()));
	}

	/**
	 * compares the number of methods found by parsing with those retrieved from the reflection class
	 * @param Tx_ExtensionBuilder_Domain_Model_Class $classObject
	 * @param Tx_ExtensionBuilder_Reflection_ClassReflection $classReflection
	 * @return void
	 */
	public function ParserFindsAllMethods($classObject, $classReflection) {
		$reflectionMethodCount = count($classReflection->getNotInheritedMethods());
		$classObjectMethodCount = count($classObject->getMethods());
		$this->assertEquals($classObjectMethodCount, $reflectionMethodCount, 'Not all Methods were found!');
	}

	/**
	 * compares the number of properties found by parsing with those retrieved from the reflection class
	 * @param Tx_ExtensionBuilder_Domain_Model_Class $classObject
	 * @param Tx_ExtensionBuilder_Reflection_ClassReflection $classReflection
	 * @return void
	 */
	public function ParserFindsAllProperties($classObject, $classReflection) {
		$reflectionPropertyCount = count($classReflection->getNotInheritedProperties());
		$classObjectPropertCount = count($classObject->getProperties());
		$this->assertEquals($classObjectPropertCount, $reflectionPropertyCount, 'Not all Properties were found!');
	}

}

?>
