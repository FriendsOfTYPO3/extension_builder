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

namespace EBT\ExtensionBuilder\Service;

use EBT\ExtensionBuilder\Domain\Model\BackendModule;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Utility\Inflector;
use InvalidArgumentException;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Helper for localization related stuff
 */
class LocalizationService implements SingletonInterface
{
    protected ?XliffParser $xliffParser = null;

    protected function getXliffParser(): XliffParser
    {
        if ($this->xliffParser === null) {
            $this->xliffParser = GeneralUtility::makeInstance(XliffParser::class);
        }
        return $this->xliffParser;
    }

    public function getLabelArrayFromFile($file, $languageKey = 'default'): array
    {
        $xliffParser = $this->getXliffParser();
        $xml = $xliffParser->getParsedData($file, $languageKey);
        return $this->flattenLocallangArray($xml, 'xlf', $languageKey);
    }

    /**
     * @param Extension $extension
     * @param string $type
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function prepareLabelArray(Extension $extension, string $type = 'locallang'): array
    {
        $labelArray = [];
        foreach ($extension->getDomainObjects() as $domainObject) {
            /* @var DomainObject $domainObject */
            $labelArray[$domainObject->getLabelNamespace()] = Inflector::humanize($domainObject->getName());
            $labelArray[$domainObject->getDescriptionNamespace()] = Inflector::humanize($domainObject->getDescription());
            foreach ($domainObject->getProperties() as $property) {
                $labelArray[$property->getLabelNamespace()] = Inflector::humanize($property->getName());
                $labelArray[$property->getDescriptionNamespace()] = Inflector::humanize($property->getDescription());
            }
            if ($type === 'locallang_db.xlf') {
                $tableToMapTo = $domainObject->getMapToTable();
                if (!empty($tableToMapTo)) {
                    $labelArray[$tableToMapTo . '.tx_extbase_type.' . $domainObject->getRecordType()] = $extension->getName() . ' ' . $domainObject->getName();
                    $labelArray[$extension->getShortExtensionKey() . '.tx_extbase_type'] = 'Record Type';
                }
                if (count($domainObject->getChildObjects()) > 0) {
                    $labelArray[$extension->getShortExtensionKey() . '.tx_extbase_type.0'] = 'Default';
                    $labelArray[$domainObject->getLabelNamespace() . '.tx_extbase_type.' . $domainObject->getRecordType()] = $extension->getName() . ' ' . $domainObject->getName();
                }
            }
        }
        if ($type === 'locallang_db.xlf' && $extension->hasPlugins()) {
            foreach ($extension->getPlugins() as $plugin) {
                $labelArray['tx_' . $extension->getExtensionKey() . '_' . $plugin->getKey() . '.name'] = $plugin->getName();
                $labelArray['tx_' . $extension->getExtensionKey() . '_' . $plugin->getKey() . '.description'] = $plugin->getDescription();
            }
        }
        return $labelArray;
    }

    /**
     * @param DomainObject $domainObject
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function prepareLabelArrayForContextHelp(DomainObject $domainObject): array
    {
        $labelArray = [];
        foreach ($domainObject->getProperties() as $property) {
            $labelArray[$property->getFieldName() . '.description'] = htmlspecialchars($property->getDescription());
        }
        return $labelArray;
    }

    public function prepareLabelArrayForBackendModule(BackendModule $backendModule): array
    {
        return [
            'mlang_tabs_tab' => htmlspecialchars($backendModule->getTabLabel()),
            'mlang_labels_tablabel' => htmlspecialchars($backendModule->getDescription()),
        ];
    }

    /**
     * reduces an array coming from Utility\GeneralUtility::xml2array or parseXliff
     * to a simple index => label array
     *
     * @static
     *
     * @param array $array
     * @param string $format xml/xlf
     * @param string $languageKey
     *
     * @return array
     */
    public function flattenLocallangArray(array $array, string $format, string $languageKey): array
    {
        $cleanMergedLabelArray = [];
        if ($format === 'xlf') {
            foreach ($array[$languageKey] as $index => $label) {
                $cleanMergedLabelArray[$index] = $label[0]['source'];
            }
        } else {
            $cleanMergedLabelArray = $array['data'][$languageKey];
        }
        return $cleanMergedLabelArray;
    }
}
