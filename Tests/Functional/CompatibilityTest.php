<?php
namespace EBT\ExtensionBuilder\Tests\Functional;

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
class CompatibilityTest extends \EBT\ExtensionBuilder\Tests\BaseFunctionalTest
{
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
     */
    protected $configurationManager = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder
     */
    protected $extensionSchemaBuilder = null;

    /**
     * @test
     */
    public function checkRequirements()
    {
    }

    /**
     * This test creates an extension based on a JSON file, generated
     * with version 1.0 of the ExtensionBuilder and compares all
     * generated files with the originally created ones
     * This test should help, to find compatibility breaking changes
     *
     * @test
     */
    public function generateExtensionFromVersion3Configuration()
    {
        $this->configurationManager = $this->getAccessibleMock(
            'EBT\ExtensionBuilder\Configuration\ConfigurationManager',
            array('dummy')
        );
        $this->extensionSchemaBuilder = $this->objectManager->get('EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder');

        $testExtensionDir = $this->fixturesPath . 'TestExtensions/test_extension/';
        $jsonFile = $testExtensionDir . \EBT\ExtensionBuilder\Configuration\ConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;

        if (file_exists($jsonFile)) {
            // compatibility adaptions for configurations from older versions
            $extensionConfigurationJSON = json_decode(file_get_contents($jsonFile), true);
            $extensionConfigurationJSON = $this->configurationManager->fixExtensionBuilderJSON($extensionConfigurationJSON, false);
        } else {
            $extensionConfigurationJSON = array();
            self::fail('JSON file not found');
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

        $referenceFiles = \TYPO3\CMS\Core\Utility\GeneralUtility::getAllFilesAndFoldersInPath(array(), $testExtensionDir, 'php,sql,txt,html');
        foreach ($referenceFiles as $referenceFile) {
            $createdFile = str_replace($testExtensionDir, $this->extension->getExtensionDir(), $referenceFile);
            if (!in_array(basename($createdFile), array('ExtensionBuilder.json'))) {
                $referenceFileContent = file_get_contents($referenceFile);
                self::assertFileExists($createdFile, 'File ' . $createdFile . ' was not created!');
                // do not compare files that contain a formatted DateTime, as it might have changed between file creation and this comparison
                if (strpos($referenceFile, 'ext_emconf') === false) {
                    $originalLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, $referenceFileContent, true);
                    $generatedLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, file_get_contents($createdFile), true);
                    for ($c = 0; $c < count($originalLines); $c++) {
                        $originalLine = str_replace(
                            array('###YEAR###', '2017'),
                            array(date('Y-m-d'), date('Y'), date('Y')),
                            $originalLines[$c]
                        );
                        self::assertEquals(
                            preg_replace('/\s+/', ' ', $originalLine),
                            preg_replace('/\s+/', ' ', $generatedLines[$c]),
                            'File ' . $createdFile . ' was not equal to original file! Difference in line ' . $c . ':' . $generatedLines[$c] . ' != ' . $originalLines[$c]
                        );
                    }
                    /**
                     * self::assertEquals(
                     * $originalLines,
                     * $generatedLines,
                     * 'File ' . $createdFile . ' was not equal to original file.' . serialize(array_diff($generatedLines, $originalLines))
                     * );*/
                }
            }
        }
    }
}
