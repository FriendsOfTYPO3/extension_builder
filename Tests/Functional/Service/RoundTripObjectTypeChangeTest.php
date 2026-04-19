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

class RoundTripObjectTypeChangeTest extends BaseFunctionalTest
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function entityToValueObjectRemovesControllerAndRepositoryFiles(): void
    {
        $modelName = 'Foo';
        $uid = md5('entity-to-value-object');
        $extDir = $this->extension->getExtensionDir();

        // Create model file
        $this->generateInitialModelClassFile($modelName);

        // Create controller and repository files
        $controllerDir = $extDir . 'Classes/Controller/';
        $repositoryDir = $extDir . 'Classes/Domain/Repository/';
        mkdir($controllerDir, 0777, true);
        mkdir($repositoryDir, 0777, true);

        file_put_contents(
            $controllerDir . $modelName . 'Controller.php',
            "<?php\nnamespace EBT\\Dummy\\Controller;\nclass FooController extends \\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController {\n    public function listAction(): void {}\n}\n"
        );
        file_put_contents(
            $repositoryDir . $modelName . 'Repository.php',
            "<?php\nnamespace EBT\\Dummy\\Domain\\Repository;\nclass FooRepository extends \\TYPO3\\CMS\\Extbase\\Persistence\\Repository {}\n"
        );

        self::assertFileExists($controllerDir . $modelName . 'Controller.php');
        self::assertFileExists($repositoryDir . $modelName . 'Repository.php');

        // Old domain object: AggregateRoot with actions
        $oldDomainObject = $this->buildDomainObject($modelName, true, true);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $extDir);

        // New domain object: non-AggregateRoot (ValueObject), no actions
        $newDomainObject = $this->buildDomainObject($modelName, false, false);
        $newDomainObject->setUniqueIdentifier($uid);

        // getDomainModelClassFile triggers controller and repository cleanup
        $modelFile = $this->roundTripService->getDomainModelClassFile($newDomainObject);

        self::assertNotNull($modelFile, 'Model file must still be returned');
        $modelDir = $extDir . 'Classes/Domain/Model/';
        self::assertFileExists($modelDir . $modelName . '.php', 'Model file must not be deleted');
        self::assertFileDoesNotExist($controllerDir . $modelName . 'Controller.php', 'Controller file must be deleted');
        self::assertFileDoesNotExist($repositoryDir . $modelName . 'Repository.php', 'Repository file must be deleted');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function valueObjectToEntityReturnsNullForControllerAndRepositoryWithoutPreviousFiles(): void
    {
        $modelName = 'Bar';
        $uid = md5('value-object-to-entity');

        // Create only the model file (no controller or repository — it was a ValueObject)
        $this->generateInitialModelClassFile($modelName);

        // Old domain object: non-AggregateRoot (ValueObject), no actions
        $oldDomainObject = $this->buildDomainObject($modelName, false, false);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        // New domain object: promoted to AggregateRoot, has actions
        $newDomainObject = $this->buildDomainObject($modelName, true, true);
        $newDomainObject->setUniqueIdentifier($uid);

        // No controller or repository files exist yet, so these must return null without crashing
        $controllerFile = $this->roundTripService->getControllerClassFile($newDomainObject);
        $repositoryFile = $this->roundTripService->getRepositoryClassFile($newDomainObject);

        self::assertNull($controllerFile, 'getControllerClassFile must return null when no previous file exists');
        self::assertNull($repositoryFile, 'getRepositoryClassFile must return null when no previous file exists');
    }
}
