<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Tests\Functional\Service;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Regenerates the astrophotography demo extension from its stored ExtensionBuilder.json
 * and compares every generated file against the fixture snapshot.
 *
 * Any change to code templates, TCA generators, or model generators that alters output
 * will cause this test to fail with a unified diff showing exactly what changed.
 */
class AstrophotographyCompatibilityTest extends BaseFunctionalTest
{
    /**
     * @throws ExtensionException
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function generateExtensionFromAstrophotographyConfiguration(): void
    {
        $configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);
        $extensionSchemaBuilder = GeneralUtility::makeInstance(ExtensionSchemaBuilder::class);

        $fixtureDir = $this->fixturesPath . 'TestExtensions/eb_astrophotography/';
        $jsonFile = $fixtureDir . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;

        if (file_exists($jsonFile)) {
            $extensionConfigurationJSON = json_decode(file_get_contents($jsonFile), true);
            $extensionConfigurationJSON['storagePath'] = $this->fixturesPath . 'TestExtensions/';
            $extensionConfigurationJSON = $configurationManager->fixExtensionBuilderJSON(
                $extensionConfigurationJSON
            );
        } else {
            self::fail('ExtensionBuilder.json not found at ' . $jsonFile);
        }

        $this->extension = $extensionSchemaBuilder->build($extensionConfigurationJSON);
        $this->fileGenerator->setSettings([
            'codeTemplateRootPaths.' => [
                Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/',
            ],
            'codeTemplatePartialPaths.' => [
                Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/Partials',
            ],
            'extConf' => [
                'enableRoundtrip' => '0',
            ],
        ]);

        $this->extension->setExtensionDir('eb_astrophotography/');
        $this->fileGenerator->build($this->extension);

        $diffOutput = new UnifiedDiffOutputBuilder('', true);
        $differ = new Differ($diffOutput);

        $finder = (new Finder())
            ->files()
            ->in($fixtureDir)
            ->name('/\.(php|sql|html|typoscript)$/');

        foreach ($finder as $file) {
            $referenceFile = $file->getPathname();
            $createdFile = str_replace($fixtureDir, $this->extension->getExtensionDir(), $referenceFile);

            if (basename($createdFile) === 'ExtensionBuilder.json') {
                continue;
            }

            self::assertFileExists($createdFile, 'File ' . $createdFile . ' was not created!');

            $referenceContent = $this->normalizeDates(file_get_contents($referenceFile));
            $generatedContent = $this->normalizeDates(file_get_contents($createdFile));

            // Remove trailing whitespace (clears space-only lines too).
            $referenceContent = preg_replace('#\s+$#m', '', $referenceContent);
            $generatedContent = preg_replace('#\s+$#m', '', $generatedContent);
            // Normalize multiple line-breaks.
            $referenceContent = preg_replace('#(\r\n|\n)+#ms', "\n", $referenceContent);
            $generatedContent = preg_replace('#(\r\n|\n)+#ms', "\n", $generatedContent);
            // Normalize leading whitespace.
            $referenceContent = preg_replace('#^\s+#m', "\t", $referenceContent);
            $generatedContent = preg_replace('#^\s+#m', "\t", $generatedContent);

            $differences = $differ->diff($referenceContent, $generatedContent);
            self::assertEmpty(
                trim($differences),
                sprintf(
                    "Differences detected:\n\n--- %s (reference)\n+++ %s (generated)\n%s",
                    $referenceFile,
                    $createdFile,
                    $differences
                )
            );
        }
    }

    /**
     * Replaces date/timestamp strings with stable placeholders so comparisons
     * do not fail when the test runs on a different day than when the fixture was generated.
     * Applied symmetrically to both reference and generated content.
     */
    private function normalizeDates(string $content): string
    {
        // ISO datetime: e.g. 2026-04-06T12:34:00Z
        $content = (string)preg_replace('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/', '###TIMESTAMP###', $content);
        // ISO date: e.g. 2026-04-06
        $content = (string)preg_replace('/\d{4}-\d{2}-\d{2}/', '###DATE###', $content);
        return $content;
    }
}
