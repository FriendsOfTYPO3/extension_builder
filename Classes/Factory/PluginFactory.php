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

use EBT\ExtensionBuilder\Domain\Model\Plugin;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PluginFactory
{
    public function buildPlugin(array $pluginValues): Plugin
    {
        $plugin = new Plugin();
        $plugin->setName($pluginValues['name']);
        $plugin->setDescription($pluginValues['description']);
        $plugin->setKey($pluginValues['key']);

        if (!empty($pluginValues['actions']['controllerActionCombinations'])) {
            $controllerActionCombinations = [];
            $lines = GeneralUtility::trimExplode(LF, $pluginValues['actions']['controllerActionCombinations'], true);
            foreach ($lines as $line) {
                [$controllerName, $actionNames] = GeneralUtility::trimExplode('=>', $line);
                if (!empty($actionNames)) {
                    $controllerActionCombinations[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
                }
            }
            $plugin->setControllerActionCombinations($controllerActionCombinations);
        }

        if (!empty($pluginValues['actions']['noncacheableActions'])) {
            $nonCacheableControllerActions = [];
            $lines = GeneralUtility::trimExplode(LF, $pluginValues['actions']['noncacheableActions'], true);
            foreach ($lines as $line) {
                [$controllerName, $actionNames] = GeneralUtility::trimExplode('=>', $line);
                if (!empty($actionNames)) {
                    $nonCacheableControllerActions[$controllerName] = GeneralUtility::trimExplode(',', $actionNames);
                }
            }
            $plugin->setNonCacheableControllerActions($nonCacheableControllerActions);
        }
        return $plugin;
    }
}
