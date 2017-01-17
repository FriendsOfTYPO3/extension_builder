<?php
namespace EBT\ExtensionBuilder\Tests;

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

use EBT\ExtensionBuilder\Utility\Spyc;
use org\bovigo\vfs\vfsStream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\UnknownClassException;

abstract class BaseFunctionalTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    /**
     * @var bool
     */
    protected $backupGlobals = false;

    /**
     * @var string
     */
    protected $modelClassDir = 'Classes/Domain/Model/';
    /**
     * @var string
     */
    protected $codeTemplateRootPath = '';
    /**
     * @var string
     */
    protected $modelClassTemplatePath = '';
    /**
     * @var string
     */
    protected $fixturesPath = '';
    /**
     * @var \EBT\ExtensionBuilder\Service\Parser
     */
    protected $parserService = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\Printer
     */
    protected $printerService = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\ClassBuilder
     */
    protected $classBuilder = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\RoundTrip
     */
    protected $roundTripService = null;
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\FileGenerator
     */
    protected $fileGenerator = null;
    protected $testExtensionsToLoad = array('typo3conf/ext/extension_builder');

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        if (!class_exists('PhpParser\Parser')) {
            throw new UnknownClassException('PhpParser not found!!');
        }
        $this->fixturesPath = __DIR__ . '/Fixtures/';

        $testTargetDir = 'testDir';
        vfsStream::setup($testTargetDir);
        $dummyExtensionDir = vfsStream::url($testTargetDir) . '/';

        $yamlParser = new Spyc();
        $settings = $yamlParser->YAMLLoadString(file_get_contents($this->fixturesPath . 'Settings/settings1.yaml'));

        $this->extension = $this->getMock(\EBT\ExtensionBuilder\Domain\Model\Extension::class, array('getExtensionDir'));
        $this->extension->setVendorName('EBT');
        $this->extension->setExtensionKey('dummy');
        $this->extension->expects(
            self::any())
            ->method('getExtensionDir')
            ->will(self::returnValue($dummyExtensionDir));
        if (is_dir($dummyExtensionDir)) {
            GeneralUtility::mkdir($dummyExtensionDir);
        }
        $this->extension->setSettings($settings);

        // get instances to inject in Mocks
        $configurationManager = $this->objectManager->get(\EBT\ExtensionBuilder\Configuration\ConfigurationManager::class);

        $this->parserService = new \EBT\ExtensionBuilder\Service\Parser(new \PhpParser\Lexer());
        $this->printerService = $this->objectManager->get(\EBT\ExtensionBuilder\Service\Printer::class);
        $localizationService = $this->objectManager->get(\EBT\ExtensionBuilder\Service\LocalizationService::class);

        $this->classBuilder = $this->objectManager->get(\EBT\ExtensionBuilder\Service\ClassBuilder::class);
        $this->classBuilder->initialize($this->extension);

        $this->roundTripService = $this->getAccessibleMock(\EBT\ExtensionBuilder\Service\RoundTrip::class, array('dummy'));
        $this->inject($this->roundTripService, 'configurationManager', $configurationManager);
        $this->inject($this->roundTripService, 'parserService', $this->parserService);
        $this->roundTripService->initialize($this->extension);

        $this->fileGenerator = $this->getAccessibleMock(\EBT\ExtensionBuilder\Service\FileGenerator::class, array('dummy'));
        $this->inject($this->fileGenerator, 'objectManager', $this->objectManager);
        $this->inject($this->fileGenerator, 'printerService', $this->printerService);
        $this->inject($this->fileGenerator, 'localizationService', $localizationService);
        $this->inject($this->fileGenerator, 'classBuilder', $this->classBuilder);

        $this->inject($this->fileGenerator, 'roundTripService', $this->roundTripService);

        $this->codeTemplateRootPath = PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/';
        $this->modelClassTemplatePath = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';

        $this->fileGenerator->setSettings(
            array(
                'codeTemplateRootPath' => $this->codeTemplateRootPath,
                'extConf' => array(
                    'enableRoundtrip' => '1'
                )
            )
        );
        $this->fileGenerator->_set('codeTemplateRootPath', __DIR__ . '/../Resources/Private/CodeTemplates/Extbase/');
        $this->fileGenerator->_set('enableRoundtrip', true);
        $this->fileGenerator->_set('extension', $this->extension);
    }

    protected function tearDown()
    {
        if (!empty($this->extension) && $this->extension->getExtensionKey() != null) {
            GeneralUtility::rmdir($this->extension->getExtensionDir(), true);
        }
    }

    /**
     * Helper function
     * @param $name
     * @param $entity
     * @param $aggregateRoot
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    protected function buildDomainObject($name, $entity = false, $aggregateRoot = false)
    {
        $domainObject = $this->getAccessibleMock(
            'EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject',
            array('dummy')
        );
        /* @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject */
        $domainObject->setExtension($this->extension);
        $domainObject->setName($name);
        $domainObject->setEntity($entity);
        $domainObject->setAggregateRoot($aggregateRoot);
        if ($aggregateRoot) {
            $defaultActions = ['list', 'show', 'new', 'create', 'edit', 'update', 'delete'];
            foreach ($defaultActions as $actionName) {
                $action = GeneralUtility::makeInstance(\EBT\ExtensionBuilder\Domain\Model\DomainObject\Action::class);
                $action->setName($actionName);
                if ($actionName == 'deleted') {
                    $action->setNeedsTemplate = false;
                }
                $domainObject->addAction($action);
            }
        }
        return $domainObject;
    }

    /**
     * Builds an initial class file to test parsing and modifiying of existing classes
     *
     * This class file is generated based on the CodeTemplates
     * @param string $modelName
     */
    protected function generateInitialModelClassFile($modelName)
    {
        $domainObject = $this->buildDomainObject($modelName);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        $modelClassDir = 'Classes/Domain/Model/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $modelClassDir);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertTrue(is_dir($absModelClassDir), 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        GeneralUtility::writeFile($modelClassPath, $classFileContent);
    }

    protected function removeInitialModelClassFile($modelName)
    {
        if (@file_exists($this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php')) {
            unlink($this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php');
        }
        self::assertFalse(file_exists($this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php'), 'Dummy files could not be removed:' . $this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php');
    }
}
