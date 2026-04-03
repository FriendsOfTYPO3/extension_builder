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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RoundTripControllerActionsTest extends BaseFunctionalTest
{
    private function buildControllerFile(string $modelName, array $actionNames): void
    {
        $controllerDir = $this->extension->getExtensionDir() . 'Classes/Controller/';
        if (!is_dir($controllerDir)) {
            mkdir($controllerDir, 0777, true);
        }

        $methods = '';
        foreach ($actionNames as $action) {
            $methods .= "\n    public function {$action}Action(): void {}\n";
        }

        file_put_contents(
            $controllerDir . $modelName . 'Controller.php',
            "<?php\nnamespace EBT\\Dummy\\Controller;\nclass {$modelName}Controller extends \\TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController {{$methods}}\n"
        );
    }

    private function buildDomainObjectWithActions(string $name, array $actionNames): \EBT\ExtensionBuilder\Domain\Model\DomainObject
    {
        // Use entity=true, aggregateRoot=false to avoid auto-adding default CRUD actions
        $domainObject = $this->buildDomainObject($name, true, false);
        foreach ($actionNames as $actionName) {
            $action = GeneralUtility::makeInstance(Action::class);
            $action->setName($actionName);
            $domainObject->addAction($action);
        }
        return $domainObject;
    }

    /**
     * @test
     */
    public function addingAnActionToControllerIsReflectedInRoundtrip(): void
    {
        $modelName = 'ActionAddModel';
        $uid = md5('action-add');

        // Initial controller has list and show actions
        $this->buildControllerFile($modelName, ['list', 'show']);

        // Old domain object with list + show
        $oldDomainObject = $this->buildDomainObjectWithActions($modelName, ['list', 'show']);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        // New domain object with list + show + create
        $newDomainObject = $this->buildDomainObjectWithActions($modelName, ['list', 'show', 'create']);
        $newDomainObject->setUniqueIdentifier($uid);

        $controllerFile = $this->roundTripService->getControllerClassFile($newDomainObject);

        self::assertNotNull($controllerFile, 'Controller file must be returned');
        $controllerClass = $controllerFile->getFirstClass();

        // Existing actions must still be present
        self::assertTrue($controllerClass->methodExists('listAction'), 'listAction must still exist');
        self::assertTrue($controllerClass->methodExists('showAction'), 'showAction must still exist');
        // New action method is NOT in the existing file; the class builder will add it — verify no exception
        // The roundtrip service does NOT add new actions (only removes old ones); the class builder handles additions
        self::assertFalse($controllerClass->methodExists('createAction'), 'createAction not yet in file — will be added by class builder');
    }

    /**
     * @test
     */
    public function removingAnActionFromControllerIsReflectedInRoundtrip(): void
    {
        $modelName = 'ActionRemoveModel';
        $uid = md5('action-remove');

        // Initial controller has list, show, and create actions
        $this->buildControllerFile($modelName, ['list', 'show', 'create']);

        // Old domain object with list + show + create
        $oldDomainObject = $this->buildDomainObjectWithActions($modelName, ['list', 'show', 'create']);
        $oldDomainObject->setUniqueIdentifier($uid);

        $this->roundTripService->_set('previousDomainObjects', [$uid => $oldDomainObject]);
        $this->roundTripService->_set('previousExtensionDirectory', $this->extension->getExtensionDir());

        // New domain object WITHOUT create action
        $newDomainObject = $this->buildDomainObjectWithActions($modelName, ['list', 'show']);
        $newDomainObject->setUniqueIdentifier($uid);

        $controllerFile = $this->roundTripService->getControllerClassFile($newDomainObject);

        self::assertNotNull($controllerFile, 'Controller file must be returned');
        $controllerClass = $controllerFile->getFirstClass();

        self::assertTrue($controllerClass->methodExists('listAction'), 'listAction must still exist');
        self::assertTrue($controllerClass->methodExists('showAction'), 'showAction must still exist');
        self::assertFalse($controllerClass->methodExists('createAction'), 'createAction must be removed from controller');
    }
}
