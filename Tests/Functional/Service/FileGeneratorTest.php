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

use EBT\ExtensionBuilder\Domain\Exception\ExtensionException;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Domain\Model\Person;
use EBT\ExtensionBuilder\Domain\Model\Plugin;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use EBT\ExtensionBuilder\Utility\Inflector;
use ReflectionClass;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

class FileGeneratorTest extends BaseFunctionalTest
{
    /**
     * Generate the appropriate code for a simple model class
     * for a non aggregate root domain object with one boolean property
     *
     * @test
     */
    public function generateCodeForModelClassWithBooleanProperty(): void
    {
        $modelName = 'ModelCgt1';
        $propertyName = 'blue';
        $domainObject = $this->buildDomainObject($modelName);

        $property = new BooleanProperty();
        $property->setName($propertyName);
        $property->setRequired(true);
        $domainObject->addProperty($property);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);

        self::assertMatchesRegularExpression(
            '/.*class ModelCgt1.*/',
            $classFileContent,
            'Class declaration was not generated'
        );
        self::assertMatchesRegularExpression(
            '/.*protected \\$blue.*/',
            $classFileContent,
            'protected boolean property was not generated'
        );
        self::assertMatchesRegularExpression(
            '/.*\* @var bool.*/',
            $classFileContent,
            'var tag for boolean property was not generated'
        );
        self::assertMatchesRegularExpression(
            '/.*\* @TYPO3\\\CMS\\\Extbase\\\Annotation\\\Validate\("NotEmpty"\).*/',
            $classFileContent,
            'validate tag for required property was not generated'
        );
        self::assertMatchesRegularExpression(
            '/.*public function getBlue\(\).*/',
            $classFileContent,
            'Getter for boolean property was not generated'
        );
        self::assertMatchesRegularExpression(
            '/.*public function setBlue\(bool \$blue\).*/',
            $classFileContent,
            'Setter for boolean property was not generated'
        );
        self::assertMatchesRegularExpression(
            '/.*public function isBlue\(\).*/',
            $classFileContent,
            'is method for boolean property was not generated'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one boolean property
     *
     * @test
     */
    public function writeModelClassWithBooleanProperty(): void
    {
        $modelName = 'ModelCgt1';
        $propertyName = 'blue';
        $domainObject = $this->buildDomainObject($modelName, true);
        $domainObject->setDescription('This is the model class for ' . $modelName);

        $property = new BooleanProperty($propertyName);
        $property->setRequired(true);
        $domainObject->addProperty($property);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        $modelClassDir = 'Classes/Domain/Model/';
        mkdir($this->extension->getExtensionDir() . $modelClassDir, 0777, true);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertDirectoryExists($absModelClassDir, 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        file_put_contents($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);

        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include_once($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);

        $reflectionService = new ReflectionService(new NullFrontend('extbase'), 'ClassSchemata');
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');
        self::assertTrue($reflection->hasMethod('is' . ucfirst($propertyName)), 'isMethod was not generated');

        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        $parameters = $setterMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in setter method');

        $firstParameterName = current(array_keys($parameters));
        self::assertEquals($propertyName, $firstParameterName, 'Wrong parameter name in setter method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one string property
     *
     * @test
     */
    public function writeModelClassWithStringProperty(): void
    {
        $modelName = 'ModelCgt2';
        $propertyName = 'title';
        $domainObject = $this->buildDomainObject($modelName);
        $property = new StringProperty($propertyName);
        $domainObject->addProperty($property);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        $modelClassDir = 'Classes/Domain/Model/';
        mkdir($this->extension->getExtensionDir() . $modelClassDir, 0777, true);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertDirectoryExists($absModelClassDir, 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        file_put_contents($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);

        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);

        $reflectionService = new ReflectionService(new NullFrontend('extbase'), 'ClassSchemata');
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');
        self::assertFalse($reflection->hasMethod('is' . ucfirst($propertyName)), 'isMethod should not be generated');

        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        $parameters = $setterMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in setter method');

        $firstParameterName = current(array_keys($parameters));
        self::assertEquals($propertyName, $firstParameterName, 'Wrong parameter name in setter method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one to one relation
     *
     * @test
     */
    public function writeModelClassWithZeroToOneRelation(): void
    {
        $modelName = 'ModelCgt3';
        $relatedModelName = 'RelatedModel';
        $propertyName = 'relName';

        $domainObject = $this->buildDomainObject($modelName);
        $relatedDomainObject = $this->buildDomainObject($relatedModelName);

        $relation = new Relation\ZeroToOneRelation($propertyName);
        $relation->setForeignModel($relatedDomainObject);
        $domainObject->addProperty($relation);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        $modelClassDir = 'Classes/Domain/Model/';
        mkdir($this->extension->getExtensionDir() . $modelClassDir, 0777, true);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertTrue(is_dir($absModelClassDir), 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        file_put_contents($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);

        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);

        $relatedClassFileContent = $this->fileGenerator->generateDomainObjectCode($relatedDomainObject);

        $relatedModelClassPath = $absModelClassDir . $relatedDomainObject->getName() . '.php';
        file_put_contents($relatedModelClassPath, $relatedClassFileContent);
        self::assertFileExists($relatedModelClassPath, 'File was not generated: ' . $relatedModelClassPath);
        $relatedClassName = $relatedDomainObject->getFullQualifiedClassName();
        if (!class_exists($relatedClassName)) {
            include($relatedModelClassPath);
        }
        self::assertTrue(class_exists($relatedClassName), 'Class was not generated:' . $relatedClassName);

        $reflectionService = new ReflectionService(new NullFrontend('extbase'), 'ClassSchemata');
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');

        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        $parameters = $setterMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in setter method');

        $firstParameterName = current(array_keys($parameters));
        self::assertEquals($propertyName, $firstParameterName, 'Wrong parameter name in setter method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with zero to many relation
     *
     * @test
     */
    public function writeModelClassWithZeroToManyRelation(): void
    {
        $modelName = 'ModelCgt4';
        $relatedModelName = 'RelatedModel';
        $propertyName = 'relNames';

        $domainObject = $this->buildDomainObject($modelName);
        $relatedDomainObject = $this->buildDomainObject($relatedModelName);

        $relation = new Relation\ZeroToManyRelation($propertyName);
        $relation->setForeignModel($relatedDomainObject);
        $domainObject->addProperty($relation);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        $modelClassDir = 'Classes/Domain/Model/';
        mkdir($this->extension->getExtensionDir() . $modelClassDir, 0777, true);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertDirectoryExists($absModelClassDir, 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        file_put_contents($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);

        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);

        $relatedClassFileContent = $this->fileGenerator->generateDomainObjectCode($relatedDomainObject);

        $relatedModelClassPath = $absModelClassDir . $relatedDomainObject->getName() . '.php';
        file_put_contents($relatedModelClassPath, $relatedClassFileContent);
        self::assertFileExists($relatedModelClassPath, 'File was not generated: ' . $relatedModelClassPath);
        $relatedClassName = $relatedDomainObject->getFullQualifiedClassName();
        if (!class_exists($relatedClassName)) {
            include($relatedModelClassPath);
        }
        self::assertTrue(class_exists($relatedClassName), 'Class was not generated:' . $relatedClassName);

        $reflection = new ReflectionClass(new $className());
        self::assertTrue(
            $reflection->hasMethod('add' . ucfirst(Inflector::singularize($propertyName))),
            'Add method was not generated'
        );
        self::assertTrue(
            $reflection->hasMethod('remove' . ucfirst(Inflector::singularize($propertyName))),
            'Remove method was not generated'
        );
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');

        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        $parameters = $setterMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in setter method');

        $parameter = current($parameters);
        self::assertEquals($parameter->getName(), $propertyName, 'Wrong parameter name in setter method');

        $addMethod = $reflection->getMethod('add' . ucfirst(Inflector::singularize($propertyName)));
        $parameters = $addMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in add method');

        $parameter = current($parameters);
        self::assertEquals(
            Inflector::singularize($propertyName),
            $parameter->getName(),
            'Wrong parameter name in add method'
        );

        $removeMethod = $reflection->getMethod('remove' . ucfirst(Inflector::singularize($propertyName)));
        $parameters = $removeMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in remove method');

        $parameter = current($parameters);
        self::assertEquals(
            Inflector::singularize($propertyName) . 'ToRemove',
            $parameter->getName(),
            'Wrong parameter name in remove method'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one to one relation
     *
     * @test
     */
    public function writeModelClassWithManyToManyRelation(): void
    {
        $modelName = 'ModelCgt5';
        $relatedModelName = 'RelatedModel';
        $propertyName = 'relNames';
        $domainObject = $this->buildDomainObject($modelName);
        $relatedDomainObject = $this->buildDomainObject($relatedModelName);

        $relation = new Relation\ManyToManyRelation($propertyName);
        $relation->setForeignModel($relatedDomainObject);
        $relation->setInlineEditing(false);
        $domainObject->addProperty($relation);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        $modelClassDir = 'Classes/Domain/Model/';
        mkdir($this->extension->getExtensionDir() . $modelClassDir, 0777, true);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertDirectoryExists($absModelClassDir, 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        file_put_contents($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);

        $className = $domainObject->getFullQualifiedClassName();
        include($modelClassPath);
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);

        $reflection = new ReflectionClass(new $className());
        self::assertTrue(
            $reflection->hasMethod('add' . ucfirst(Inflector::singularize($propertyName))),
            'Add method was not generated'
        );
        self::assertTrue(
            $reflection->hasMethod('remove' . ucfirst(Inflector::singularize($propertyName))),
            'Remove method was not generated'
        );
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');
        self::assertTrue($reflection->hasMethod('initializeObject'), 'initializeObject was not generated');

        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        $parameters = $setterMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in setter method');

        $parameter = current($parameters);
        self::assertEquals($parameter->getName(), $propertyName, 'Wrong parameter name in setter method');

        $addMethod = $reflection->getMethod('add' . ucfirst(Inflector::singularize($propertyName)));
        $parameters = $addMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in add method');

        $parameter = current($parameters);
        self::assertEquals(
            Inflector::singularize($propertyName),
            $parameter->getName(),
            'Wrong parameter name in add method'
        );

        $removeMethod = $reflection->getMethod('remove' . ucfirst(Inflector::singularize($propertyName)));
        $parameters = $removeMethod->getParameters();
        self::assertCount(1, $parameters, 'Wrong parameter count in remove method');

        $parameter = current($parameters);
        self::assertEquals(
            Inflector::singularize($propertyName) . 'ToRemove',
            $parameter->getName(),
            'Wrong parameter name in remove method'
        );
    }

    /**
     * Write a simple model class for a non aggregate root domain object
     *
     * @test
     */
    public function writeSimpleControllerClassFromDomainObject(): void
    {
        $domainObject = $this->buildDomainObject('ModelCgt6', true);

        $action = GeneralUtility::makeInstance(Action::class);
        $action->setName('list');
        $domainObject->addAction($action);

        $classFileContent = $this->fileGenerator->generateActionControllerCode($domainObject);

        $controllerClassDir = 'Classes/Controller/';
        mkdir($this->extension->getExtensionDir() . $controllerClassDir, 0777, true);
        $absControllerClassDir = $this->extension->getExtensionDir() . $controllerClassDir;
        self::assertDirectoryExists($absControllerClassDir, 'Directory ' . $absControllerClassDir . ' was not created');

        $controllerClassPath = $absControllerClassDir . $domainObject->getName() . 'Controller.php';
        file_put_contents($controllerClassPath, $classFileContent);
        self::assertFileExists($controllerClassPath, 'File was not generated: ' . $controllerClassPath);

        $className = $domainObject->getControllerClassName();
        if (!class_exists($className)) {
            include($controllerClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);
    }

    /**
     * Write a simple model class for a non aggregate root domain object
     *
     * @test
     */
    public function writeRepositoryClassFromDomainObject(): void
    {
        $domainObject = $this->buildDomainObject('ModelCgt6', true, true);
        $classFileContent = $this->fileGenerator->generateDomainRepositoryCode($domainObject);

        $repositoryClassDir = 'Classes/Domain/Repository/';
        mkdir($this->extension->getExtensionDir() . $repositoryClassDir, 0777, true);
        $absRepositoryClassDir = $this->extension->getExtensionDir() . $repositoryClassDir;
        self::assertDirectoryExists($absRepositoryClassDir, 'Directory ' . $absRepositoryClassDir . ' was not created');

        $repositoryClassPath = $absRepositoryClassDir . $domainObject->getName() . 'Repository.php';
        file_put_contents($repositoryClassPath, $classFileContent);
        self::assertFileExists($repositoryClassPath, 'File was not generated: ' . $repositoryClassPath);

        $className = $domainObject->getFullyQualifiedDomainRepositoryClassName();
        if (!class_exists($className)) {
            include($repositoryClassPath);
        }
        self::assertTrue(
            class_exists($className),
            'Class was not generated:' . $className . 'in ' . $repositoryClassPath
        );
    }

    /**
     * This test is too generic, since it creates the required classes
     * with a whole fileGenerator->build call
     *
     *
     * @test
     */
    public function writeAggregateRootClassesFromDomainObject(): void
    {
        $domainObject = $this->buildDomainObject('ModelCgt7', true, true);

        $property = new BooleanProperty('blue');
        $property->setRequired(true);
        $domainObject->addProperty($property);

        $this->extension->addDomainObject($domainObject);

        $this->fileGenerator->build($this->extension);

        $extensionDir = $this->extension->getExtensionDir();

        self::assertFileExists($extensionDir . 'Classes/Controller/' . $domainObject->getName() . 'Controller.php');
        self::assertFileExists($extensionDir . 'Classes/Domain/Model/' . $domainObject->getName() . '.php');
        self::assertFileExists($extensionDir . 'Classes/Domain/Repository/' . $domainObject->getName() . 'Repository.php');
    }

    /**
     * Verify that ext_tables.sql contains a CREATE TABLE for a child model that has no own properties
     * but is the target of a ZeroToMany inline relation (requires FK column in its table).
     *
     * @test
     */
    public function extTablesSqlContainsForeignKeyForChildModelWithNoProperties(): void
    {
        $ownerModelName = 'OwnerModel';
        $childModelName = 'ChildModel';
        $relationName = 'children';

        $ownerDomainObject = $this->buildDomainObject($ownerModelName, true, true);
        $childDomainObject = $this->buildDomainObject($childModelName, true);

        $property = new BooleanProperty('active');
        $ownerDomainObject->addProperty($property);

        $relation = new Relation\ZeroToManyRelation($relationName);
        $relation->setForeignModel($childDomainObject);
        $relation->setRenderType('inline');
        $ownerDomainObject->addProperty($relation);

        $this->extension->addDomainObject($ownerDomainObject);
        $this->extension->addDomainObject($childDomainObject);

        $this->fileGenerator->build($this->extension);

        $sqlFile = $this->extension->getExtensionDir() . 'ext_tables.sql';
        self::assertFileExists($sqlFile, 'ext_tables.sql was not generated');

        $sqlContent = file_get_contents($sqlFile);
        self::assertStringContainsString(
            'CREATE TABLE ' . $childDomainObject->getDatabaseTableName(),
            $sqlContent,
            'ext_tables.sql must contain CREATE TABLE for child model with no own properties'
        );
        // FK column name is derived from the owner model name (lowercase)
        $expectedFkColumn = strtolower($ownerModelName);
        self::assertStringContainsString(
            $expectedFkColumn,
            $sqlContent,
            'ext_tables.sql must contain the FK column for the child model'
        );
    }

    /**
     * @test
     */
    public function writeExtensionFiles(): void
    {
        $modelName = 'ModelCgt8';
        $relatedModelName = 'RelatedModel';
        $propertyName = 'relNames';
        $domainObject = $this->buildDomainObject($modelName, true, true);

        $relatedDomainObject = $this->buildDomainObject($relatedModelName, true);

        $relation = new Relation\ManyToManyRelation($propertyName);
        $relation->setForeignModel($relatedDomainObject);
        $relation->setInlineEditing(false);
        $domainObject->addProperty($relation);

        $property = new BooleanProperty('title');
        $domainObject->addProperty($property);

        $this->extension->addDomainObject($domainObject);
        $this->extension->addDomainObject($relatedDomainObject);

        $plugin = new Plugin();
        $plugin->setName('Test');
        $plugin->setKey('test');
        $this->extension->addPlugin($plugin);

        $this->extension->setGenerateEmptyGitRepository(true);
        $this->extension->setGenerateEditorConfig(true);

        $this->fileGenerator->build($this->extension);

        $extensionDir = $this->extension->getExtensionDir();

        $extensionFiles = [
            '.git/config',
            '.editorconfig',
            '.gitattributes',
            '.gitignore',
            'ext_emconf.php',
            'ext_localconf.php',
            'ext_tables.sql',
            'Configuration/ExtensionBuilder/settings.yaml',
            'Configuration/TCA/Overrides/sys_template.php',
            'Configuration/TCA/Overrides/tt_content.php',
            'Configuration/TCA/' . $domainObject->getDatabaseTableName() . '.php',
            'Configuration/TypoScript/constants.typoscript',
            'Configuration/TypoScript/setup.typoscript',
            'Configuration/Icons.php',
            'Resources/Private/Partials/' . $domainObject->getName() . '/Properties.html',
            'Resources/Private/Partials/' . $domainObject->getName() . '/FormFields.html',
            'Resources/Private/Language/locallang.xlf',
            'Resources/Private/Language/locallang_db.xlf',
        ];
        foreach ($extensionFiles as $extensionFile) {
            self::assertFileExists($extensionDir . $extensionFile, 'File was not generated: ' . $extensionFile);
        }
    }

    /**
     * @test
     */
    public function generateSiteSetFilesWhenActive(): void
    {
        $plugin = new Plugin();
        $plugin->setName('Test');
        $plugin->setKey('test');
        $this->extension->addPlugin($plugin);
        $this->extension->setGenerateSiteSet(true);

        $this->fileGenerator->build($this->extension);

        $extensionDir = $this->extension->getExtensionDir();
        $extensionName = $this->extension->getExtensionName();
        $setsDir = 'Configuration/Sets/' . $extensionName . '/';

        self::assertFileExists($extensionDir . $setsDir . 'config.yaml');
        self::assertFileExists($extensionDir . $setsDir . 'setup.typoscript');
        self::assertFileExists($extensionDir . $setsDir . 'constants.typoscript');
        self::assertFileDoesNotExist($extensionDir . 'Configuration/TCA/Overrides/sys_template.php');
    }

    /**
     * @test
     */
    public function generateClassicFilesWhenSiteSetInactive(): void
    {
        $plugin = new Plugin();
        $plugin->setName('Test');
        $plugin->setKey('test');
        $this->extension->addPlugin($plugin);
        $this->extension->setGenerateSiteSet(false);

        $this->fileGenerator->build($this->extension);

        $extensionDir = $this->extension->getExtensionDir();

        self::assertFileExists($extensionDir . 'Configuration/TCA/Overrides/sys_template.php');
        self::assertFileExists($extensionDir . 'Configuration/TypoScript/setup.typoscript');
        self::assertFileExists($extensionDir . 'Configuration/TypoScript/constants.typoscript');
        self::assertDirectoryDoesNotExist($extensionDir . 'Configuration/Sets');
    }

    public static function getDeprecatedTypoScriptExtensionsDataProvider(): array
    {
        return [['ts'], ['txt']];
    }

    /**
     * @test
     * @dataProvider getDeprecatedTypoScriptExtensionsDataProvider
     * @param string $deprecatedExtension
     * @throws ExtensionException
     */
    public function writeExtensionFilesOverWritesFilesWithDeprecatedExtensions(string $deprecatedExtension): void
    {
        $plugin = new Plugin();
        $plugin->setName('Test');
        $plugin->setKey('test');
        $this->extension->addPlugin($plugin);

        $setupFile = implode(
            DIRECTORY_SEPARATOR,
            [$this->extension->getExtensionDir(), 'Configuration', 'TypoScript', 'setup.' . $deprecatedExtension]
        );

        mkdir(dirname($setupFile), 0777, true);
        file_put_contents($setupFile, '# some sample content');

        $this->fileGenerator->build($this->extension);

        $extensionDir = $this->extension->getExtensionDir();

        self::assertFileExists($extensionDir . 'Configuration/TypoScript/setup.' . $deprecatedExtension);
        self::assertFileDoesNotExist($extensionDir . 'Configuration/TypoScript/setup.typoscript');
    }

    /**
     * @test
     */
    public function generateComposerJsonWritesAuthorEmailOnFirstBuild(): void
    {
        $person = (new Person())->setName('John Doe')->setEmail('john@example.com')->setRole('developer');
        $this->extension->setPersons([$person]);

        $this->fileGenerator->build($this->extension);

        $composerFile = $this->extension->getExtensionDir() . 'composer.json';
        self::assertFileExists($composerFile);

        $data = json_decode(file_get_contents($composerFile), true);
        self::assertSame('John Doe', $data['authors'][0]['name']);
        self::assertSame('john@example.com', $data['authors'][0]['email']);
        self::assertSame('developer', $data['authors'][0]['role']);
    }

    /**
     * @test
     */
    public function generateComposerJsonWritesAllAuthors(): void
    {
        $person1 = (new Person())->setName('Alice')->setEmail('alice@example.com');
        $person2 = (new Person())->setName('Bob')->setEmail('bob@example.com');
        $this->extension->setPersons([$person1, $person2]);

        $this->fileGenerator->build($this->extension);

        $data = json_decode(file_get_contents($this->extension->getExtensionDir() . 'composer.json'), true);
        self::assertCount(2, $data['authors']);
        self::assertSame('Alice', $data['authors'][0]['name']);
        self::assertSame('alice@example.com', $data['authors'][0]['email']);
        self::assertSame('Bob', $data['authors'][1]['name']);
        self::assertSame('bob@example.com', $data['authors'][1]['email']);
    }

    /**
     * @test
     */
    public function generateComposerJsonUpdatesAuthorsOnRebuildWithoutOverwritingManualRequire(): void
    {
        // First build: creates composer.json
        $person = (new Person())->setName('Original Author')->setEmail('original@example.com');
        $this->extension->setPersons([$person]);
        $this->fileGenerator->build($this->extension);

        // Simulate developer adding a manual require entry
        $composerFile = $this->extension->getExtensionDir() . 'composer.json';
        $data = json_decode(file_get_contents($composerFile), true);
        $data['require']['vendor/some-package'] = '^1.0';
        file_put_contents($composerFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

        // Second build: add a new author
        $person2 = (new Person())->setName('New Author')->setEmail('new@example.com');
        $this->extension->setPersons([$person, $person2]);
        $this->fileGenerator->build($this->extension);

        $updated = json_decode(file_get_contents($composerFile), true);

        // Authors are updated
        self::assertCount(2, $updated['authors']);
        self::assertSame('Original Author', $updated['authors'][0]['name']);
        self::assertSame('New Author', $updated['authors'][1]['name']);

        // Manually added require is preserved
        self::assertArrayHasKey('vendor/some-package', $updated['require']);
        self::assertSame('^1.0', $updated['require']['vendor/some-package']);
    }
}
