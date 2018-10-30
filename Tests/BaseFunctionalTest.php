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

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Service\ClassBuilder;
use EBT\ExtensionBuilder\Service\FileGenerator;
use EBT\ExtensionBuilder\Service\LocalizationService;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Service\Printer;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Utility\SpycYAMLParser;
use org\bovigo\vfs\vfsStream;
use PhpParser\Lexer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\UnknownClassException;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;

abstract class BaseFunctionalTest extends FunctionalTestCase
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
     * @var \EBT\ExtensionBuilder\Service\ParserService
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

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
     protected $testDir = NULL;

    protected $testExtensionsToLoad = ['typo3conf/ext/extension_builder'];

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->fixturesPath = __DIR__ . '/Fixtures/';

        $rootDir = vfsStream::setup('root');
        $this->testDir = vfsStream::newDirectory('testDir');
        $rootDir->addChild($this->testDir);

        $fixturesDir = vfsStream::newDirectory('Fixtures');
        $rootDir->addChild($fixturesDir);

        vfsStream::copyFromFileSystem($this->fixturesPath, $fixturesDir, 1024*1024);

        $yamlParser = new SpycYAMLParser();
        $settings = $yamlParser->YAMLLoadString(file_get_contents($this->fixturesPath . 'Settings/settings1.yaml'));

        $this->extension = $this->getMockBuilder(Extension::class)
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $this->extension->setVendorName('EBT');
        $this->extension->setExtensionKey('dummy');
        $dummyExtensionDir = 'dummy';
        $this->extension->expects(
            self::any())
            ->method('getExtensionDir')
            ->will(self::returnValue($dummyExtensionDir));
        if (is_dir($dummyExtensionDir)) {
           GeneralUtility::mkdir($dummyExtensionDir, true);
        }

        $this->extension->setSettings($settings);

        // get instances to inject in Mocks
        $configurationManager = $this->objectManager->get(ExtensionBuilderConfigurationManager::class);

        $this->parserService = new ParserService();
        $this->printerService = $this->objectManager->get(Printer::class);
        $localizationService = $this->objectManager->get(LocalizationService::class);

        $this->classBuilder = $this->objectManager->get(ClassBuilder::class);
        $this->classBuilder->initialize($this->extension);

        $this->roundTripService = $this->getAccessibleMock(RoundTrip::class, ['dummy']);
        $this->inject($this->roundTripService, 'configurationManager', $configurationManager);
        $this->inject($this->roundTripService, 'parserService', $this->parserService);
        $this->roundTripService->initialize($this->extension);

        $this->fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['dummy']);
        $this->inject($this->fileGenerator, 'objectManager', $this->objectManager);
        $this->inject($this->fileGenerator, 'printerService', $this->printerService);
        $this->inject($this->fileGenerator, 'localizationService', $localizationService);
        $this->inject($this->fileGenerator, 'classBuilder', $this->classBuilder);

        $this->inject($this->fileGenerator, 'roundTripService', $this->roundTripService);

        $this->codeTemplateRootPath = PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/';
        $this->modelClassTemplatePath = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';

        $this->fileGenerator->setSettings(
            [
                'codeTemplateRootPaths.' => [PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/'],
                'codeTemplatePartialPaths.' => [PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/Partials'],
                'extConf' => [
                    'enableRoundtrip' => '1'
                ]
            ]
        );
        // needed when sub routines in file generator are called without an initial setup
        $this->fileGenerator->_set('codeTemplateRootPaths', [PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/']);
        $this->fileGenerator->_set('codeTemplatePartialPaths', [PATH_typo3conf . 'ext/extension_builder/Resources/Private/CodeTemplates/Extbase/Partials']);
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
            ['dummy']
        );
        /* @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject */
        $domainObject->setExtension($this->extension);
        $domainObject->setName($name);
        $domainObject->setEntity($entity);
        $domainObject->setAggregateRoot($aggregateRoot);
        if ($aggregateRoot) {
            $defaultActions = ['list', 'show', 'new', 'create', 'edit', 'update', 'delete'];
            foreach ($defaultActions as $actionName) {
                $action = GeneralUtility::makeInstance(Action::class);
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
     *
     * @param string $modelName
     *
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     * @throws \Exception
     */
    protected function generateInitialModelClassFile($modelName)
    {
        $domainObject = $this->buildDomainObject($modelName);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, false);
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
