<?php
namespace EBT\ExtensionBuilder\Utility;

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

use EBT\ExtensionBuilder\Domain\Model\Extension;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\InstallUtility;
use TYPO3\CMS\Install\Service\SqlSchemaMigrationService;

class ExtensionInstallationStatus
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Extension
     */
    protected $extension = null;
    /**
     * @var InstallUtility
     */
    protected $installTool = null;
    /**
     * @var array[]
     */
    protected $updateStatements = array();
    /**
     * @var bool
     */
    protected $dbUpdateNeeded = false;

    public function __construct()
    {
        $this->installTool = GeneralUtility::makeInstance(InstallUtility::class);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    public function getStatusMessage()
    {
        $statusMessage = '';
        $this->checkForDbUpdate($this->extension->getExtensionKey(), $this->extension->getExtensionDir() . 'ext_tables.sql');

        if ($this->dbUpdateNeeded) {
            $statusMessage .= '<p>Database has to be updated!</p>';
            $typeInfo = array(
                'add' => 'Add fields',
                'change' => 'Change fields',
                'create_table' => 'Create tables'
            );
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
        }

        return $statusMessage;
    }

    /**
     * @param string $extKey
     * @return void
     */
    public function checkForDbUpdate($extensionKey)
    {
        $this->dbUpdateNeeded = false;
        if (ExtensionManagementUtility::isLoaded($extensionKey)) {
            $sqlFile = ExtensionManagementUtility::extPath($extensionKey) . 'ext_tables.sql';
            if (@file_exists($sqlFile)) {
                $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                /* @var \TYPO3\CMS\Install\Service\SqlSchemaMigrationService $sqlHandler */
                $sqlHandler = GeneralUtility::makeInstance(SqlSchemaMigrationService::class);

                $sqlContent = GeneralUtility::getUrl($sqlFile);
                $fieldDefinitionsFromFile = $sqlHandler->getFieldDefinitions_fileContent($sqlContent);
                if (count($fieldDefinitionsFromFile)) {
                    $fieldDefinitionsFromCurrentDatabase = $sqlHandler->getFieldDefinitions_database();
                    $updateTableDefinition = $sqlHandler->getDatabaseExtra($fieldDefinitionsFromFile, $fieldDefinitionsFromCurrentDatabase);
                    $this->updateStatements = $sqlHandler->getUpdateSuggestions($updateTableDefinition);
                    if (!empty($updateTableDefinition['extra']) || !empty($updateTableDefinition['diff']) || !empty($updateTableDefinition['diff_currentValues'])) {
                        $this->dbUpdateNeeded = true;
                    }
                }
            }
        }
    }

    public function performDbUpdates($params)
    {
        $hasErrors = false;
        if (!empty($params['updateStatements']) && !empty($params['extensionKey'])) {
            $this->checkForDbUpdate($params['extensionKey']);
            if ($this->dbUpdateNeeded) {
                foreach ($this->updateStatements as $type => $statements) {
                    foreach ($statements as $key => $statement) {
                        if (in_array($type, array('change', 'add', 'create_table')) && in_array($key, $params['updateStatements'])) {
                            $res = $this->getDatabaseConnection()->admin_query($statement);
                            if ($res === false) {
                                $hasErrors = true;
                                GeneralUtility::devLog('SQL error', 'extension_builder', 0, array('statement' => $statement, 'error' => $this->getDatabaseConnection()->sql_error()));
                            } elseif (is_resource($res) || is_a($res, '\\mysqli_result')) {
                                $this->getDatabaseConnection()->sql_free_result($res);
                            }
                        }
                    }
                }
            }
        }
        if ($hasErrors) {
            return array('error' => 'Database could not be updated. Please check it in the update wizzard of the install tool');
        } else {
            return array('success' => 'Database was successfully updated');
        }
    }

    /**
     * @return bool
     */
    public function isDbUpdateNeeded()
    {
        return $this->dbUpdateNeeded;
    }

    /**
     * @return array
     */
    public function getUpdateStatements()
    {
        return $this->updateStatements;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
