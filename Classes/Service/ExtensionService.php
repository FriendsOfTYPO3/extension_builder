<?php

namespace EBT\ExtensionBuilder\Service;

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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class ExtensionService
{
    /**
     * @return string[]
     */
    public function resolveStoragePaths(): array
    {
        $storagePaths = array_merge(
            [Environment::getExtensionsPath()],
            $this->resolveComposerStoragePaths()
        );

        return array_map(
            function (string $storagePath) {
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
        if (!defined('TYPO3_COMPOSER_MODE') || !TYPO3_COMPOSER_MODE) {
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
            if (GeneralUtility::isAbsPath($repository['url'])) {
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
}
