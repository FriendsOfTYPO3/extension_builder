<?php
namespace EBT\ExtensionBuilder\Service;

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

use EBT\ExtensionBuilder\Utility\Inflector;
use TYPO3\CMS\Core\Utility;

/**
 * Helper for localization related stuff
 */
class LocalizationService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Core\Localization\Parser\XliffParser
     */
    protected $xliffParser = null;

    /**
     * @param \TYPO3\CMS\Core\Localization\Parser\XliffParser $xlifflParser
     */
    protected function getXliffParser()
    {
        if (is_null($this->xliffParser)) {
            $this->xliffParser = Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Localization\Parser\XliffParser');
        }
        return $this->xliffParser;
    }

    public function getLabelArrayFromFile($file, $languageKey = 'default')
    {
        $xliffParser = $this->getXliffParser();
        $xml = $xliffParser->getParsedData($file, $languageKey);
        return $this->flattenLocallangArray($xml, 'xlf', $languageKey);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @param string $type
     * @throws \InvalidArgumentException
     */
    public function prepareLabelArray($extension, $type = 'locallang')
    {
        $labelArray = array();
        foreach ($extension->getDomainObjects() as $domainObject) {
            /* @var \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject */
            $labelArray[$domainObject->getLabelNamespace()] = Inflector::humanize($domainObject->getName());
            foreach ($domainObject->getProperties() as $property) {
                $labelArray[$property->getLabelNamespace()] = Inflector::humanize($property->getName());
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
    public function prepareLabelArrayForContextHelp($domainObject)
    {
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
    public function prepareLabelArrayForBackendModule($backendModule)
    {
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
    public function flattenLocallangArray($array, $format, $languageKey)
    {
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
