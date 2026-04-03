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
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Tests that extension key renames propagate to all namespace references.
 *
 * Uses the test_extension fixture (key: "test_extension", vendor: "FIXTURE") as
 * the "old" state and simulates a rename to key "new_test_ext".
 */
class RoundTripExtensionKeyRenameTest extends BaseFunctionalTest
{
    private Extension $fixtureExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);
        $extensionSchemaBuilder = GeneralUtility::makeInstance(ExtensionSchemaBuilder::class);

        $testExtensionDir = $this->fixturesPath . 'TestExtensions/test_extension/';
        $jsonFile = $testExtensionDir . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;

        $extensionConfigurationJSON = json_decode(file_get_contents($jsonFile), true);
        $extensionConfigurationJSON = $configurationManager->fixExtensionBuilderJSON($extensionConfigurationJSON);

        $this->fixtureExtension = $extensionSchemaBuilder->build($extensionConfigurationJSON);
        $this->fixtureExtension->setExtensionDir($testExtensionDir);

        // Simulate extension key rename: old key = "test_extension", new key = "new_test_ext"
        $this->fixtureExtension->setOriginalExtensionKey('test_extension');
        $this->fixtureExtension->setExtensionKey('new_test_ext');

        $this->roundTripService->_set('extension', $this->fixtureExtension);
        $this->roundTripService->_set('previousExtensionDirectory', $testExtensionDir);
        $this->roundTripService->_set('extensionDirectory', $testExtensionDir);
        $this->roundTripService->_set('extensionRenamed', true);
        $this->roundTripService->_set('previousExtensionKey', 'test_extension');
        $this->roundTripService->_set(
            'previousDomainObjects',
            [
                $this->fixtureExtension->getDomainObjectByName('Main')->getUniqueIdentifier() => $this->fixtureExtension->getDomainObjectByName('Main'),
            ]
        );
    }

    protected function tearDown(): void
    {
        // Avoid deletion of the shared fixture extension directory
    }

    /**
     * @test
     */
    public function extensionKeyRenameUpdatesNamespaceInModelClass(): void
    {
        $mainObject = $this->fixtureExtension->getDomainObjectByName('Main');
        $modelFile = $this->roundTripService->getDomainModelClassFile($mainObject);

        self::assertNotNull($modelFile, 'Model class file must not be null');
        $modelClass = $modelFile->getFirstClass();

        $namespaceName = $modelClass->getNamespaceName();
        self::assertStringContainsString(
            'NewTestExt',
            $namespaceName,
            'Model namespace must contain the new extension name segment "NewTestExt"'
        );
        self::assertStringNotContainsString(
            'TestExtension',
            $namespaceName,
            'Model namespace must not contain the old extension name "TestExtension"'
        );
    }

    /**
     * @test
     */
    public function extensionKeyRenameUpdatesNamespaceInControllerClass(): void
    {
        $mainObject = $this->fixtureExtension->getDomainObjectByName('Main');
        $controllerFile = $this->roundTripService->getControllerClassFile($mainObject);

        self::assertNotNull($controllerFile, 'Controller class file must not be null');
        $controllerClass = $controllerFile->getFirstClass();

        $namespaceName = $controllerClass->getNamespaceName();
        self::assertStringContainsString(
            'NewTestExt',
            $namespaceName,
            'Controller namespace must contain the new extension name segment "NewTestExt"'
        );
        self::assertStringNotContainsString(
            'TestExtension',
            $namespaceName,
            'Controller namespace must not contain the old extension name "TestExtension"'
        );
    }

    /**
     * @test
     */
    public function extensionKeyRenameUpdatesNamespaceInRepositoryClass(): void
    {
        $mainObject = $this->fixtureExtension->getDomainObjectByName('Main');
        $repositoryFile = $this->roundTripService->getRepositoryClassFile($mainObject);

        self::assertNotNull($repositoryFile, 'Repository class file must not be null');
        $repositoryClass = $repositoryFile->getFirstClass();

        $namespaceName = $repositoryClass->getNamespaceName();
        self::assertStringContainsString(
            'NewTestExt',
            $namespaceName,
            'Repository namespace must contain the new extension name segment "NewTestExt"'
        );
        self::assertStringNotContainsString(
            'TestExtension',
            $namespaceName,
            'Repository namespace must not contain the old extension name "TestExtension"'
        );
    }
}
