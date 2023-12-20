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

namespace EBT\ExtensionBuilder\Service;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class ExtensionService
{
    public const COMPOSER_PATH_WARNING = 'You are running TYPO3 in composer mode. You have to configure at
        least one local path repository in your composer.json in order to create an extension with
        Extension Builder. The recommended way is to create a "packages" folder where your extension is loaded from and symlinked into typo3conf/ext afterwards.<br /><br />
        Please execute <code>mkdir -p packages && composer config repositories.local path "packages/*"</code> in your terminal in the project root directory.
        After that reload the Extension Builder.<br /><br />
        See <a style="text-decoration: underline" target="_blank"
        href="https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html">
        Documentation</a>';

    /**
     * @return string[]
     */
    public function resolveStoragePaths(): array
    {
        if (Environment::isComposerMode()) {
            $storagePaths = $this->resolveComposerStoragePaths();
        } else {
            $storagePaths = [Environment::getExtensionsPath()];
        }

        return array_map(
            static function (string $storagePath) {
                return rtrim($storagePath, '/') . '/';
            },
            $storagePaths
        );
    }

    /**
     * @return string[]
     */
    public function resolveComposerStoragePaths(): array
    {
        if (!Environment::isComposerMode()) {
            return [];
        }

        $storagePaths = [];
        $projectPath = Environment::getProjectPath();
        $composerSettings = json_decode(file_get_contents($projectPath . '/composer.json'), true);
        foreach ($composerSettings['repositories'] ?? [] as $repository) {
            if (empty($repository['url']) || ($repository['type'] ?? null) !== 'path') {
                continue;
            }
            // skip non-symlinked path repositories
            if (($repository['options']['symlink'] ?? null) === false) {
                continue;
            }
            if (PathUtility::isAbsolutePath($repository['url'])) {
                $storagePaths[] = $repository['url'];
            } else {
                $repositoryPath = PathUtility::getCanonicalPath($projectPath . '/' . $repository['url']);
                $storagePaths[] = preg_replace('#/[*?/]+$#', '', $repositoryPath);
            }
        }
        return $storagePaths;
    }

    public function isComposerStoragePath(string $path): bool
    {
        foreach ($this->resolveComposerStoragePaths() as $composerStoragePath) {
            if (strpos($path, $composerStoragePath) === 0) {
                return true;
            }
        }
        return false;
    }

    public function isStoragePathConfigured(): bool
    {
        return !Environment::isComposerMode() || count($this->resolveStoragePaths()) > 0;
    }

    /**
     * Returns, if typo3/cms-composer-installers v4 is used by checking, if /vendor/friendsoftypo3/extension-builder/
     * is part of the absolute install path of extension_builder
     */
    public function isComposerInstallerV4(): bool
    {
        $extensionPath = GeneralUtility::getFileAbsFileName('EXT:extension_builder/');
        return str_contains($extensionPath, '/vendor/friendsoftypo3/extension-builder/');
    }
}
