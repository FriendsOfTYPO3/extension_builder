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
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use org\bovigo\vfs\vfsStream;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RoundTripInitializeTest extends BaseFunctionalTest
{
    private RoundTrip $roundTrip;
    private ExtensionBuilderConfigurationManager $configurationManager;
    private ExtensionSchemaBuilder $extensionSchemaBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);
        $this->extensionSchemaBuilder = GeneralUtility::makeInstance(ExtensionSchemaBuilder::class);
        $this->roundTrip = $this->getAccessibleMock(
            RoundTrip::class,
            null,
            [new ParserService(), $this->configurationManager, $this->extensionSchemaBuilder]
        );
    }

    private function buildTestExtension(string $extensionKey, string $vfsStoragePath): Extension
    {
        $extension = $this->getMockBuilder(Extension::class)
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $extension->setVendorName('EBT');
        $extension->setExtensionKey($extensionKey);
        $extension->setStoragePath($vfsStoragePath);
        $extension->expects(self::any())
            ->method('getExtensionDir')
            ->willReturn($vfsStoragePath . $extensionKey . '/');
        return $extension;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initializeWithMissingJsonTreatsAsFreshExtension(): void
    {
        $extKey = 'no_json_ext';
        $vfsStoragePath = 'vfs://root/testDir/';

        // Directory exists but no ExtensionBuilder.json inside
        $extDir = vfsStream::newDirectory($extKey);
        $this->testDir->addChild($extDir);

        $extension = $this->buildTestExtension($extKey, $vfsStoragePath);
        $this->roundTrip->initialize($extension);

        self::assertNull($this->roundTrip->_get('previousExtension'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initializeWithMalformedJsonTreatsAsFreshExtension(): void
    {
        $extKey = 'malformed_json_ext';
        $vfsStoragePath = 'vfs://root/testDir/';

        $extDir = vfsStream::newDirectory($extKey);
        $jsonFile = vfsStream::newFile(ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE);
        $jsonFile->setContent('{invalid json content...');
        $extDir->addChild($jsonFile);
        $this->testDir->addChild($extDir);

        $extension = $this->buildTestExtension($extKey, $vfsStoragePath);
        $this->roundTrip->initialize($extension);

        self::assertNull($this->roundTrip->_get('previousExtension'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initializeWithValidJsonLoadsPreviousExtension(): void
    {
        $extKey = 'valid_json_ext';
        $vfsStoragePath = 'vfs://root/testDir/';

        // Minimal valid JSON where extensionKey matches $extKey to avoid triggering the rename path
        $validJson = json_encode([
            'modules' => [
                [
                    'config' => ['position' => [100, 100]],
                    'name' => 'New Model Object',
                    'value' => [
                        'actionGroup' => ['_default1_list' => true],
                        'name' => 'Item',
                        'objectsettings' => [
                            'aggregateRoot' => true,
                            'description' => '',
                            'type' => 'Entity',
                            'uid' => '100000000001',
                        ],
                        'propertyGroup' => ['properties' => []],
                        'relationGroup' => ['relations' => []],
                    ],
                ],
            ],
            'properties' => [
                'extensionKey' => $extKey,
                'vendorName' => 'Vendor',
                'name' => 'Valid JSON Test Extension',
                'description' => '',
            ],
            'storagePath' => $vfsStoragePath,
            'wires' => [],
        ]);

        $extDir = vfsStream::newDirectory($extKey);
        $jsonFile = vfsStream::newFile(ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE);
        $jsonFile->setContent($validJson);
        $extDir->addChild($jsonFile);
        $this->testDir->addChild($extDir);

        $extension = $this->buildTestExtension($extKey, $vfsStoragePath);
        $this->roundTrip->initialize($extension);

        self::assertNotNull($this->roundTrip->_get('previousExtension'));
    }
}
