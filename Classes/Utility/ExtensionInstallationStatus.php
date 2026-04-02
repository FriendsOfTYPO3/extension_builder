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

namespace EBT\ExtensionBuilder\Utility;

use EBT\ExtensionBuilder\Domain\Model\Extension;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ExtensionInstallationStatus
{
    protected ?Extension $extension = null;
    protected bool $usesComposerPath = false;

    public function setExtension(Extension $extension): void
    {
        $this->extension = $extension;
    }

    public function setUsesComposerPath(bool $usesComposerPath): void
    {
        $this->usesComposerPath = $usesComposerPath;
    }

    /**
     * @return string[]
     */
    public function getStatusMessages(): array
    {
        $messages = [];

        if (!ExtensionManagementUtility::isLoaded($this->extension->getExtensionKey())) {
            $messages[] = LocalizationUtility::translate('notification.not_installed', 'ExtensionBuilder')
                ?? 'Your Extension is not installed yet.';
            if ($this->usesComposerPath) {
                $messages[] = LocalizationUtility::translate(
                    'notification.composer_require',
                    'ExtensionBuilder',
                    [$this->extension->getComposerInfo()['name']]
                ) ?? sprintf('Execute: composer require %s:@dev in terminal', $this->extension->getComposerInfo()['name']);
            }
        } else {
            $messages[] = LocalizationUtility::translate('notification.db_update', 'ExtensionBuilder')
                ?? 'Please check the Install Tool for possible database updates!';
        }

        return $messages;
    }
}
