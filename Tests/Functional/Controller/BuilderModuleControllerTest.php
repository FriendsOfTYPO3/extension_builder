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

namespace EBT\ExtensionBuilder\Tests\Functional\Controller;

use CallbackFilterIterator;
use DirectoryIterator;
use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Controller\BuilderModuleController;
use EBT\ExtensionBuilder\Domain\Repository\ExtensionRepository;
use EBT\ExtensionBuilder\Domain\Validator\ExtensionValidator;
use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Parser\NodeFactory;
use EBT\ExtensionBuilder\Service\ClassBuilder;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Service\ExtensionService;
use EBT\ExtensionBuilder\Service\FileGenerator;
use EBT\ExtensionBuilder\Service\LocalizationService;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Service\Printer;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use EBT\ExtensionBuilder\Utility\ExtensionInstallationStatus;
use PHPUnit\Framework\MockObject\MockObject;
use SplFileInfo;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

class BuilderModuleControllerTest extends BaseFunctionalTest
{
    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \EBT\ExtensionBuilder\Domain\Exception\ExtensionException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @throws \TYPO3\TestingFramework\Core\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpBackendUserFromFixture(1);
    }

    /**
     * @test
     */
    public function rpcActionSaveCreatesExtensionOnFirstSave(): void
    {
        $subject = $this->createBuilderModuleControllerMockForRpcActionSave();

        $this->assertDirectoryNotExists(Environment::getProjectPath() . '/packages/my_test_extension');
        $this->assertDirectoryNotExists(Environment::getProjectPath() . '/var/tx_extensionbuilder/backups/my_test_extension');

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayNotHasKey('warning', $result);
        $this->assertArrayNotHasKey('error', $result);
        $this->assertDirectoryExists(Environment::getProjectPath() . '/packages/my_test_extension');
        $this->assertDirectoryIsReadable(Environment::getProjectPath() . '/packages/my_test_extension');
        $this->assertDirectoryIsWritable(Environment::getProjectPath() . '/packages/my_test_extension');
        $this->assertFileExists(Environment::getProjectPath() . '/packages/my_test_extension/composer.json');
        $this->assertFileExists(Environment::getProjectPath() . '/packages/my_test_extension/ext_emconf.php');
        $this->assertFileExists(Environment::getProjectPath() . '/packages/my_test_extension/ext_tables.php');
        $this->assertFileExists(Environment::getProjectPath() . '/packages/my_test_extension/ExtensionBuilder.json');
        $this->assertDirectoryExists(Environment::getProjectPath() . '/packages/my_test_extension/Configuration');
        $this->assertDirectoryExists(Environment::getProjectPath() . '/packages/my_test_extension/Resources');
        $this->assertDirectoryNotExists(Environment::getProjectPath() . '/var/tx_extensionbuilder/backups/my_test_extension');
    }

    /**
     * @test
     * @depends rpcActionSaveCreatesExtensionOnFirstSave
     */
    public function rpcActionSaveBackupsExtensionOnSecondSave(): void
    {
        $subject = $this->createBuilderModuleControllerMockForRpcActionSave();

        $this->assertDirectoryExists(Environment::getProjectPath() . '/packages/my_test_extension');
        $this->assertDirectoryNotExists(Environment::getProjectPath() . '/var/tx_extensionbuilder/backups');

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayNotHasKey('warning', $result);
        $this->assertArrayNotHasKey('error', $result);
        $this->assertDirectoryExists(Environment::getProjectPath() . '/packages/my_test_extension');
        $this->assertDirectoryExists(Environment::getProjectPath() . '/var/tx_extensionbuilder/backups/my_test_extension');

        /** @var SplFileInfo $fileInfo */
        $directoryIterator = new DirectoryIterator(Environment::getProjectPath() . '/var/tx_extensionbuilder/backups/my_test_extension');
        $subDirectoryIterator = new CallbackFilterIterator($directoryIterator, function ($fileInfo) {
            return $fileInfo->isDir() && $fileInfo->getFilename() !== '.' && $fileInfo->getFilename() !== '..';
        });
        $this->assertEquals(1, iterator_count($subDirectoryIterator));
        foreach ($subDirectoryIterator as $fileInfo) {
            $this->assertDirectoryIsReadable($fileInfo->getPathname());
            $this->assertDirectoryIsWritable($fileInfo->getPathname());
            $this->assertFileExists($fileInfo->getPathname() . '/composer.json');
            $this->assertFileExists($fileInfo->getPathname() . '/ext_emconf.php');
            $this->assertFileExists($fileInfo->getPathname() . '/ext_tables.php');
            $this->assertFileExists($fileInfo->getPathname() . '/ExtensionBuilder.json');
            $this->assertDirectoryExists($fileInfo->getPathname() . '/Configuration');
            $this->assertDirectoryExists($fileInfo->getPathname() . '/Resources');
        }
    }

    /**
     * @test
     * @depends rpcActionSaveBackupsExtensionOnSecondSave
     */
    public function rpcActionSaveCreatesSettingsYamlAndPreventsSavingIfFileIsMissing(): void
    {
        $subject = $this->createBuilderModuleControllerMockForRpcActionSave();
        $fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['build']);
        $fileGenerator->expects(self::never())->method('build');
        $subject->injectFileGenerator($fileGenerator);

        unlink(Environment::getProjectPath() . '/packages/my_test_extension/Configuration/ExtensionBuilder/settings.yaml');
        $this->assertFileNotExists(Environment::getProjectPath() . '/packages/my_test_extension/Configuration/ExtensionBuilder/settings.yaml');

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayNotHasKey('success', $result);
        $this->assertArrayHasKey('warning', $result);
        $this->assertStringContainsString('Roundtrip is enabled but no configuration file was found.', $result['warning']);
        // TODO: Streamline extension path calculation.
        // $this->assertStringContainsString('packages/my_test_extension', $result['warning']);
        $this->assertFileExists(Environment::getProjectPath() . '/packages/my_test_extension/Configuration/ExtensionBuilder/settings.yaml');
    }

    /**
     * @test
     * @depends rpcActionSaveBackupsExtensionOnSecondSave
     */
    public function rpcActionSaveRequiresConfirmationIfRewritingFullExtension(): void
    {
        $subject = $this->createBuilderModuleControllerMockForRpcActionSave(['enableRoundtrip' => '0']);
        $fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['build']);
        $fileGenerator->expects(self::never())->method('build');
        $subject->injectFileGenerator($fileGenerator);

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayNotHasKey('success', $result);
        $this->assertArrayHasKey('confirm', $result);
        $this->assertStringContainsString('This action will overwrite previously saved content!', $result['confirm']);
    }

    /**
     * @test
     * @depends rpcActionSaveBackupsExtensionOnSecondSave
     */
    public function rpcActionSaveRequiresConfirmationIfExtensionConfigurationThrowsWarning(): void
    {
        $requestDataParamsWorking = json_decode($this->getDefaultRequestDataForRpcActionSave()['params']['working'], true);
        $requestDataParamsWorking = self::mergeRecursiveWithOverrule($requestDataParamsWorking, [
            'properties' => [
                'plugins' => [
                    [
                        'actions' => [
                            'controllerActionCombinations' => 'ParentModel => list => too many arrows',
                        ],
                    ],
                ],
            ],
        ]);

        $subject = $this->createBuilderModuleControllerMockForRpcActionSave(
            [],
            [],
            ['params' => ['working' => json_encode($requestDataParamsWorking)]]
        );
        $fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['build']);
        $fileGenerator->expects(self::never())->method('build');
        $subject->injectFileGenerator($fileGenerator);

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayNotHasKey('success', $result);
        $this->assertArrayHasKey('confirm', $result);
        $this->assertStringContainsString('Wrong format in configuration for controllerActionCombinations in plugin My Plugin', $result['confirm']);
    }

    /**
     * @test
     * @depends rpcActionSaveBackupsExtensionOnSecondSave
     */
    public function rpcActionSaveFailsIfExtensionConfigurationThrowsError(): void
    {
        $requestDataParamsWorking = json_decode($this->getDefaultRequestDataForRpcActionSave()['params']['working'], true);
        $requestDataParamsWorking = self::mergeRecursiveWithOverrule($requestDataParamsWorking, [
            'modules' => [
                [
                    'value' => [
                        'propertyGroup' => [
                            'properties' => [
                                1 => [
                                    'propertyName' => 'name',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $subject = $this->createBuilderModuleControllerMockForRpcActionSave(
            [],
            [],
            ['params' => ['working' => json_encode($requestDataParamsWorking)]]
        );
        $fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['build']);
        $fileGenerator->expects(self::never())->method('build');
        $subject->injectFileGenerator($fileGenerator);

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayNotHasKey('success', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Property "name" of Model "ParentModel" exists twice.', $result['error']);
    }

    /**
     * @test
     * @depends rpcActionSaveBackupsExtensionOnSecondSave
     */
    public function rpcActionSaveRequiresConfirmationIfExtensionThrowsWarning(): void
    {
        $requestDataParamsWorking = json_decode($this->getDefaultRequestDataForRpcActionSave()['params']['working'], true);
        $requestDataParamsWorking = self::mergeRecursiveWithOverrule($requestDataParamsWorking, [
            'properties' => [
                'plugins' => [
                    [
                        'actions' => [
                            'controllerActionCombinations' => 'ParentModel => show',
                        ],
                    ],
                ],
            ],
        ]);

        $subject = $this->createBuilderModuleControllerMockForRpcActionSave(
            [],
            [],
            ['params' => ['working' => json_encode($requestDataParamsWorking)]]
        );
        $fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['build']);
        $fileGenerator->expects(self::never())->method('build');
        $subject->injectFileGenerator($fileGenerator);

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayNotHasKey('success', $result);
        $this->assertArrayHasKey('confirm', $result);
        $this->assertStringContainsString('Default action ParentModel-&gt;show  can not be called without a domain object parameter', $result['confirm']);
    }

    /**
     * @test
     * @depends rpcActionSaveBackupsExtensionOnSecondSave
     */
    public function rpcActionSaveFailsIfExtensionThrowsError(): void
    {
        $requestDataParamsWorking = json_decode($this->getDefaultRequestDataForRpcActionSave()['params']['working'], true);
        $requestDataParamsWorking = self::mergeRecursiveWithOverrule($requestDataParamsWorking, [
            'properties' => [
                'extensionKey' => 'my_test_extension_$',
            ],
        ]);

        $subject = $this->createBuilderModuleControllerMockForRpcActionSave(
            [],
            [],
            ['params' => ['working' => json_encode($requestDataParamsWorking)]]
        );
        $fileGenerator = $this->getAccessibleMock(FileGenerator::class, ['build']);
        $fileGenerator->expects(self::never())->method('build');
        $subject->injectFileGenerator($fileGenerator);

        /** @var JsonResponse $response */
        $subject->_call('initializeAction');
        $response = $subject->_call('dispatchRpcAction');
        $result = json_decode((string)$response->getBody(), true);

        $this->assertArrayNotHasKey('success', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Illegal characters in extension key', $result['error']);
    }

    /**
     * @param array $extensionConfiguration
     * @param array $typoScriptSettings
     * @param array $requestData
     * @return BuilderModuleController|MockObject|AccessibleObjectInterface
     */
    protected function createBuilderModuleControllerMockForRpcActionSave(
        array $extensionConfiguration = [],
        array $typoScriptSettings = [],
        array $requestData = []
    ): BuilderModuleController {
        $extensionConfiguration = self::mergeRecursiveWithOverrule($this->getDefaultExtensionConfiguration(), $extensionConfiguration);
        $typoScriptSettings = self::mergeRecursiveWithOverrule($this->getDefaultTypoScriptSettings(), $typoScriptSettings);
        $requestData = self::mergeRecursiveWithOverrule($this->getDefaultRequestDataForRpcActionSave(), $requestData);
        return $this->createBuilderModuleControllerMock($extensionConfiguration, $typoScriptSettings, $requestData);
    }

    /**
     * @param array $extensionConfiguration
     * @param array $typoScriptSettings
     * @param array $requestData
     * @return BuilderModuleController|MockObject|AccessibleObjectInterface
     */
    protected function createBuilderModuleControllerMock(
        array $extensionConfiguration,
        array $typoScriptSettings,
        array $requestData
    ): BuilderModuleController {
        $configurationManager = $this->createMock(ConfigurationManager::class);
        $configurationManager->expects(self::any())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)
            ->willReturn($typoScriptSettings);

        $extensionBuilderConfigurationManager = $this->getAccessibleMock(
            ExtensionBuilderConfigurationManager::class,
            ['getExtensionBuilderSettings', 'parseRequest']
        );
        $extensionBuilderConfigurationManager->injectConfigurationManager($configurationManager);
        $extensionBuilderConfigurationManager->expects(self::any())
            ->method('getExtensionBuilderSettings')
            ->willReturn($extensionConfiguration);
        $extensionBuilderConfigurationManager->_set('inputData', $requestData);

        $extensionService = new ExtensionService();
        $extensionService->injectExtensionBuilderConfigurationManager($extensionBuilderConfigurationManager);

        $extensionValidator = new ExtensionValidator();
        $extensionValidator->injectExtensionBuilderConfigurationManager($extensionBuilderConfigurationManager);

        $objectSchemaBuilder = new ObjectSchemaBuilder();
        $objectSchemaBuilder->injectConfigurationManager($extensionBuilderConfigurationManager);

        $extensionSchemaBuilder = new ExtensionSchemaBuilder();
        $extensionSchemaBuilder->injectConfigurationManager($extensionBuilderConfigurationManager);
        $extensionSchemaBuilder->injectObjectSchemaBuilder($objectSchemaBuilder);

        $printerService = new Printer();
        $printerService->injectNodeFactory(new NodeFactory());

        $classBuilder = new ClassBuilder();
        $classBuilder->injectConfigurationManager($extensionBuilderConfigurationManager);
        $classBuilder->injectClassFactory(new ClassFactory());
        $classBuilder->injectParserService(new ParserService());
        $classBuilder->injectPrinterService($printerService);

        $fileGenerator = new FileGenerator();
        $fileGenerator->injectRoundTripService($this->roundTripService);
        $fileGenerator->injectLocalizationService(new LocalizationService());
        $fileGenerator->injectClassBuilder($classBuilder);
        $fileGenerator->injectPrinterService($printerService);

        $extensionRepository = new ExtensionRepository();
        $extensionRepository->injectExtensionBuilderConfigurationManager($extensionBuilderConfigurationManager);

        $subject = $this->getAccessibleMock(BuilderModuleController::class, ['dummy'], [], '', false);
        $subject->injectExtensionBuilderConfigurationManager($extensionBuilderConfigurationManager);
        $subject->injectExtensionService($extensionService);
        $subject->injectExtensionValidator($extensionValidator);
        $subject->injectExtensionSchemaBuilder($extensionSchemaBuilder);
        $subject->injectFileGenerator($fileGenerator);
        $subject->injectExtensionInstallationStatus(new ExtensionInstallationStatus());
        $subject->injectExtensionRepository($extensionRepository);
        $subject->injectResponseFactory(new ResponseFactory());
        $subject->injectStreamFactory(new StreamFactory());

        return $subject;
    }

    protected function getDefaultExtensionConfiguration(): array
    {
        return [
            'storageDir' => Environment::getProjectPath() . '/packages/',
            'backupDir' => 'var/tx_extensionbuilder/backups',
            'backupExtension' => '1',
            'enableRoundtrip' => '1',
        ];
    }

    protected function getDefaultTypoScriptSettings(): array
    {
        return [
            'module.' => [
                'extension_builder.' => [
                    'settings.' => [
                        'codeTemplateRootPaths.' => ['EXT:extension_builder/Resources/Private/CodeTemplates/Extbase/'],
                        'codeTemplatePartialPaths.' => ['EXT:extension_builder/Resources/Private/CodeTemplates/Extbase/Partials'],
                    ],
                ],
            ],
        ];
    }

    protected function getDefaultRequestDataForRpcActionSave(): array
    {
        return [
            'id' => 1,
            'method' => 'saveWiring',
            'params' => [
                'language' => 'extbaseModeling',
                'name' => 'My Test Extension',
                'working' => json_encode([
                    'modules' => [
                        [
                            'config' => [
                                'position' => [
                                    110,
                                    128,
                                ],
                            ],
                            'name' => 'New Model Object',
                            'value' => [
                                'actionGroup' => [
                                    '_default0_index' => true,
                                    '_default1_list' => true,
                                    '_default2_show' => true,
                                    '_default3_new_create' => true,
                                    '_default4_edit_update' => true,
                                    '_default5_delete' => true,
                                    'customActions' => [
                                         'custom',
                                    ],
                                ],
                                'name' => 'ParentModel',
                                'objectsettings' => [
                                    'addDeletedField' => true,
                                    'addHiddenField' => true,
                                    'addStarttimeEndtimeFields' => true,
                                    'aggregateRoot' => true,
                                    'categorizable' => false,
                                    'description' => '',
                                    'mapToTable' => '',
                                    'parentClass' => '',
                                    'sorting' => false,
                                    'type' => 'Entity',
                                    'uid' => '73397978883',
                                ],
                                'propertyGroup' => [
                                    'properties' => [
                                        [
                                            'allowedFileTypes' => '',
                                            'maxItems' => '1',
                                            'propertyDescription' => '',
                                            'propertyIsExcludeField' => true,
                                            'propertyIsL10nModeExclude' => false,
                                            'propertyIsNullable' => false,
                                            'propertyIsRequired' => false,
                                            'propertyName' => 'name',
                                            'propertyType' => 'String',
                                            'uid' => '426723298956',
                                        ],
                                    ],
                                ],
                                'relationGroup' => [
                                    'relations' => [
                                        [
                                            'foreignRelationClass' => '',
                                            'lazyLoading' => false,
                                            'propertyIsExcludeField' => true,
                                            'relationDescription' => '',
                                            'relationName' => 'children',
                                            'relationType' => 'zeroToMany',
                                            'relationWire' => '[wired]',
                                            'renderType' => 'inline',
                                            'uid' => '136104124275',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'config' => [
                                'position' => [
                                     600,
                                     157,
                                ],
                            ],
                            'name' => 'New Model Object',
                            'value' => [
                                'actionGroup' => [
                                    '_default0_index' => false,
                                    '_default1_list' => false,
                                    '_default2_show' => false,
                                    '_default3_new_create' => false,
                                    '_default4_edit_update' => false,
                                    '_default5_delete' => false,
                                    'customActions' => [],
                                ],
                                'name' => 'ChildModel',
                                'objectsettings' => [
                                    'addDeletedField' => true,
                                    'addHiddenField' => true,
                                    'addStarttimeEndtimeFields' => true,
                                    'aggregateRoot' => false,
                                    'categorizable' => false,
                                    'description' => '',
                                    'mapToTable' => '',
                                    'parentClass' => '',
                                    'sorting' => false,
                                    'type' => 'Entity',
                                    'uid' => '1486964833132',
                                ],
                                'propertyGroup' => [
                                    'properties' => [
                                        [
                                            'allowedFileTypes' => '',
                                            'maxItems' => '1',
                                            'propertyDescription' => '',
                                            'propertyIsExcludeField' => true,
                                            'propertyIsL10nModeExclude' => false,
                                            'propertyIsNullable' => false,
                                            'propertyIsRequired' => false,
                                            'propertyName' => 'name',
                                            'propertyType' => 'String',
                                            'uid' => '935539835891',
                                        ],
                                    ],
                                ],
                                'relationGroup' => [
                                    'relations' => [],
                                ],
                            ],
                        ],
                    ],
                    'properties' => [
                        'backendModules' => [
                            [
                                'actions' => [
                                    'controllerActionCombinations' => 'ParentModel => new,create,edit,update,delete,custom',
                                ],
                                'description' => '',
                                'key' => 'mybackendmodule',
                                'mainModule' => 'tools',
                                'name' => 'My Backend Module',
                                'tabLabel' => 'My Backend Module',
                            ],
                        ],
                        'description' => '',
                        'emConf' => [
                            'category' => 'plugin',
                            'custom_category' => '',
                            'dependsOn' => 'typo3 => 11.5.0-11.5.99',
                            'disableLocalization' => false,
                            'disableVersioning' => false,
                            'generateDocumentationTemplate' => false,
                            'generateEditorConfig' => false,
                            'generateEmptyGitRepository' => false,
                            'sourceLanguage' => 'en',
                            'state' => 'alpha',
                            'targetVersion' => '11.5.0-11.5.99',
                            'version' => '1.0.0',
                        ],
                        'extensionKey' => 'my_test_extension',
                        'name' => 'My Test Extension',
                        'originalExtensionKey' => '',
                        'originalVendorName' => '',
                        'persons' => [
                            [
                                'company' => '',
                                'email' => 'typo3@alexandernitsche.com',
                                'name' => 'Alexander Nitsche',
                                'role' => 'Developer',
                            ],
                        ],
                        'plugins' => [
                            [
                                'actions' => [
                                    'controllerActionCombinations' => 'ParentModel => index,list,show',
                                    'noncacheableActions' => '',
                                ],
                                'description' => '',
                                'key' => 'myplugin',
                                'name' => 'My Plugin',
                            ],
                        ],
                        'vendorName' => 'MyTestExtension',
                    ],
                    'wires' => [
                        [
                            'src' => [
                                'moduleId' => 0,
                                'terminal' => 'relationWire_0',
                                'uid' => '136104124275',
                            ],
                            'tgt' => [
                                'moduleId' => 1,
                                'terminal' => 'SOURCES',
                                'uid' => '1486964833132',
                            ],
                        ],
                    ],
                ]),
            ],
            'version' => 'json-rpc-2.0',
        ];
    }

    /**
     * Merges two arrays recursively, with similar values in the original array being overwritten by the values of the
     * overrule array.
     *
     * The differences to the existing PHP function array_merge_recursive() are:
     *  * Elements of the original array get replaced instead of merged if the same key is present in the overrule array.
     *  * Both types of keys are compared: Numbers and strings.
     *  * Keys of the original array can be unset. ($enableUnsetFeature)
     *  * Much more control over what is actually merged. ($addKeys, $includeEmptyValues)
     *
     * This utility function is a slightly customized copy of the \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule
     * to avoid unnecessary reliance on production functions in tests.
     *
     * @param array $original The original array.
     * @param array $overrule The overrule array.
     * @param bool $addKeys If set to FALSE, keys that are NOT found in the original array will not be set. Thus only existing value can/will be overruled from overrule array.
     * @param bool $includeEmptyValues If set, empty simple values of the overrule array will also overrule.
     * @param bool $enableUnsetFeature If set, special values "__UNSET" can be used in the overrule array to unset array keys in the original array.
     */
    public static function mergeRecursiveWithOverrule(
        array $original,
        array $overrule,
        bool $addKeys = true,
        bool $includeEmptyValues = true,
        bool $enableUnsetFeature = true
    ): array {
        $result = $original;

        foreach ($overrule as $key => $_) {
            if ($enableUnsetFeature && $_ === '__UNSET') {
                unset($result[$key]);
            } elseif (isset($result[$key]) && is_array($result[$key])) {
                if (is_array($_)) {
                    $result[$key] = self::mergeRecursiveWithOverrule($result[$key], $_, $addKeys, $includeEmptyValues, $enableUnsetFeature);
                }
            } elseif ((isset($result[$key]) || $addKeys) && (!empty($_) || $includeEmptyValues)) {
                $result[$key] = $_;
            }
        }

        return $result;
    }
}
