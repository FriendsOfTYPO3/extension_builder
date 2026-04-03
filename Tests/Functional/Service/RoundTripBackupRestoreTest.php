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
use TYPO3\CMS\Core\Core\Environment;

class RoundTripBackupRestoreTest extends BaseFunctionalTest
{
    /** Relative path — resolved against Environment::getProjectPath() by backupExtension() */
    private string $backupRelDir = '';
    private string $backupAbsDir = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupRelDir = 'var/tx_extensionbuilder/test_backups_' . uniqid('', true);
        $this->backupAbsDir = Environment::getProjectPath() . '/' . $this->backupRelDir;
        mkdir($this->backupAbsDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->backupAbsDir)) {
            \TYPO3\CMS\Core\Utility\GeneralUtility::rmdir($this->backupAbsDir, true);
        }
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listBackupsReturnsEmptyArrayWhenNoBackupsExist(): void
    {
        $result = RoundTrip::listBackups('my_extension', $this->backupRelDir);
        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function listBackupsReturnsBackupEntries(): void
    {
        $backup1 = $this->backupAbsDir . '/my_extension/2026-04-01-1000000';
        $backup2 = $this->backupAbsDir . '/my_extension/2026-04-02-2000000';
        mkdir($backup1, 0777, true);
        mkdir($backup2, 0777, true);
        file_put_contents($backup1 . '/ext_emconf.php', '<?php');
        file_put_contents($backup2 . '/ext_emconf.php', '<?php');
        file_put_contents($backup2 . '/composer.json', '{}');

        $result = RoundTrip::listBackups('my_extension', $this->backupRelDir);

        self::assertCount(2, $result);
        // Newest first (SCANDIR_SORT_DESCENDING)
        self::assertEquals('2026-04-02-2000000', $result[0]['directory']);
        self::assertEquals(2, $result[0]['fileCount']);
        self::assertEquals('2026-04-01-1000000', $result[1]['directory']);
        self::assertEquals(1, $result[1]['fileCount']);
    }

    /**
     * @test
     */
    public function listBackupsFormatsDatesFromTimestamp(): void
    {
        $timestamp = mktime(14, 30, 0, 4, 1, 2026);
        $dirName = '2026-04-01-' . $timestamp;
        mkdir($this->backupAbsDir . '/my_extension/' . $dirName, 0777, true);

        $result = RoundTrip::listBackups('my_extension', $this->backupRelDir);

        self::assertCount(1, $result);
        self::assertEquals(date('Y-m-d H:i:s', $timestamp), $result[0]['label']);
    }

    /**
     * @test
     */
    public function restoreBackupCopiesFilesFromBackup(): void
    {
        $extensionDir = $this->extension->getExtensionDir();
        mkdir($extensionDir, 0777, true);
        file_put_contents($extensionDir . 'ext_emconf.php', '<?php // current');

        $backupEntry = '2026-04-01-1000000';
        $backupSrc = $this->backupAbsDir . '/dummy/' . $backupEntry;
        mkdir($backupSrc, 0777, true);
        file_put_contents($backupSrc . '/ext_emconf.php', '<?php // from backup');

        RoundTrip::restoreBackup($this->extension, $backupEntry, $this->backupRelDir);

        $restoredContent = file_get_contents($extensionDir . 'ext_emconf.php');
        self::assertStringContainsString('from backup', $restoredContent);
    }

    /**
     * @test
     */
    public function restoreBackupCreatesNestedBackupBeforeRestoring(): void
    {
        $extensionDir = $this->extension->getExtensionDir();
        mkdir($extensionDir, 0777, true);
        file_put_contents($extensionDir . 'ext_emconf.php', '<?php // current');

        $backupEntry = '2026-04-01-1000000';
        $backupSrc = $this->backupAbsDir . '/dummy/' . $backupEntry;
        mkdir($backupSrc, 0777, true);
        file_put_contents($backupSrc . '/ext_emconf.php', '<?php // from backup');

        RoundTrip::restoreBackup($this->extension, $backupEntry, $this->backupRelDir);

        // A second backup should have been created (the safety backup of the current state)
        $extensionBackupDir = $this->backupAbsDir . '/dummy/';
        $dirs = array_diff(scandir($extensionBackupDir), ['.', '..', $backupEntry]);
        self::assertNotEmpty($dirs, 'Expected a safety backup to be created before restore');
    }

    /**
     * @test
     */
    public function restoreBackupThrowsExceptionForInvalidDirectoryName(): void
    {
        $this->expectException(\Exception::class);
        RoundTrip::restoreBackup($this->extension, '../etc/passwd', $this->backupRelDir);
    }
}
