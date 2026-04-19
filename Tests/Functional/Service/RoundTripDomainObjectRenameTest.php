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

use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;

class RoundTripDomainObjectRenameTest extends BaseFunctionalTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function domainObjectRenameUpdatesModelControllerAndRepository(): void
    {
        $oldName = 'Foo';
        $newName = 'Bar';
        $uid = md5('rename-foo-bar');

        // Create the initial model file
        $this->generateInitialModelClassFile($oldName);

        // Create initial controller stub
        $controllerDir = $this->extension->getExtensionDir() . 'Classes/Controller/';
        mkdir($controllerDir, 0777, true);
        file_put_contents(
            $controllerDir . $oldName . 'Controller.php',
            "<?php\nnamespace EBT\\Dummy\\Controller;\nclass FooController extends \\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController {\n    public function listAction(): void {}\n}\n"
        );

        // Create initial repository stub
        $repositoryDir = $this->extension->getExtensionDir() . 'Classes/Domain/Repository/';
        mkdir($repositoryDir, 0777, true);
        file_put_contents(
            $repositoryDir . $oldName . 'Repository.php',
            "<?php\nnamespace EBT\\Dummy\\Domain\\Repository;\nclass FooRepository extends \\TYPO3\\CMS\\Extbase\\Persistence\\Repository {}\n"
        );

        // Set up old domain object (aggregate root with actions)
        $oldDomainObject = $this->buildDomainObject($oldName, true, true);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        // Set up new domain object with same UID but new name
        $newDomainObject = $this->buildDomainObject($newName, true, true);
        $newDomainObject->setUniqueIdentifier($uid);

        // Call the three roundtrip methods
        $modelFile = $this->roundTripService->getDomainModelClassFile($newDomainObject);
        $controllerFile = $this->roundTripService->getControllerClassFile($newDomainObject);
        $repositoryFile = $this->roundTripService->getRepositoryClassFile($newDomainObject);

        self::assertNotNull($modelFile, 'Model class file must not be null');
        self::assertNotNull($controllerFile, 'Controller class file must not be null');
        self::assertNotNull($repositoryFile, 'Repository class file must not be null');

        $modelClass = $modelFile->getFirstClass();
        $controllerClass = $controllerFile->getFirstClass();
        $repositoryClass = $repositoryFile->getFirstClass();

        self::assertSame($newName, $modelClass->getName(), 'Model class must be renamed to Bar');
        self::assertSame($newName . 'Controller', $controllerClass->getName(), 'Controller class must be renamed to BarController');
        self::assertSame($newName . 'Repository', $repositoryClass->getName(), 'Repository class must be renamed to BarRepository');

        self::assertStringNotContainsString('Foo', $modelClass->getName());
        self::assertStringNotContainsString('Foo', $controllerClass->getName());
        self::assertStringNotContainsString('Foo', $repositoryClass->getName());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function renameAggregateRootUpdatesInjectMethodAndActionParameters(): void
    {
        $oldName = 'Entries';
        $newName = 'Entry';
        $uid = md5('rename-entries-entry');

        $this->generateInitialModelClassFile($oldName);

        $oldDomainObject = $this->buildDomainObject($oldName, true, true);

        $controllerDir = $this->extension->getExtensionDir() . 'Classes/Controller/';
        mkdir($controllerDir, 0777, true);
        $controllerCode = $this->fileGenerator->generateActionControllerCode($oldDomainObject);
        file_put_contents($controllerDir . $oldName . 'Controller.php', $controllerCode);

        $repositoryDir = $this->extension->getExtensionDir() . 'Classes/Domain/Repository/';
        mkdir($repositoryDir, 0777, true);
        file_put_contents(
            $repositoryDir . $oldName . 'Repository.php',
            "<?php\nnamespace EBT\\Dummy\\Domain\\Repository;\nclass EntriesRepository extends \\TYPO3\\CMS\\Extbase\\Persistence\\Repository {}\n"
        );

        $oldDomainObject->setUniqueIdentifier($uid);
        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $newDomainObject = $this->buildDomainObject($newName, true, true);
        $newDomainObject->setUniqueIdentifier($uid);

        $controllerFile = $this->roundTripService->getControllerClassFile($newDomainObject);

        self::assertNotNull($controllerFile, 'Controller class file must not be null');

        $controllerClass = $controllerFile->getFirstClass();
        self::assertSame($newName . 'Controller', $controllerClass->getName());

        // Constructor injection parameter must reference the new repository (TYPO3 v12+)
        $constructor = $controllerClass->getMethod('__construct');
        self::assertNotNull($constructor, 'Controller must have a constructor');
        $constructorParam = $constructor->getParameterByPosition(0);
        self::assertNotNull($constructorParam, 'Constructor must have a parameter');
        self::assertSame('entryRepository', $constructorParam->getName(), 'Constructor parameter must be renamed');
        self::assertStringContainsString('EntryRepository', $constructorParam->getTypeHint(), 'Constructor parameter type must reference new repository');
        self::assertStringNotContainsString('EntriesRepository', $constructorParam->getTypeHint(), 'Constructor parameter must not reference old repository');

        // Action method parameters must use the new domain class, not the old one
        foreach (['showAction', 'editAction', 'updateAction', 'deleteAction'] as $actionName) {
            $actionMethod = $controllerClass->getMethod($actionName);
            if ($actionMethod === null) {
                continue;
            }
            foreach ($actionMethod->getParameters() as $param) {
                self::assertStringNotContainsString(
                    'Entries',
                    $param->getTypeHint(),
                    "Parameter type hint in {$actionName} must not reference old class"
                );
            }
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function renameAggregateRootUpdatesInjectMethodNameAndBody(): void
    {
        $oldName = 'Foo';
        $newName = 'Bar';
        $uid = md5('rename-foo-bar-inject');

        $this->generateInitialModelClassFile($oldName);

        $oldDomainObject = $this->buildDomainObject($oldName, true, true);

        $controllerDir = $this->extension->getExtensionDir() . 'Classes/Controller/';
        mkdir($controllerDir, 0777, true);
        // Controller with TYPO3 v11-style inject method (not constructor injection)
        file_put_contents(
            $controllerDir . $oldName . 'Controller.php',
            "<?php\nnamespace EBT\\Dummy\\Controller;\nuse EBT\\Dummy\\Domain\\Repository\\FooRepository;\nclass FooController extends \\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController {\n    private FooRepository \$fooRepository;\n    public function injectFooRepository(FooRepository \$fooRepository): void { \$this->fooRepository = \$fooRepository; }\n}\n"
        );

        $repositoryDir = $this->extension->getExtensionDir() . 'Classes/Domain/Repository/';
        mkdir($repositoryDir, 0777, true);
        file_put_contents(
            $repositoryDir . $oldName . 'Repository.php',
            "<?php\nnamespace EBT\\Dummy\\Domain\\Repository;\nclass FooRepository extends \\TYPO3\\CMS\\Extbase\\Persistence\\Repository {}\n"
        );

        $oldDomainObject->setUniqueIdentifier($uid);
        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        $newDomainObject = $this->buildDomainObject($newName, true, true);
        $newDomainObject->setUniqueIdentifier($uid);

        $controllerFile = $this->roundTripService->getControllerClassFile($newDomainObject);

        self::assertNotNull($controllerFile, 'Controller class file must not be null');

        $controllerClass = $controllerFile->getFirstClass();
        self::assertSame($newName . 'Controller', $controllerClass->getName());

        // Inject method must be renamed from injectFooRepository to injectBarRepository
        self::assertNull($controllerClass->getMethod('injectFooRepository'), 'Old inject method must be removed');
        $injectMethod = $controllerClass->getMethod('injectBarRepository');
        self::assertNotNull($injectMethod, 'Renamed inject method injectBarRepository must exist');

        // Inject method parameter must reference the new repository
        $injectParam = $injectMethod->getParameterByPosition(0);
        self::assertNotNull($injectParam, 'Inject method must have a parameter');
        self::assertSame('barRepository', $injectParam->getName(), 'Inject parameter must be renamed to barRepository');
        self::assertStringContainsString('BarRepository', $injectParam->getTypeHint(), 'Inject parameter type must reference new repository');
        self::assertStringNotContainsString('FooRepository', $injectParam->getTypeHint(), 'Inject parameter must not reference old repository');
    }
}
