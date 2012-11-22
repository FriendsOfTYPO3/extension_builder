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


/**
 *
 * @author Nico de Haen
 *
 */
class Tx_ExtensionBuilder_CodeGeneratorUnitTest extends Tx_ExtensionBuilder_Tests_BaseTest {

	function setUp() {
		parent::setUp();
	}


	/**
	 * Generate the appropriate code for a simple model class
	 * for a non aggregate root domain object with one boolean property
	 *
	 * @test
	 */
	function generateCodeForModelClassWithBooleanProperty() {
		$modelName = 'ModelCgt1';
		$propertyName = 'blue';
		$domainObject = $this->buildDomainObject($modelName);
		$property = new Tx_ExtensionBuilder_Domain_Model_DomainObject_BooleanProperty();
		$property->setName($propertyName);
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);
		$classFileContent = $this->codeGenerator->generateDomainObjectCode($domainObject, FALSE);
		$this->assertRegExp("/.*class ModelCgt1.*/", $classFileContent, 'Class declaration was not generated');
		$this->assertRegExp('/.*protected \\$blue.*/', $classFileContent, 'boolean property was not generated');
		$this->assertRegExp('/.*\* \@var boolean.*/', $classFileContent, 'var tag for boolean property was not generated');
		$this->assertRegExp('/.*\* \@validate NotEmpty.*/', $classFileContent, 'validate tag for required property was not generated');
		$this->assertRegExp('/.*public function getBlue\(\).*/', $classFileContent, 'Getter for boolean property was not generated');
		$this->assertRegExp('/.*public function setBlue\(\$blue\).*/', $classFileContent, 'Setter for boolean property was not generated');
		$this->assertRegExp('/.*public function isBlue\(\).*/', $classFileContent, 'is method for boolean property was not generated');
	}

	/**
	 * @test
	 */
	function parseAndWriteClass() {
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/ClassParser/BasicAliasClass.php');
		$className = '\\Bar\\Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicAliasClass';
		$classParser = new Tx_ExtensionBuilder_Utility_ClassParser();
		$classObject = $classParser->parse($className);
		$classObject->setNameSpace('Foo');
		$classObject->setName('Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicAliasClass');
		$tmpDir = 'tmp/';
		\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($this->extension->getExtensionDir(),$tmpDir);
		$absTmpDir = $this->extension->getExtensionDir().$tmpDir;
		$this->assertTrue(is_dir($absTmpDir),'Directory ' . $absTmpDir . ' was not created');

		$targetFile =  $absTmpDir . 'BasicAliasClass.php';
		$classFileContent = $this->codeGenerator->renderTemplate('Classes/class.phpt', array( 'extension' => $this->extension, 'classObject' => $classObject));
		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($targetFile,$classFileContent);
		$this->assertFileExists($targetFile,'File was not generated: ' . $targetFile);
	}

}

?>
