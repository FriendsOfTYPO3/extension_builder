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

use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripOverwriteStrategyTest extends BaseFunctionalTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileGenerator->_set('roundTripEnabled', true);
    }

    /**
     * @test
     */
    public function keepStrategyPreventsFileOverwrite(): void
    {
        $this->extension->setSettings([
            'overwriteSettings' => [
                'Resources' => [
                    'Private' => [
                        'Language' => [
                            'locallang.xml' => 'keep',
                        ],
                    ],
                ],
            ],
        ]);
        $this->fileGenerator->_set('extension', $this->extension);

        $langDir = $this->extension->getExtensionDir() . 'Resources/Private/Language/';
        mkdir($langDir, 0777, true);

        $targetFile = $langDir . 'locallang.xml';
        $originalContent = '<?xml version="1.0"?><T3locallang><data><languageKey index="default">CUSTOM TRANSLATION</languageKey></data></T3locallang>';
        file_put_contents($targetFile, $originalContent);

        $this->fileGenerator->_call('writeFile', $targetFile, '<T3locallang>GENERATED CONTENT</T3locallang>');

        self::assertStringEqualsFile(
            $targetFile,
            $originalContent,
            'File with keep strategy must not be overwritten'
        );
    }

    /**
     * @test
     */
    public function mergeStrategyAppliesSplitTokenAndPreservesCustomContent(): void
    {
        $this->extension->setSettings([
            'overwriteSettings' => [
                'ext_localconf.php' => 'merge',
            ],
        ]);
        $this->fileGenerator->_set('extension', $this->extension);

        $extDir = $this->extension->getExtensionDir();
        if (!is_dir($extDir)) {
            mkdir($extDir, 0777, true);
        }

        $targetFile = $extDir . 'ext_localconf.php';
        $customSection = "\n// my custom setup\n\$GLOBALS['custom'] = true;";
        $existingContent = "<?php\n// generated\n" . RoundTrip::SPLIT_TOKEN . $customSection;
        file_put_contents($targetFile, $existingContent);

        $newGeneratedContent = "<?php\n// freshly generated\n";
        $this->fileGenerator->_call('writeFile', $targetFile, $newGeneratedContent);

        $writtenContent = file_get_contents($targetFile);
        self::assertStringContainsString(
            '// freshly generated',
            $writtenContent,
            'Generated content must be present after merge'
        );
        self::assertStringContainsString(
            'my custom setup',
            $writtenContent,
            'Custom content after split token must be preserved in merge strategy'
        );
    }

    /**
     * @test
     */
    public function noSettingMeansFileIsOverwritten(): void
    {
        $this->extension->setSettings([
            'overwriteSettings' => [],
        ]);
        $this->fileGenerator->_set('extension', $this->extension);

        $extDir = $this->extension->getExtensionDir();
        if (!is_dir($extDir)) {
            mkdir($extDir, 0777, true);
        }

        $targetFile = $extDir . 'some_file.txt';
        file_put_contents($targetFile, 'old content');

        $this->fileGenerator->_call('writeFile', $targetFile, 'new content');

        self::assertStringEqualsFile($targetFile, 'new content', 'File with no overwrite setting must be overwritten');
    }
}
