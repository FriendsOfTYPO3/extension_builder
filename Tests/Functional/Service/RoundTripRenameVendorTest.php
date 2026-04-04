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
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class RoundTripRenameVendorTest extends BaseFunctionalTest
{
    private Extension $fixtureExtension;

    /**
     * @throws ExtensionException
     * @throws InvalidConfigurationTypeException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);
        $extensionSchemaBuilder = GeneralUtility::makeInstance(ExtensionSchemaBuilder::class);

        $testExtensionDir = $this->fixturesPath . 'TestExtensions/test_extension/';
        $jsonFile = $testExtensionDir . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;

        if (file_exists($jsonFile)) {
            // compatibility adaptions for configurations from older versions
            $extensionConfigurationJSON = json_decode(file_get_contents($jsonFile), true);
            $extensionConfigurationJSON = $configurationManager->fixExtensionBuilderJSON(
                $extensionConfigurationJSON
            );
        } else {
            $extensionConfigurationJSON = [];
            self::fail('JSON file not found: ' . $jsonFile);
        }

        $this->fixtureExtension = $extensionSchemaBuilder->build($extensionConfigurationJSON);
        $this->fixtureExtension->setExtensionDir($testExtensionDir);
        $this->roundTripService->_set('extension', $this->fixtureExtension);
        $this->roundTripService->_set('previousExtensionDirectory', $testExtensionDir);
        $this->roundTripService->_set('extensionDirectory', $testExtensionDir);
        $this->roundTripService->_set(
            'previousDomainObjects',
            [
                $this->fixtureExtension->getDomainObjectByName('Main')->getUniqueIdentifier() => $this->fixtureExtension->getDomainObjectByName('Main'),
            ]
        );
        $this->fileGenerator->setSettings(
            [
                'codeTemplateRootPath' => Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/',
                'extConf' => [
                    'enableRoundtrip' => '1',
                ],
            ]
        );
    }

    protected function tearDown(): void
    {
        // overwrite parent tearDown to avoid deletion of fixture extension
    }

    /**
     * @test
     */
    public function changeVendorNameResultsInNewNamespace(): void
    {
        $this->fixtureExtension->setOriginalVendorName('FIXTURE');
        $this->fixtureExtension->setVendorName('VENDOR');
        self::assertEquals('VENDOR\TestExtension', $this->fixtureExtension->getNamespaceName());
    }

    /**
     * @test
     */
    public function changeVendorNameResultsInUpdatedTagsInControllerClass(): void
    {
        $this->fixtureExtension->setOriginalVendorName('FIXTURE');
        $this->fixtureExtension->setVendorName('VENDOR');

        $controllerClassFile = $this->roundTripService->getControllerClassFile($this->fixtureExtension->getDomainObjectByName('Main'));
        $controllerClassObject = $controllerClassFile->getFirstClass();

        // Check constructor parameter type hint (modern style with constructor injection)
        $methods = $controllerClassObject->getMethods();
        $constructorMethod = null;
        foreach ($methods as $method) {
            if ($method->getName() === '__construct') {
                $constructorMethod = $method;
                break;
            }
        }

        self::assertNotNull($constructorMethod, 'Constructor should be defined');
        $parameters = $constructorMethod->getParameters();
        self::assertNotEmpty($parameters, 'Constructor should have parameters');

        // Check first parameter type hint for repository
        $firstParam = current($parameters);
        $typeHint = $firstParam->getTypeHint();
        self::assertEquals('\VENDOR\TestExtension\Domain\Repository\MainRepository', $typeHint);
    }

    /**
     * @test
     */
    public function changeVendorNameResultsInUpdatedTagsInModelClass(): void
    {
        $this->fixtureExtension->setOriginalVendorName('FIXTURE');
        $this->fixtureExtension->setVendorName('VENDOR');

        $modelClassFile = $this->roundTripService->getDomainModelClassFile($this->fixtureExtension->getDomainObjectByName('Main'));
        $modelClassObject = $modelClassFile->getFirstClass();
        $properties = $modelClassObject->getProperties();
        self::assertEquals(
            '\VENDOR\TestExtension\Domain\Model\Child1',
            $properties['child1']->getTagValue('var')
        );
        self::assertEquals(
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\VENDOR\TestExtension\Domain\Model\Child2>',
            $properties['children2']->getTagValue('var')
        );
        self::assertEquals(
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\VENDOR\TestExtension\Domain\Model\Child4>',
            $properties['children4']->getTagValue('var')
        );
    }
}
