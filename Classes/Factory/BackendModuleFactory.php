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

namespace EBT\ExtensionBuilder\Factory;

use EBT\ExtensionBuilder\Domain\Model\BackendModule;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendModuleFactory
{
    public function buildBackendModule(array $backendModuleValues): BackendModule
    {
        $backendModule = new BackendModule();
        $backendModule->setName($backendModuleValues['name']);
        $backendModule->setMainModule($backendModuleValues['mainModule']);
        $backendModule->setTabLabel($backendModuleValues['tabLabel']);
        $backendModule->setKey($backendModuleValues['key']);
        $backendModule->setDescription($backendModuleValues['description']);

        if (!empty($backendModuleValues['actions']['controllerActionCombinations'])) {
            $controllerActionCombinations = [];
            $lines = GeneralUtility::trimExplode(
                LF,
                $backendModuleValues['actions']['controllerActionCombinations'],
                true
            );
            foreach ($lines as $line) {
                [$controllerName, $actionNames] = GeneralUtility::trimExplode('=>', $line);
                $controllerActionCombinations[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
            }
            $backendModule->setControllerActionCombinations($controllerActionCombinations);
        }
        return $backendModule;
    }
}
