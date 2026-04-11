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
    /**
     * @test
     */
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
}
