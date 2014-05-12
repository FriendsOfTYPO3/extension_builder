<?php
namespace EBT\ExtensionBuilder\Tests\Functional;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nico de Haen
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
use org\bovigo\vfs\vfsStream;

/**
 *
 * This tests takes a extension configuration generated with Version 1.0
 * generates a complete Extension and compares it with the
 * one generated with Version 1
 *
 *
 * @author Nico de Haen
 *
 */
class CompatibilityFunctionTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * @var \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder
	 */
	protected $configurationManager = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder
	 */
	protected $extensionSchemaBuilder = NULL;

	/**
	 * @test
	 */
	public function checkRequirements() {
		$this->assertTrue(
			class_exists('org\\bovigo\\vfs\\vfsStream'),
			'Requirements not fulfilled: vfsStream is needed for file operation tests. '
			. 'Please make sure you are using at least phpunit Version 3.5.6');
	}

	/**
	 * This test creates an extension based on a JSON file, generated
	 * with version 1.0 of the ExtensionBuilder and compares all
	 * generated files with the originally created ones
	 * This test should help, to find compatibility breaking changes
	 *
	 * @test
	 */
	function generateExtensionFromVersion3Configuration() {
		$this->configurationManager = $this->getMock(
			$this->buildAccessibleProxy('EBT\ExtensionBuilder\Configuration\ConfigurationManager'),
			array('dummy')
		);
		$this->extensionSchemaBuilder = $this->objectManager->get('EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder');

		$testExtensionDir = $this->fixturesPath . 'TestExtensions/test_extension_v3/';
		$jsonFile = $testExtensionDir . \EBT\ExtensionBuilder\Configuration\ConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;

		if (file_exists($jsonFile)) {
			// compatibility adaptions for configurations from older versions
			$extensionConfigurationJSON = json_decode(file_get_contents($jsonFile), TRUE);
			$extensionConfigurationJSON = $this->configurationManager->fixExtensionBuilderJSON($extensionConfigurationJSON, FALSE);
		} else {
			$extensionConfigurationJSON = array();
			$this->fail('JSON file not found');
		}

		$this->extension = $this->extensionSchemaBuilder->build($extensionConfigurationJSON);
		$this->fileGenerator->setSettings(
			array(
				 'codeTemplateRootPath' => PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/',
				 'extConf' => array(
					 'enableRoundtrip' => '0'
				 )
			)
		);
		$newExtensionDir = vfsStream::url('testDir') . '/';
		$this->extension->setExtensionDir($newExtensionDir . 'test_extension/');

		$this->fileGenerator->build($this->extension);

		$referenceFiles = \TYPO3\CMS\Core\Utility\GeneralUtility::getAllFilesAndFoldersInPath(array(), $testExtensionDir);
		foreach ($referenceFiles as $referenceFile) {
			$createdFile = str_replace($testExtensionDir, $this->extension->getExtensionDir(), $referenceFile);
			if (!in_array(basename($createdFile), array('ExtensionBuilder.json'))) {
				$referenceFileContent = str_replace(
					array('2011-08-11T06:49:00Z', '2011-08-11', '###YEAR###'),
					array(date('Y-m-d\TH:i:00\Z'),date('Y-m-d'), date('Y')),
					file_get_contents($referenceFile)
				);
				$this->assertFileExists($createdFile, 'File ' . $createdFile . ' was not created!');
				// do not compare files that contain a formatted DateTime, as it might have changed between file creation and this comparison
				if(strpos($referenceFile, 'xlf') === FALSE && strpos($referenceFile, 'yaml') === FALSE ) {

					$originalLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, $referenceFileContent, TRUE);
					$generatedLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, file_get_contents($createdFile), TRUE);
					$this->assertEquals(
						$originalLines,
						$generatedLines,
						'File ' . $createdFile . ' was not equal to original file.'
					);
				}
			}
		}
	}
}
