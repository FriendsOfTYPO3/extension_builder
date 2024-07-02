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
use Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ExtensionInstallationStatus
{
    protected ?Extension $extension = null;
    protected array $updateStatements = [];
    protected bool $dbUpdateNeeded = false;
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
     * @throws Exception
     */
    public function getStatusMessage(): string
    {
        $statusMessage = '';
        // $this->checkForDbUpdate($this->extension->getExtensionKey());

        if ($this->dbUpdateNeeded) {
            $statusMessage .= '<p>Database has to be updated!</p>';
            $typeInfo = [
                'add' => 'Add fields',
                'change' => 'Change fields',
                'create_table' => 'Create tables'
            ];
            $statusMessage .= '<div id="dbUpdateStatementsWrapper"><table>';
            foreach ($this->updateStatements as $type => $statements) {
                $statusMessage .= '<tr><td></td><td style="text-align:left;padding-left:15px">' . $typeInfo[$type] . ':</td></tr>';
                foreach ($statements as $key => $statement) {
                    if ($type == 'add') {
                        $statusMessage .= '<tr><td><input type="checkbox" name="dbUpdateStatements[]" value="' . $key . '" checked="checked" /></td><td style="text-align:left;padding-left:15px">' . $statement . '</td></tr>';
                    } elseif ($type === 'change') {
                        $statusMessage .= '<tr><td><input type="checkbox" name="dbUpdateStatements[]" value="' . $key . '" checked="checked" /></td><td style="text-align:left;padding-left:15px">' . $statement . '</td></tr>';
                        $statusMessage .= '<tr><td></td><td style="text-align:left;padding-left:15px">Current value: ' . $this->updateStatements['change_currentValue'][$key] . '</td></tr>';
                    } elseif ($type === 'create_table') {
                        $statusMessage .= '<tr><td><input type="checkbox" name="dbUpdateStatements[]" value="' . $key . '" checked="checked" /></td><td style="text-align:left;padding-left:15px;">' . nl2br($statement) . '</td></tr>';
                    }
                }
            }
            $statusMessage .= '</table></div>';
        }

        if (!ExtensionManagementUtility::isLoaded($this->extension->getExtensionKey())) {
            $statusMessage .= '<p>Your Extension is not installed yet.</p>';
            if ($this->usesComposerPath) {
                $statusMessage .= sprintf(
                    'Execute <code>composer require %1$s:@dev</code> in terminal',
                    $this->extension->getComposerInfo()['name']
                );
            }
        } else {
            $statusMessage .= '<br /><p>Please check the Install Tool for possible database updates!</p>';
        }

        return $statusMessage;
    }

    public function isDbUpdateNeeded(): bool
    {
        return $this->dbUpdateNeeded;
    }
}
