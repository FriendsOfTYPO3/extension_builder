<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

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

use EBT\ExtensionBuilder\Domain\Model\DomainObject;

class MappingViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper
{
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
     */
    protected $configurationManager = null;

    /**
     * @param \EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager
     * @return void
     */
    public function injectConfigurationManager(\EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('domainObject', 'object', '', true);
        $this->registerArgument('renderCondition', 'string', '', true);
    }

    /**
     * Helper function to verify various conditions around possible mapping/inheritance configurations
     *
     * @return string
     */
    public function render()
    {
        $content = '';
        /** @var DomainObject $domainObject */
        $domainObject = $this->arguments['domainObject'];
        $extensionPrefix = 'Tx_' . $domainObject->getExtension()->getExtensionName();

        // an external table should have a loadable TCA configuration and the column definitions
        // for external tables have to be declared in ext_tables.php
        $isMappedToExternalTable = false;

        // table name is only set, if the model is mapped to a table or if the domain object extends a class
        $tableName = $domainObject->getMapToTable();

        if ($tableName && strpos($tableName, strtolower($extensionPrefix) . '_domain_model_') === false) {
            $isMappedToExternalTable = true;
        }

        switch ($this->arguments['renderCondition']) {

            case 'isMappedToInternalTable'    :
                if (!$isMappedToExternalTable) {
                    $content = $this->renderThenChild();
                } else {
                    $content = $this->renderElseChild();
                }
                break;

            case 'isMappedToExternalTable'    :
                if ($isMappedToExternalTable) {
                    $content = $this->renderThenChild();
                } else {
                    $content = $this->renderElseChild();
                }
                break;

            case 'needsTypeField'            :
                if ($this->needsTypeField($domainObject, $isMappedToExternalTable)) {
                    $content = $this->renderThenChild();
                } else {
                    $content = $this->renderElseChild();
                }
                break;
        }

        return $content;
    }

    /**
     * Do we have to create a typefield in database and configuration?
     *
     * A typefield is needed if either the domain objects extends another class
     * or if other domain objects of this extension extend it or if it is mapped
     * to an existing table
     *
     * @param DomainObject $domainObject
     * @param bool $isMappedToExternalTable
     * @return bool
     */
    protected function needsTypeField(DomainObject $domainObject, $isMappedToExternalTable)
    {
        $needsTypeField = false;
        if ($domainObject->getChildObjects() || $isMappedToExternalTable) {
            $tableName = $domainObject->getDatabaseTableName();
            if (!isset($GLOBALS['TCA'][$tableName]['ctrl']['type']) || $GLOBALS['TCA'][$tableName]['ctrl']['type'] == 'tx_extbase_type') {
                /**
                 * if the type field is set but equals the default extbase record type field name it might
                 * have been defined by the current extension and thus has to be defined again when rewriting TCA definitions
                 * this might result in duplicate definition, but the type field definition is always wrapped in a condition
                 * "if (!isset($GLOBALS['TCA'][table][ctrl][type]){ ..."
                 *
                 * If we don't check the TCA at runtime it would result in a repetition of type field definitions
                 * in case an extension has multiple models extending other models of the same extension
                 */
                $needsTypeField = true;
            }
        }
        return $needsTypeField;
    }
}
