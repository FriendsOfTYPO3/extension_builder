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

/**
 * Class RecordTypeViewHelper
 * @package EBT\ExtensionBuilder\ViewHelpers
 */

class RecordTypeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
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
	 * Helper function to find the parents class recordType
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
	 * @return string
	 */
	public function render(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject) {
		$classSettings = $this->configurationManager->getExtbaseClassConfiguration($domainObject->getParentClass());
		if (isset($classSettings['recordType'])) {
			$parentRecordType = \EBT\ExtensionBuilder\Utility\Tools::convertClassNameToRecordType($classSettings['recordType']);
		} else {
			$parentRecordType = \EBT\ExtensionBuilder\Utility\Tools::convertClassNameToRecordType($domainObject->getParentClass());
			$existingTypes = $GLOBALS['TCA'][$domainObject->getDatabaseTableName()]['types'];
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Parent Record type: ' . $parentRecordType, 'extension_builder', 2, $existingTypes);
			if (is_array($existingTypes) && !isset($existingTypes[$parentRecordType])) {
				// no types field for parent record type configured, use the default type 1
				if (isset($existingTypes['1'])) {
					$parentRecordType = 1;
				} else {
					//if it not exists get first existing key
					$parentRecordType = reset(array_keys($existingTypes));
				}
			}
		}

		$this->templateVariableContainer->add('parentRecordType', $parentRecordType);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('parentRecordType');

		return $content;
	}

}
