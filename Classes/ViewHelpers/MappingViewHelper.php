<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class MappingViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper {
	/**
	 * @var \EBT\ExtensionBuilder\Configuration\ConfigurationManager
	 */
	protected $configurationManager = NULL;

	/**
	 * @param \EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\EBT\ExtensionBuilder\Configuration\ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Helper function to verify various conditions around possible mapping/inheritance configurations
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
	 * @param string $renderCondition
	 * @return string
	 */
	public function render(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject, $renderCondition) {
		$content = '';
		$extensionPrefix = 'Tx_' . \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($domainObject->getExtension()->getExtensionKey());

		// an external table should have a loadable TCA configuration and the column definitions
		// for external tables have to be declared in ext_tables.php
		$isMappedToExternalTable = FALSE;

		// table name is only set, if the model is mapped to a table or if the domain object extends a class
		$tableName = $domainObject->getMapToTable();

		if ($tableName && strpos($tableName, strtolower($extensionPrefix) . '_domain_model_') === FALSE) {
			$isMappedToExternalTable = TRUE;
		}

		switch ($renderCondition) {

			case 'isMappedToInternalTable'	:
				if (!$isMappedToExternalTable) {
					$content = $this->renderThenChild();
				} else {
					$content = $this->renderElseChild();
				}
				break;

			case 'isMappedToExternalTable'	:
				if ($isMappedToExternalTable) {
					$content = $this->renderThenChild();
				} else {
					$content = $this->renderElseChild();
				}
				break;

			case 'needsTypeField'			:
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
	 * @param string $tableName
	 * @param string $parentClass
	 * @param bool $isMappedToExternalTable
	 * @return bool
	 */
	protected function needsTypeField(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject, $isMappedToExternalTable) {
		$needsTypeField = FALSE;
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
				$needsTypeField = TRUE;
			}
		}
		return $needsTypeField;
	}

}
