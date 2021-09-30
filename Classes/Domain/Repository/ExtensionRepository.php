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

namespace EBT\ExtensionBuilder\Domain\Repository;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Service\ExtensionService;
use Exception;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Repository for existing Extbase Extensions
 */
class ExtensionRepository implements SingletonInterface
{
    protected ExtensionBuilderConfigurationManager $configurationManager;
    protected ExtensionService $extensionService;

    public function injectExtensionBuilderConfigurationManager(
        ExtensionBuilderConfigurationManager $configurationManager
    ): void {
        $this->configurationManager = $configurationManager;
    }

    public function injectExtensionService(ExtensionService $extensionService): void
    {
        $this->extensionService = $extensionService;
    }

    /**
     * loops through all extensions in typo3conf/ext/
     * and searches for a JSON file with extension builder configuration
     *
     * @return array
     */
    public function findAll(): array
    {
        $extensions = [];
        foreach ($this->extensionService->resolveStoragePaths() as $storagePath) {
            $extensions = array_merge($extensions, $this->findAllInDirectory($storagePath));
        }
        return array_values($extensions);
    }

    protected function findAllInDirectory(string $storagePath): array
    {
        $result = [];
        $extensionDirectoryHandle = opendir($storagePath);
        while (false !== ($singleExtensionDirectory = readdir($extensionDirectoryHandle))) {
            if ($singleExtensionDirectory[0] == '.' || !is_dir($storagePath . $singleExtensionDirectory)) {
                continue;
            }
            $extensionBuilderConfiguration = $this->configurationManager
                ->getExtensionBuilderConfiguration($singleExtensionDirectory, $storagePath);
            if ($extensionBuilderConfiguration !== null) {
                $result[$singleExtensionDirectory] = [
                    'name' => $singleExtensionDirectory,
                    'storagePath' => $storagePath,
                    'working' => json_encode($extensionBuilderConfiguration)
                ];
            }
        }
        closedir($extensionDirectoryHandle);

        return $result;
    }

    /**
     * @param Extension $extension
     *
     * @throws Exception
     * @throws \TYPO3\CMS\Core\Package\Exception
     */
    public function saveExtensionConfiguration(Extension $extension): void
    {
        $extensionBuildConfiguration = $this->configurationManager->getConfigurationFromModeler();
        $extensionBuildConfiguration['storagePath'] = $extension->getStoragePath();
        $extensionBuildConfiguration['log'] = [
            'last_modified' => date('Y-m-d h:i'),
            'extension_builder_version' => ExtensionManagementUtility::getExtensionVersion('extension_builder'),
            'be_user' => $this->getBackendUserAuthentication()->user['realName'] . ' (' . $this->getBackendUserAuthentication()->user['uid'] . ')'
        ];
        $encodeOptions = 0;
        // option JSON_PRETTY_PRINT is available since PHP 5.4.0
        if (defined('JSON_PRETTY_PRINT')) {
            $encodeOptions |= JSON_PRETTY_PRINT;
        }
        GeneralUtility::writeFile(
            $extension->getExtensionDir() . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE,
            json_encode($extensionBuildConfiguration, $encodeOptions)
        );
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
