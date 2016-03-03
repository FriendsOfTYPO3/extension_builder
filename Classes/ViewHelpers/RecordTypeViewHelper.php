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

/**
 * Class RecordTypeViewHelper
 */
class RecordTypeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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

    /**
     * Helper function to find the parents class recordType
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @return string
     */
    public function render(\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject)
    {
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
        $this->templateVariableContainer->add('parentModelName', end(explode('\\', $domainObject->getParentClass())));
        $this->templateVariableContainer->add('parentRecordType', $parentRecordType);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('parentRecordType');
        $this->templateVariableContainer->remove('parentModelName');

        return $content;
    }
}
