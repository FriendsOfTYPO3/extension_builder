<?php
namespace EBT\ExtensionBuilder\Tests\Functional;

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

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripRenameVendorTest extends BaseFunctionalTest
{
    /**
     * @var \EBT\ExtensionBuilder\Service\ObjectSchemaBuilder
     */
    protected $objectSchemaBuilder = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder
     */
    protected $extensionSchemaBuilder = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $fixtureExtension = null;

    protected function setUp()
    {
        parent::setUp();
        $this->configurationManager = $this->getAccessibleMock(
            'EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager',
            ['dummy']
        );
        $this->extensionSchemaBuilder = $this->objectManager->get('EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder');

        $testExtensionDir = $this->fixturesPath . 'TestExtensions/test_extension/';
        $jsonFile = $testExtensionDir . ExtensionBuilderConfigurationManager::EXTENSION_BUILDER_SETTINGS_FILE;

        if (file_exists($jsonFile)) {
            // compatibility adaptions for configurations from older versions
            $extensionConfigurationJSON = json_decode(file_get_contents($jsonFile), true);
            $extensionConfigurationJSON = $this->configurationManager->fixExtensionBuilderJSON($extensionConfigurationJSON, false);
        } else {
            $extensionConfigurationJSON = [];
            self::fail('JSON file not found: ' . $jsonFile);
        }

        $this->fixtureExtension = $this->extensionSchemaBuilder->build($extensionConfigurationJSON);
        $this->fixtureExtension->setExtensionDir($testExtensionDir);
        $this->roundTripService->_set('extension', $this->fixtureExtension);
        $this->roundTripService->_set('previousExtensionDirectory', $testExtensionDir);
        $this->roundTripService->_set('extensionDirectory', $testExtensionDir);
        $this->roundTripService->_set('previousDomainObjects', [
            $this->fixtureExtension->getDomainObjectByName('Main')->getUniqueIdentifier() => $this->fixtureExtension->getDomainObjectByName('Main')
        ]);
        $this->fileGenerator->setSettings(
            [
                'codeTemplateRootPath' => PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/',
                'extConf' => [
                    'enableRoundtrip' => '1'
                ]
            ]
        );
    }

    protected function tearDown()
    {
        // overwrite parent tearDown to avoid deletion of fixture extension
    }

    /**
     * @test
     */
    public function changeVendorNameResultsInNewNamespace()
    {
        $this->fixtureExtension->setOriginalVendorName('FIXTURE');
        $this->fixtureExtension->setVendorName('VENDOR');
        self::assertEquals('VENDOR\TestExtension', $this->fixtureExtension->getNamespaceName());
    }

    /**
     * @test
     */
    public function changeVendorNameResultsInUpdatedTagsInControllerClass()
    {
        $this->fixtureExtension->setOriginalVendorName('FIXTURE');
        $this->fixtureExtension->setVendorName('VENDOR');

        $controllerClassFile = $this->roundTripService->getControllerClassFile($this->fixtureExtension->getDomainObjectByName('Main'));
        $controllerClassObject = $controllerClassFile->getFirstClass();
        $repositoryProperty = current($controllerClassObject->getProperties());
        self::assertEquals('\VENDOR\TestExtension\Domain\Repository\MainRepository', $repositoryProperty->getTagValues('var'));
    }

    /**
     * @test
     */
    public function changeVendorNameResultsInUpdatedTagsInModelClass()
    {
        $this->fixtureExtension->setOriginalVendorName('FIXTURE');
        $this->fixtureExtension->setVendorName('VENDOR');

        $modelClassFile = $this->roundTripService->getDomainModelClassFile($this->fixtureExtension->getDomainObjectByName('Main'));
        $modelClassObject = $modelClassFile->getFirstClass();
        $properties = $modelClassObject->getProperties();
        self::assertEquals('\VENDOR\TestExtension\Domain\Model\Child1', $properties['child1']->getTagValue('var'));
        self::assertEquals('\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\VENDOR\TestExtension\Domain\Model\Child2>', $properties['children2']->getTagValue('var'));
        self::assertEquals('\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\VENDOR\TestExtension\Domain\Model\Child4>', $properties['children4']->getTagValue('var'));
    }
}
