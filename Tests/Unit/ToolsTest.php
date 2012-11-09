<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen
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


class Tx_ExtensionBuilder_ToolsTest extends Tx_ExtensionBuilder_Tests_BaseTest {

	/**
	 * @test
	 */
	function mergeLocallangXlfWithXlf() {
		$this->markTestSkipped(
		  'The support for merging xml and xfl files is postponed.'
		);
		$existingFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/Tools/existing_locallang.xlf';

		$newXlf = "<?xml version='1.0' encoding='utf-8' standalone='yes' ?>
		<xliff version='1.0'>
			<file source-language='en' datatype='plaintext' original='messages' date='2012-03-47T19:00:47Z' product-name='test123'>
				<header/>
				<body>
					<trans-unit id='tx_test_index1'>
						<source>Label 1</source>
					</trans-unit>
					<trans-unit id='tx_test_index3'>
						<source>Additional label 3</source>
					</trans-unit>
				</body>
			</file>
		</xliff>";
		$result = Tx_ExtensionBuilder_Utility_Tools::mergeLocallangXml($existingFile, $newXlf, 'xlf');
		$expected = array(
			'tx_test_index1' => 'Label 1 modified',
			'tx_test_index3' => 'Additional label 3',
			'tx_test_index2' => 'Label 2',
		);
		$this->assertEquals($result, $expected);
	}

	/**
	 * @test
	 */
	function mergeLocallangXmlWithXml() {
		$this->markTestSkipped(
		  'The support for merging xml files with the new mergeing method is postponed.'
		);
		$existingFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/Tools/existing_locallang.xml';

		$newXml = "<?xml version='1.0' encoding='utf-8' standalone='yes' ?>
		<T3locallang>
			<meta type='array'>
				<type>module</type>
				<description>Language labels for the test 123 extension in the FRONTEND</description>
			</meta>
			<data type='array'>
				<languageKey index='default' type='array'>
		             <label index='tx_test_index1'>Label 1</label>
		             <label index='tx_test_index3'>Additional label 3</label>
				</languageKey>
			</data>
		</T3locallang>";
		$result = Tx_ExtensionBuilder_Utility_Tools::mergeLocallangXml($existingFile, $newXml, 'xml');
		$expected = array(
			'tx_test_index1' => 'Label 1 modified',
			'tx_test_index3' => 'Additional label 3',
			'tx_test_index2' => 'Label 2',
		);
		$this->assertEquals($result, $expected);
	}

	/**
	 * @test
	 */
	function mergeLocallangXmlWithXlf() {
		$this->markTestSkipped(
		  'The support for merging xml files with xlf is postponed.'
		);
		$existingFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Examples/Tools/existing_locallang.xml';

		$newXlf = "<?xml version='1.0' encoding='utf-8' standalone='yes' ?>
			<xliff version='1.0'>
				<file source-language='en' datatype='plaintext' original='messages' date='2012-03-47T19:00:47Z' product-name='test123'>
					<header/>
					<body>
						<trans-unit id='tx_test_index1'>
							<source>Label 1</source>
						</trans-unit>
						<trans-unit id='tx_test_index3'>
							<source>Additional label 3</source>
						</trans-unit>
					</body>
				</file>
			</xliff>";
		$result = Tx_ExtensionBuilder_Utility_Tools::mergeLocallangXml($existingFile, $newXlf, 'xlf');
		$expected = array(
			'tx_test_index1' => 'Label 1 modified',
			'tx_test_index3' => 'Additional label 3',
			'tx_test_index2' => 'Label 2',
		);
		$this->assertEquals($result, $expected);
	}
}