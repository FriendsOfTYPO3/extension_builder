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

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use org\bovigo\vfs\vfsStream;
use SebastianBergmann\Diff\Differ;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This tests takes a extension configuration generated with Version 1.0
 * generates a complete Extension and compares it with the
 * one generated with Version 1
 */
class CompatibilityTest extends BaseFunctionalTest
{
    /**
     * @var ExtensionBuilderConfigurationManager
     */
    protected $configurationManager = null;
    /**
     * @var ExtensionSchemaBuilder
     */
    protected $extensionSchemaBuilder = null;

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
        $this->configurationManager = $this->getAccessibleMock(ExtensionBuilderConfigurationManager::class, ['dummy']);
        $this->extensionSchemaBuilder = $this->objectManager->get(ExtensionSchemaBuilder::class);

        $testExtensionDir = $this->fixturesPath . 'TestExtensions/test_extension/';
        $jsonFile = $testExtensionDir . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;

        if (file_exists($jsonFile)) {
            // compatibility adaptions for configurations from older versions
            $extensionConfigurationJSON = json_decode(file_get_contents($jsonFile), true);
            $extensionConfigurationJSON = $this->configurationManager->fixExtensionBuilderJSON($extensionConfigurationJSON);
        } else {
            $extensionConfigurationJSON = [];
            self::fail('JSON file not found');
        }

        $this->extension = $this->extensionSchemaBuilder->build($extensionConfigurationJSON);
        $this->fileGenerator->setSettings(
            [
                'codeTemplateRootPath' => PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/',
                'extConf' => [
                    'enableRoundtrip' => '0'
                ]
            ]
        );
        $newExtensionDir = vfsStream::url('testDir') . '/';
        $this->extension->setExtensionDir($newExtensionDir . 'test_extension/');

        $this->fileGenerator->build($this->extension);

        $differ = new Differ('', false);

        $referenceFiles = GeneralUtility::getAllFilesAndFoldersInPath([], $testExtensionDir, 'php,sql,txt,html');
        foreach ($referenceFiles as $referenceFile) {
            $createdFile = str_replace($testExtensionDir, $this->extension->getExtensionDir(), $referenceFile);
            if (basename($createdFile) === 'ExtensionBuilder.json') {
                continue;
            }
            $referenceFileContent = str_replace(
                ['2019-09-22T01:00:00Z', '2019-09-22', '###YEAR###', '2019'],
                [date('Y-m-d\TH:i:00\Z'), date('Y-m-d'), date('Y'), date('Y')],
                file_get_contents($referenceFile)
            );
            self::assertFileExists($createdFile, 'File ' . $createdFile . ' was not created!');

            $generatedFileContent = str_replace(
                ['2019-01-01T01:00:00Z', '2019-01-01', '###YEAR###', '2019'],
                [date('Y-m-d\TH:i:00\Z'), date('Y-m-d'), date('Y'), date('Y')],
                file_get_contents($createdFile)
            );

            // remove spaces at end of line (also clears space-only lines)
            $referenceFileContent = preg_replace('#\s+$#m', '', $referenceFileContent);
            $generatedFileContent = preg_replace('#\s+$#m', '', $generatedFileContent);
            // normalize multiple line-breaks
            $referenceFileContent = preg_replace('#(\r\n|\n)+#ms', "\n", $referenceFileContent);
            $generatedFileContent = preg_replace('#(\r\n|\n)+#ms', "\n", $generatedFileContent);
            $referenceFileContent = preg_replace('#^\s+#m', "\t", $referenceFileContent);
            $generatedFileContent = preg_replace('#^\s+#m', "\t", $generatedFileContent);
            $differences = $differ->diff($referenceFileContent, $generatedFileContent);

            self::assertEmpty(
                trim($differences),
                sprintf(
                    "Differences detected:\n\n--- %s (reference)\n+++ %s (generated)\n%s",
                    $createdFile,
                    $createdFile,
                    $differences
                )
            );
        }
    }
}
