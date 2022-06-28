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
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Core\Environment;

class ExtensionServiceTest extends BaseFunctionalTest
{
    /**
     * @test
     */
    public function resolveStoragePathsReturnsOnePathIfSetInExtensionConfiguration(): void
    {
        $configurationManager = $this->getAccessibleMock(ExtensionBuilderConfigurationManager::class, ['getExtensionBuilderSettings']);
        $configurationManager->expects(self::any())
            ->method('getExtensionBuilderSettings')
            ->willReturn(['storageDir' => '/var/www/html/packages/']);
        $this->extensionService->injectExtensionBuilderConfigurationManager($configurationManager);
        $storagePaths = $this->extensionService->resolveStoragePaths();

        self::assertCount(1, $storagePaths);
        self::assertEquals('/var/www/html/packages/', $storagePaths[0]);
    }

    /**
     * @test
     */
    public function resolveStoragePathsReturnsNoPathIfComposerJsonIsMissingInComposerMode(): void
    {
        $configurationManager = $this->getAccessibleMock(ExtensionBuilderConfigurationManager::class, ['getExtensionBuilderSettings']);
        $configurationManager->expects(self::any())
            ->method('getExtensionBuilderSettings')
            ->willReturn(['storageDir' => '']);
        $this->extensionService->injectExtensionBuilderConfigurationManager($configurationManager);
        $backupEnvironment = $this->getEnvironmentAsArray();
        Environment::initialize(...array_values(array_merge($backupEnvironment, ['composerMode' => true])));
        $storagePaths = $this->extensionService->resolveStoragePaths();
        Environment::initialize(...array_values($backupEnvironment));

        self::assertCount(0, $storagePaths);
    }

    /**
     * @test
     */
    public function resolveStoragePathsReturnsOnePathInLegacyMode(): void
    {
        $configurationManager = $this->getAccessibleMock(ExtensionBuilderConfigurationManager::class, ['getExtensionBuilderSettings']);
        $configurationManager->expects(self::any())
            ->method('getExtensionBuilderSettings')
            ->willReturn(['storageDir' => '']);
        $this->extensionService->injectExtensionBuilderConfigurationManager($configurationManager);
        $backupEnvironment = $this->getEnvironmentAsArray();
        Environment::initialize(...array_values(array_merge($backupEnvironment, ['composerMode' => false])));
        $storagePaths = $this->extensionService->resolveStoragePaths();
        Environment::initialize(...array_values($backupEnvironment));

        self::assertCount(1, $storagePaths);
        self::assertStringEndsWith('/typo3conf/ext/', $storagePaths[0]);
    }

    /**
     * This method mimics \TYPO3\CMS\Core\Environment::toArray which unfortunately does not return the "composerMode"
     * field.
     *
     * @return array
     */
    private function getEnvironmentAsArray(): array
    {
        return [
            'context' => Environment::getContext(),
            'cli' => Environment::isCli(),
            'composerMode' => Environment::isComposerMode(),
            'projectPath' => Environment::getProjectPath(),
            'publicPath' => Environment::getPublicPath(),
            'varPath' => Environment::getVarPath(),
            'configPath' => Environment::getConfigPath(),
            'currentScript' => Environment::getCurrentScript(),
            'os' => Environment::isWindows() ? 'WINDOWS' : 'UNIX',
        ];
    }

}
