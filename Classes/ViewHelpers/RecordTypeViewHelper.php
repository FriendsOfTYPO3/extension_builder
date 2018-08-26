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

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Utility\Tools;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class RecordTypeViewHelper
 */
class RecordTypeViewHelper extends AbstractViewHelper
{
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager
     */
    protected $configurationManager = null;

    /**
     * @param \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager $configurationManager
     * @return void
     */
    public function injectExtensionBuilderConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
    * Arguments Initialization
    */
    public function initializeArguments()
    {
        $this->registerArgument('domainObject', \EBT\ExtensionBuilder\Domain\Model\DomainObject::class, 'domainObject', TRUE);
    }

    /**
     * Helper function to find the parents class recordType
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function render()
    {
        $domainObject = $this->arguments['domainObject'];
        $classSettings = $this->configurationManager->getExtbaseClassConfiguration($domainObject->getParentClass());
        if (isset($classSettings['recordType'])) {
            $parentRecordType = Tools::convertClassNameToRecordType($classSettings['recordType']);
        } else {
            $parentRecordType = Tools::convertClassNameToRecordType($domainObject->getParentClass());
            $existingTypes = $GLOBALS['TCA'][$domainObject->getDatabaseTableName()]['types'];
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
