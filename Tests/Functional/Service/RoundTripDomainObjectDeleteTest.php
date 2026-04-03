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
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Service\FileGenerator;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RoundTripDomainObjectDeleteTest extends BaseFunctionalTest
{
    /**
     * @test
     */
    public function deletedDomainObjectFilesAreRemovedDuringInitialize(): void
    {
        $modelName = 'Foo';
        $uid = '100000000099';
        $extDir = $this->extension->getExtensionDir();

        // Create model, controller, and repository files for "Foo"
        $modelDir = $extDir . 'Classes/Domain/Model/';
        $controllerDir = $extDir . 'Classes/Controller/';
        $repositoryDir = $extDir . 'Classes/Domain/Repository/';
        mkdir($modelDir, 0777, true);
        mkdir($controllerDir, 0777, true);
        mkdir($repositoryDir, 0777, true);

        file_put_contents($modelDir . $modelName . '.php', "<?php\nnamespace EBT\\Dummy\\Domain\\Model;\nclass Foo extends \\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity {}\n");
        file_put_contents($controllerDir . $modelName . 'Controller.php', "<?php\nnamespace EBT\\Dummy\\Controller;\nclass FooController extends \\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController {\n    public function listAction(): void {}\n}\n");
        file_put_contents($repositoryDir . $modelName . 'Repository.php', "<?php\nnamespace EBT\\Dummy\\Domain\\Repository;\nclass FooRepository extends \\TYPO3\\CMS\\Extbase\\Persistence\\Repository {}\n");

        self::assertFileExists($modelDir . $modelName . '.php');
        self::assertFileExists($controllerDir . $modelName . 'Controller.php');
        self::assertFileExists($repositoryDir . $modelName . 'Repository.php');

        // Build an ExtensionBuilder.json with "Foo" as the only domain object
        $json = json_encode([
            'modules' => [
                [
                    'config' => ['position' => [100, 100]],
                    'name' => 'New Model Object',
                    'value' => [
                        'actionGroup' => ['_default1_list' => true, '_default2_show' => true],
                        'name' => $modelName,
                        'objectsettings' => [
                            'aggregateRoot' => true,
                            'description' => '',
                            'type' => 'Entity',
                            'uid' => $uid,
                        ],
                        'propertyGroup' => ['properties' => []],
                        'relationGroup' => ['relations' => []],
                    ],
                ],
            ],
            'properties' => [
                'extensionKey' => $this->extension->getExtensionKey(),
                'vendorName' => $this->extension->getVendorName(),
                'name' => 'Test Extension',
                'description' => '',
            ],
            'storagePath' => 'dummy',
            'wires' => [],
        ]);
        file_put_contents($extDir . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE, $json);

        // Create a new extension WITHOUT "Foo" domain objects
        $configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);
        $extensionSchemaBuilder = GeneralUtility::makeInstance(ExtensionSchemaBuilder::class);
        $roundTrip = $this->getAccessibleMock(
            RoundTrip::class,
            null,
            [new ParserService(), $configurationManager, $extensionSchemaBuilder]
        );

        $newExtension = $this->getMockBuilder(\EBT\ExtensionBuilder\Domain\Model\Extension::class)
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $newExtension->setVendorName($this->extension->getVendorName());
        $newExtension->setExtensionKey($this->extension->getExtensionKey());
        $newExtension->setStoragePath('dummy');
        $newExtension->expects(self::any())
            ->method('getExtensionDir')
            ->willReturn($extDir);
        // No domain objects in the new extension — simulates deleting "Foo"

        $roundTrip->initialize($newExtension);

        self::assertFileDoesNotExist($modelDir . $modelName . '.php', 'Model file must be deleted');
        self::assertFileDoesNotExist($controllerDir . $modelName . 'Controller.php', 'Controller file must be deleted');
        self::assertFileDoesNotExist($repositoryDir . $modelName . 'Repository.php', 'Repository file must be deleted');
    }
}
