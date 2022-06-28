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

namespace EBT\ExtensionBuilder\Tests;

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Exception\FileNotFoundException;
use EBT\ExtensionBuilder\Exception\SyntaxError;
use EBT\ExtensionBuilder\Service\ClassBuilder;
use EBT\ExtensionBuilder\Service\ExtensionService;
use EBT\ExtensionBuilder\Service\FileGenerator;
use EBT\ExtensionBuilder\Service\LocalizationService;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Service\Printer;
use EBT\ExtensionBuilder\Service\RoundTrip;
use EBT\ExtensionBuilder\Utility\SpycYAMLParser;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class BaseFunctionalTest extends FunctionalTestCase
{
    /**
     * @var bool
     */
    protected $backupGlobals = false;

    protected string $modelClassDir = 'Classes/Domain/Model/';
    protected string $codeTemplateRootPath = '';
    protected string $modelClassTemplatePath = '';
    protected string $fixturesPath = '';
    protected ParserService $parserService;
    protected Printer $printerService;
    protected ClassBuilder $classBuilder;
    protected RoundTrip $roundTripService;
    protected Extension $extension;
    protected ExtensionService $extensionService;
    protected FileGenerator $fileGenerator;
    protected vfsStreamDirectory $testDir;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/extension_builder'
    ];

    /**
     * @throws ExtensionException
     * @throws \Doctrine\DBAL\DBALException
     * @throws InvalidConfigurationTypeException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturesPath = __DIR__ . '/Fixtures/';

        $rootDir = vfsStream::setup('root');
        $this->testDir = vfsStream::newDirectory('testDir');
        $rootDir->addChild($this->testDir);

        $fixturesDir = vfsStream::newDirectory('Fixtures');
        $rootDir->addChild($fixturesDir);

        vfsStream::copyFromFileSystem($this->fixturesPath, $fixturesDir, 1024 * 1024);

        $settings = SpycYAMLParser::YAMLLoadString(file_get_contents($this->fixturesPath . 'Settings/settings1.yaml'));

        $this->extension = $this->getMockBuilder(Extension::class)
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $this->extension->setVendorName('EBT');
        $this->extension->setExtensionKey('dummy');
        $dummyExtensionDir = 'dummy';
        $this->extension->expects(self::any())
            ->method('getExtensionDir')
            ->willReturn($dummyExtensionDir);
        if (is_dir($dummyExtensionDir)) {
            GeneralUtility::mkdir($dummyExtensionDir);
        }

        $this->extension->setSettings($settings);
        $this->extension->setStoragePath('dummy');

        // get instances to inject in Mocks
        $configurationManager = GeneralUtility::makeInstance(ExtensionBuilderConfigurationManager::class);

        $this->parserService = new ParserService();
        $this->printerService = GeneralUtility::makeInstance(Printer::class);
        $localizationService = GeneralUtility::makeInstance(LocalizationService::class);

        $this->classBuilder = GeneralUtility::makeInstance(ClassBuilder::class);
        $this->classBuilder->initialize($this->extension);

        $this->roundTripService = $this->getAccessibleMock(RoundTrip::class, ['dummy']);
        $this->roundTripService->_set('configurationManager', $configurationManager);
        $this->roundTripService->_set('parserService', $this->parserService);
        $this->roundTripService->initialize($this->extension);

        $this->fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['dummy']);
        $this->fileGenerator->_set('printerService', $this->printerService);
        $this->fileGenerator->_set('localizationService', $localizationService);
        $this->fileGenerator->_set('classBuilder', $this->classBuilder);

        $this->fileGenerator->_set('roundTripService', $this->roundTripService);

        $this->codeTemplateRootPath = Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/';
        $this->modelClassTemplatePath = $this->codeTemplateRootPath . 'Classes/Domain/Model/Model.phpt';

        $this->fileGenerator->setSettings(
            [
                'codeTemplateRootPaths.' => [Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/'],
                'codeTemplatePartialPaths.' => [Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/Partials'],
                'extConf' => [
                    'enableRoundtrip' => '1'
                ]
            ]
        );
        // needed when sub routines in file generator are called without an initial setup
        $this->fileGenerator->_set(
            'codeTemplateRootPaths',
            [Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/']
        );
        $this->fileGenerator->_set(
            'codeTemplatePartialPaths',
            [Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Resources/Private/CodeTemplates/Extbase/Partials']
        );
        $this->fileGenerator->_set('enableRoundtrip', true);
        $this->fileGenerator->_set('extension', $this->extension);

        $this->extensionService = GeneralUtility::makeInstance(ExtensionService::class);
    }

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        if (!empty($this->extension) && $this->extension->getExtensionKey() !== null) {
            GeneralUtility::rmdir($this->extension->getExtensionDir(), true);
        }
    }

    /**
     * Helper function
     *
     * @param $name
     * @param bool $entity
     * @param bool $aggregateRoot
     * @return DomainObject
     */
    protected function buildDomainObject($name, bool $entity = false, bool $aggregateRoot = false): DomainObject
    {
        $domainObject = $this->getAccessibleMock(DomainObject::class, ['dummy']);
        $domainObject->setExtension($this->extension);
        $domainObject->setName($name);
        $domainObject->setEntity($entity);
        $domainObject->setAggregateRoot($aggregateRoot);
        if ($aggregateRoot) {
            $defaultActions = ['list', 'show', 'new', 'create', 'edit', 'update', 'delete'];
            foreach ($defaultActions as $actionName) {
                $action = GeneralUtility::makeInstance(Action::class);
                $action->setName($actionName);
                if ($actionName === 'deleted') {
                    $action->setNeedsTemplate = false;
                }
                $domainObject->addAction($action);
            }
        }
        return $domainObject;
    }

    /**
     * Builds an initial class file to test parsing and modifying of existing classes
     *
     * This class file is generated based on the CodeTemplates
     *
     * @param string $modelName
     *
     * @throws FileNotFoundException
     * @throws SyntaxError
     * @throws Exception
     */
    protected function generateInitialModelClassFile(string $modelName): void
    {
        $domainObject = $this->buildDomainObject($modelName);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        $modelClassDir = 'Classes/Domain/Model/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir() . $modelClassDir);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertDirectoryExists($absModelClassDir, 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        GeneralUtility::writeFile($modelClassPath, $classFileContent);
    }

    /**
     * @throws Exception
     */
    protected function removeInitialModelClassFile(string $modelName): void
    {
        $file = $this->extension->getExtensionDir() . $this->modelClassDir . $modelName . '.php';
        if (@file_exists($file)) {
            unlink($file);
        }
        self::assertFileNotExists($file, 'Dummy file could not be removed:' . $file);
    }
}
