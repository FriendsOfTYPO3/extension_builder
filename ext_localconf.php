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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// Register default code template paths for the backend module TypoScript
// configuration. Third-party extensions can add additional paths by appending
// to module.extension_builder.settings.codeTemplateRootPaths.
ExtensionManagementUtility::addTypoScriptSetup(
    'module.extension_builder.settings.codeTemplateRootPaths.0 = EXT:extension_builder/Resources/Private/CodeTemplates/Extbase/' . "\n"
    . 'module.extension_builder.settings.codeTemplatePartialPaths.0 = EXT:extension_builder/Resources/Private/CodeTemplates/Extbase/Partials'
);
