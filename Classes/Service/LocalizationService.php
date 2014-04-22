<?php
namespace EBT\ExtensionBuilder\Service;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen
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
use TYPO3\CMS\Core\Utility;

/**
 * Helper for localization related stuff
 */

class LocalizationService implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var \TYPO3\CMS\Core\Localization\Parser\XliffParser
	 */
	protected $xliffParser = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Utility\Inflector
	 * @inject
	 */
	protected $inflector = NULL;

	/**
	 * @param \TYPO3\CMS\Core\Localization\Parser\XliffParser $xlifflParser
	 */
	public function injectXliffParser(\TYPO3\CMS\Core\Localization\Parser\XliffParser $xlifflParser) {
		$this->xliffParser = $xlifflParser;
	}

	public function getLabelArrayFromFile($file, $languageKey = 'default') {
		$xml = $this->xliffParser->getParsedData($file, $languageKey);
		return $this->flattenLocallangArray($xml, 'xlf' , $languageKey);
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
	 * @param string $type
	 * @throws \InvalidArgumentException
	 */
	public function prepareLabelArray($extension, $type = 'locallang') {
		$labelArray = array();
		foreach ($extension->getDomainObjects() as $domainObject) {
			/* @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject */
			$labelArray[$domainObject->getLabelNamespace()] = $this->inflector->humanize($domainObject->getName());
			foreach ($domainObject->getProperties() as $property) {
				$labelArray[$property->getLabelNamespace()] = $this->inflector->humanize($property->getName());
			}
			if ($type == 'locallang_db') {
				$tableToMapTo = $domainObject->getMapToTable();
				if (!empty($tableToMapTo)) {
					$labelArray[$tableToMapTo . '.tx_extbase_type.' . $domainObject->getRecordType()] = $extension->getName() . ' ' . $domainObject->getName();
				}
				if (count($domainObject->getChildObjects()) > 0) {
					$labelArray[$extension->getShortExtensionKey() . '.tx_extbase_type'] = 'Record Type';
					$labelArray[$extension->getShortExtensionKey() . '.tx_extbase_type.0'] = 'Default';
					$labelArray[$domainObject->getLabelNamespace() . '.tx_extbase_type.' . $domainObject->getRecordType()] = $extension->getName() . ' ' . $domainObject->getName();
				}
			}
		}
		return $labelArray;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
	 * @throws \InvalidArgumentException
	 */
	public function prepareLabelArrayForContextHelp($domainObject) {
		$labelArray = array();
		foreach ($domainObject->getProperties() as $property) {
			$labelArray[$property->getFieldName() . '.description'] = htmlspecialchars($property->getDescription());
		}
		return $labelArray;
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\BackendModule $backendModule
	 * @param string $type
	 * @throws \InvalidArgumentException
	 */
	public function prepareLabelArrayForBackendModule($backendModule) {
		$labelArray = array();
		$labelArray['mlang_tabs_tab'] = htmlspecialchars($backendModule->getName());
		$labelArray['mlang_labels_tabdescr'] = htmlspecialchars($backendModule->getDescription());
		$labelArray['mlang_tabs_tab'] = htmlspecialchars($backendModule->getTabLabel());
		return $labelArray;
	}

	/**
	 * reduces an array coming from Utility\GeneralUtility::xml2array or parseXliff
	 * to a simple index => label array
	 *
	 * @static
	 * @param array $array
	 * @param string $format xml/xlf
	 * @return array
	 */
	public function flattenLocallangArray($array, $format, $languageKey) {
		$cleanMergedLabelArray = array();
		if ($format == 'xlf') {
			foreach ($array[$languageKey] as $index => $label) {
				$cleanMergedLabelArray[$index] = $label[0]['source'];
			}
		} else {
			$cleanMergedLabelArray = $array['data'][$languageKey];
		}
		return $cleanMergedLabelArray;
	}
}