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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Domain\Model\Plugin;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use EBT\ExtensionBuilder\Utility\Inflector;
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
    public function generateCodeForModelClassWithBooleanProperty()
    {
        $modelName = 'ModelCgt1';
        $propertyName = 'blue';
        $domainObject = $this->buildDomainObject($modelName);
        $property = new BooleanProperty();
        $property->setName($propertyName);
        $property->setRequired(true);
        $domainObject->addProperty($property);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject);
        self::assertRegExp('/.*class ModelCgt1.*/', $classFileContent, 'Class declaration was not generated');
        self::assertRegExp('/.*protected \\$blue.*/', $classFileContent, 'protected boolean property was not generated');
        self::assertRegExp('/.*\* \@var bool.*/', $classFileContent, 'var tag for boolean property was not generated');
        self::assertRegExp('/.*\* \@validate NotEmpty.*/', $classFileContent, 'validate tag for required property was not generated');
        self::assertRegExp('/.*public function getBlue\(\).*/', $classFileContent, 'Getter for boolean property was not generated');
        self::assertRegExp('/.*public function setBlue\(\$blue\).*/', $classFileContent, 'Setter for boolean property was not generated');
        self::assertRegExp('/.*public function isBlue\(\).*/', $classFileContent, 'is method for boolean property was not generated');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one boolean property
     *
     * @test
     */
    public function writeModelClassWithBooleanProperty()
    {
        $modelName = 'ModelCgt1';
        $propertyName = 'blue';
        $domainObject = $this->buildDomainObject($modelName, true);
        $domainObject->setDescription('This is the model class for ' . $modelName);
        $property = new BooleanProperty($propertyName);
        $property->setRequired(true);
        $domainObject->addProperty($property);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, false);
        $modelClassDir = 'Classes/Domain/Model/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $modelClassDir);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertTrue(is_dir($absModelClassDir), 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        GeneralUtility::writeFile($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath,'File was not generated: ' . $modelClassPath);
        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include_once($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);
        $reflectionService = new ReflectionService();
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');
        self::assertTrue($reflection->hasMethod('is' . ucfirst($propertyName)), 'isMethod was not generated');
        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        $parameters = $setterMethod['params'];
        self::assertEquals(1, count($parameters), 'Wrong parameter count in setter method');
        $firstParameterName = current(array_keys($parameters));
        self::assertEquals($firstParameterName, $propertyName, 'Wrong parameter name in setter method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one string property
     *
     * @test
     */
    public function writeModelClassWithStringProperty()
    {
        $modelName = 'ModelCgt2';
        $propertyName = 'title';
        $domainObject = $this->buildDomainObject($modelName);
        $property = new StringProperty($propertyName);
        $domainObject->addProperty($property);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, false);
        $modelClassDir = 'Classes/Domain/Model/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $modelClassDir);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertTrue(is_dir($absModelClassDir), 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        GeneralUtility::writeFile($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);
        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);
        $reflectionService = new ReflectionService();
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');
        self::assertFalse($reflection->hasMethod('is' . ucfirst($propertyName)), 'isMethod should not be generated');
        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        $parameters = $setterMethod['params'];
        self::assertEquals(1, count($parameters), 'Wrong parameter count in setter method');
        $firstParameterName = current(array_keys($parameters));
        self::assertEquals($firstParameterName, $propertyName, 'Wrong parameter name in setter method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one to one relation
     *
     * @test
     */
    public function writeModelClassWithZeroToOneRelation()
    {
        $modelName = 'ModelCgt3';
        $relatedModelName = 'RelatedModel';
        $propertyName = 'relName';
        $domainObject = $this->buildDomainObject($modelName);
        $relatedDomainObject = $this->buildDomainObject($relatedModelName);
        $relation = new Relation\ZeroToOneRelation($propertyName);
        $relation->setForeignModel($relatedDomainObject);
        $domainObject->addProperty($relation);
        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, false);
        $modelClassDir = 'Classes/Domain/Model/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $modelClassDir);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertTrue(is_dir($absModelClassDir), 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        GeneralUtility::writeFile($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);
        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);
        $this->markTestIncomplete(
          'Reflection does not find class ModelCgt3..?'
        );
        $reflectionService = new ReflectionService();
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');
        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
        self::assertTrue(in_array('param', $setterMethod['tags']), 'No param tag set for setter method');
        //$paramTagValues = $setterMethod->getTagValues('param');
        //self::assertEquals(0, strpos($paramTagValues[0], $relatedDomainObject->getFullQualifiedClassName()), 'Wrong param tag:' . $paramTagValues[0]);

        $parameters = $setterMethod['params'];
        self::assertEquals(1, count($parameters), 'Wrong parameter count in setter method');
        self::assertTrue(in_array($propertyName, array_keys($parameters)), 'Wrong parameter name in setter method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with zero to many relation
     *
     * @test
     */
    public function writeModelClassWithZeroToManyRelation()
    {
        $modelName = 'ModelCgt4';
        $relatedModelName = 'RelatedModel';
        $propertyName = 'relNames';
        $domainObject = $this->buildDomainObject($modelName);
        $relatedDomainObject = $this->buildDomainObject($relatedModelName);
        $relation = new Relation\ZeroToManyRelation($propertyName);
        $relation->setForeignModel($relatedDomainObject);
        $domainObject->addProperty($relation);

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, false);
        $modelClassDir = 'Classes/Domain/Model/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $modelClassDir);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertTrue(is_dir($absModelClassDir), 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        GeneralUtility::writeFile($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);
        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);

        $relatedClassFileContent = $this->fileGenerator->generateDomainObjectCode($relatedDomainObject, false);

        $relatedModelClassPath = $absModelClassDir . $relatedDomainObject->getName() . '.php';
        GeneralUtility::writeFile($relatedModelClassPath, $relatedClassFileContent);
        self::assertFileExists($relatedModelClassPath, 'File was not generated: ' . $relatedModelClassPath);
        $relatedClassName = $relatedDomainObject->getFullQualifiedClassName();
        if (!class_exists($relatedClassName)) {
            include($relatedModelClassPath);
            $r = new $relatedClassName();
        }
        self::assertTrue(class_exists($relatedClassName), 'Class was not generated:' . $relatedClassName);
        $this->markTestIncomplete(
          'Reflection does not find class.'
        );

        $reflectionService = new ReflectionService();
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('add' . ucfirst(Inflector::singularize($propertyName))), 'Add method was not generated');
        self::assertTrue($reflection->hasMethod('remove' . ucfirst(Inflector::singularize($propertyName))), 'Remove method was not generated');
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');

        //checking methods
//        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
//        self::assertTrue($setterMethod->isTaggedWith('param'), 'No param tag set for setter method');
//        $paramTagValues = $setterMethod->getTagValues('param');
//        self::assertEquals(0, strpos($paramTagValues[0], '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $relatedDomainObject->getFullQualifiedClassName()), 'Wrong param tag:' . $paramTagValues[0]);
//
//        $parameters = $setterMethod->getParameters();
//        self::assertEquals(1, count($parameters), 'Wrong parameter count in setter method');
//        $parameter = current($parameters);
//        self::assertEquals($parameter->getName(), $propertyName, 'Wrong parameter name in setter method');
//
//        $addMethod = $reflection->getMethod('add' . ucfirst(Inflector::singularize($propertyName)));
//        self::assertTrue($addMethod->isTaggedWith('param'), 'No param tag set for setter method');
//        $paramTagValues = $addMethod->getTagValues('param');
//        self::assertEquals(0, strpos($paramTagValues[0], $relatedDomainObject->getFullQualifiedClassName()), 'Wrong param tag:' . $paramTagValues[0]);
//
//        $parameters = $addMethod->getParameters();
//        self::assertEquals(1, count($parameters), 'Wrong parameter count in add method');
//        $parameter = current($parameters);
//        self::assertEquals($parameter->getName(), Inflector::singularize($propertyName), 'Wrong parameter name in add method');
//
//        $removeMethod = $reflection->getMethod('remove' . ucfirst(Inflector::singularize($propertyName)));
//        self::assertTrue($removeMethod->isTaggedWith('param'), 'No param tag set for remove method');
//        $paramTagValues = $removeMethod->getTagValues('param');
//        self::assertEquals(0, strpos($paramTagValues[0], $relatedDomainObject->getFullQualifiedClassName()), 'Wrong param tag:' . $paramTagValues[0]);
//
//        $parameters = $removeMethod->getParameters();
//        self::assertEquals(1, count($parameters), 'Wrong parameter count in remove method');
//        $parameter = current($parameters);
//        self::assertEquals($parameter->getName(), Inflector::singularize($propertyName) . 'ToRemove', 'Wrong parameter name in remove method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object with one to one relation
     *
     * @test
     */
    public function writeModelClassWithManyToManyRelation()
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

        $classFileContent = $this->fileGenerator->generateDomainObjectCode($domainObject, false);
        $modelClassDir = 'Classes/Domain/Model/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $modelClassDir);
        $absModelClassDir = $this->extension->getExtensionDir() . $modelClassDir;
        self::assertTrue(is_dir($absModelClassDir), 'Directory ' . $absModelClassDir . ' was not created');

        $modelClassPath = $absModelClassDir . $domainObject->getName() . '.php';
        GeneralUtility::writeFile($modelClassPath, $classFileContent);
        self::assertFileExists($modelClassPath, 'File was not generated: ' . $modelClassPath);
        $className = $domainObject->getFullQualifiedClassName();
        if (!class_exists($className)) {
            include($modelClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className);
        $this->markTestIncomplete(
          'Reflection does not find class.'
        );
        $reflectionService = new ReflectionService();
        $reflection = $reflectionService->getClassSchema(new $className());
        self::assertTrue($reflection->hasMethod('add' . ucfirst(Inflector::singularize($propertyName))), 'Add method was not generated');
        self::assertTrue($reflection->hasMethod('remove' . ucfirst(Inflector::singularize($propertyName))), 'Remove method was not generated');
        self::assertTrue($reflection->hasMethod('get' . ucfirst($propertyName)), 'Getter was not generated');
        self::assertTrue($reflection->hasMethod('set' . ucfirst($propertyName)), 'Setter was not generated');
        self::assertTrue($reflection->hasMethod('initStorageObjects'), 'initStorageObjects was not generated');

        //checking methods
//        $setterMethod = $reflection->getMethod('set' . ucfirst($propertyName));
//        self::assertTrue($setterMethod->isTaggedWith('param'), 'No param tag set for setter method');
//        $paramTagValues = $setterMethod->getTagValues('param');
//        self::assertEquals(0, strpos($paramTagValues[0], '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $relatedDomainObject->getFullQualifiedClassName()), 'Wrong param tag:' . $paramTagValues[0]);
//
//        $parameters = $setterMethod->getParameters();
//        self::assertEquals(1, count($parameters), 'Wrong parameter count in setter method');
//        $parameter = current($parameters);
//        self::assertEquals($parameter->getName(), $propertyName, 'Wrong parameter name in setter method');
//
//        $addMethod = $reflection->getMethod('add' . ucfirst(Inflector::singularize($propertyName)));
//        self::assertTrue($addMethod->isTaggedWith('param'), 'No param tag set for setter method');
//        $paramTagValues = $addMethod->getTagValues('param');
//        self::assertEquals(0, strpos($paramTagValues[0], $relatedDomainObject->getFullQualifiedClassName()), 'Wrong param tag:' . $paramTagValues[0]);
//
//        $parameters = $addMethod->getParameters();
//        self::assertEquals(1, count($parameters), 'Wrong parameter count in add method');
//        $parameter = current($parameters);
//        self::assertEquals($parameter->getName(), Inflector::singularize($propertyName), 'Wrong parameter name in add method');
//
//        $removeMethod = $reflection->getMethod('remove' . ucfirst(Inflector::singularize($propertyName)));
//        self::assertTrue($removeMethod->isTaggedWith('param'), 'No param tag set for remove method');
//        $paramTagValues = $removeMethod->getTagValues('param');
//        self::assertEquals(0, strpos($paramTagValues[0], $relatedDomainObject->getFullQualifiedClassName()), 'Wrong param tag:' . $paramTagValues[0]);
//
//        $parameters = $removeMethod->getParameters();
//        self::assertEquals(1, count($parameters), 'Wrong parameter count in remove method');
//        $parameter = current($parameters);
//        self::assertEquals($parameter->getName(), Inflector::singularize($propertyName) . 'ToRemove', 'Wrong parameter name in remove method');
    }

    /**
     * Write a simple model class for a non aggregate root domain object
     *
     * @test
     */
    public function writeSimpleControllerClassFromDomainObject()
    {
        $domainObject = $this->buildDomainObject('ModelCgt6', true);
        $action = GeneralUtility::makeInstance(Action::class);
        $action->setName('list');
        $domainObject->addAction($action);
        $classFileContent = $this->fileGenerator->generateActionControllerCode($domainObject, false);

        $controllerClassDir = 'Classes/Controller/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $controllerClassDir);
        $absControllerClassDir = $this->extension->getExtensionDir() . $controllerClassDir;
        self::assertTrue(is_dir($absControllerClassDir), 'Directory ' . $absControllerClassDir . ' was not created');
        $controllerClassPath = $absControllerClassDir . $domainObject->getName() . 'Controller.php';
        GeneralUtility::writeFile($controllerClassPath, $classFileContent);
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
    public function writeRepositoryClassFromDomainObject()
    {
        $domainObject = $this->buildDomainObject('ModelCgt6', true, true);
        $classFileContent = $this->fileGenerator->generateDomainRepositoryCode($domainObject, false);

        $repositoryClassDir = 'Classes/Domain/Repository/';
        GeneralUtility::mkdir_deep($this->extension->getExtensionDir(), $repositoryClassDir);
        $absRepositoryClassDir = $this->extension->getExtensionDir() . $repositoryClassDir;
        self::assertTrue(is_dir($absRepositoryClassDir), 'Directory ' . $absRepositoryClassDir . ' was not created');
        $repositoryClassPath = $absRepositoryClassDir . $domainObject->getName() . 'Repository.php';
        GeneralUtility::writeFile($repositoryClassPath, $classFileContent);
        self::assertFileExists($repositoryClassPath, 'File was not generated: ' . $repositoryClassPath);
        $className = $domainObject->getFullyQualifiedDomainRepositoryClassName();
        if (!class_exists($className)) {
            include($repositoryClassPath);
        }
        self::assertTrue(class_exists($className), 'Class was not generated:' . $className . 'in ' . $repositoryClassPath);
    }

    /**
     * This test is too generic, since it creates the required classes
     * with a whole fileGenerator->build call
     *
     *
     * @test
     */
    public function writeAggregateRootClassesFromDomainObject()
    {
        $domainObject = $this->buildDomainObject('ModelCgt7', true, true);
        $property = new BooleanProperty('blue');
        $property->setRequired(true);
        $domainObject->addProperty($property);

        $this->extension->addDomainObject($domainObject);

        $this->fileGenerator->build($this->extension);

        self::assertFileExists($this->extension->getExtensionDir() . 'Classes/Domain/Model/' . $domainObject->getName() . '.php');
        self::assertFileExists($this->extension->getExtensionDir() . 'Classes/Domain/Repository/' . $domainObject->getName() . 'Repository.php');
        self::assertFileExists($this->extension->getExtensionDir() . 'Classes/Controller/' . $domainObject->getName() . 'Controller.php');
    }

    /**
     * @depends writeModelClassWithManyToManyRelation
     * @depends writeAggregateRootClassesFromDomainObject
     *
     *
     * @test
     */
    public function writeExtensionFiles()
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

        $this->fileGenerator->build($this->extension);

        $extensionDir = $this->extension->getExtensionDir();

        $extensionFiles = ['ext_emconf.php', 'ext_tables.php', 'ext_tables.sql', 'ext_localconf.php'];
        foreach ($extensionFiles as $extensionFile) {
            self::assertFileExists($extensionDir . $extensionFile, 'File was not generated: ' . $extensionFile);
        }

        self::assertFileExists($extensionDir . 'Configuration/TCA/' . $domainObject->getDatabaseTableName() . '.php');
        self::assertFileExists($extensionDir . 'Configuration/ExtensionBuilder/settings.yaml');

        self::assertFileExists($extensionDir . 'Resources/Private/Language/locallang_db.xlf');
        self::assertFileExists($extensionDir . 'Resources/Private/Language/locallang.xlf');
        self::assertFileExists($extensionDir . 'Resources/Private/Partials/' . $domainObject->getName() . '/Properties.html');
        self::assertFileExists($extensionDir . 'Resources/Private/Partials/' . $domainObject->getName() . '/FormFields.html');
    }
}
