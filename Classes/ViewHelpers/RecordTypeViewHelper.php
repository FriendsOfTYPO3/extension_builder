<?php
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

class Tx_ExtensionBuilder_ViewHelpers_RecordTypeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var Tx_ExtensionBuilder_Configuration_ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @param Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_ExtensionBuilder_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Helper function to find the parents class recordType
	 * @param Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject
	 * @return string
	 */
	public function render(Tx_ExtensionBuilder_Domain_Model_DomainObject $domainObject) {
		$classSettings = $this->configurationManager->getExtbaseClassConfiguration($domainObject->getParentClass());
		if (isset($classSettings['recordType'])) {
			$parentRecordType = $this->convertClassNameToRecordType($classSettings['recordType']);
		} else {
			$parentRecordType = $this->convertClassNameToRecordType($domainObject->getParentClass());
			if (!isset($TCA[$domainObject->getDatabaseTableName()]['types'][$parentRecordType])) {
				$parentRecordType = 1;
			}
		}

		$this->templateVariableContainer->add('parentRecordType', $parentRecordType);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('parentRecordType');

		return $content;
	}

	protected function convertClassNameToRecordType($className) {
		$classNameParts = explode('\\', $className);
		if(count($classNameParts) > 5) {
			return 'Tx_' . $classNameParts[2] . '_Domain_Model_' . $classNameParts[5];
		} elseif(count($classNameParts) == 5) {
			return 'Tx_' . $classNameParts[1] . '_Domain_Model_' . $classNameParts[4];
		} else {
			return $className;
		}
	}

}

?>