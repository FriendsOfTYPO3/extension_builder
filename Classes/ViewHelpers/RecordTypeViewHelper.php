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

namespace EBT\ExtensionBuilder\ViewHelpers;

use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Utility\Tools;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RecordTypeViewHelper extends AbstractViewHelper
{
    private DataMapper $dataMapper;

    public function injectDataMapper(DataMapper $dataMapper): void
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * Arguments Initialization
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('domainObject', DomainObject::class, 'domainObject', true);
    }

    /**
     * Helper function to find the parents class recordType
     *
     * @throws Exception
     */
    public function render(): string
    {
        $domainObject = $this->arguments['domainObject'];
        $recordType = null;
        if (!empty($domainObject->getParentClass())) {
            $recordType = $this->dataMapper->getDataMap($domainObject->getParentClass())->getRecordType();
        }
        if ($recordType) {
            $parentRecordType = Tools::convertClassNameToRecordType($recordType);
        } else {
            $parentRecordType = Tools::convertClassNameToRecordType($domainObject->getParentClass());
            $existingTypes = $GLOBALS['TCA'][$domainObject->getDatabaseTableName()]['types'];
            if (is_array($existingTypes) && !isset($existingTypes[$parentRecordType])) {
                // no types field for parent record type configured, use the default type 1
                if (isset($existingTypes['1'])) {
                    $parentRecordType = 1;
                } else {
                    //if it not exists get first existing key
                    $keys = array_keys($existingTypes);
                    $parentRecordType = reset($keys);
                }
            }
        }
        $parts = explode('\\', (string) $domainObject->getParentClass());
        $this->templateVariableContainer->add('parentModelName', end($parts));
        $this->templateVariableContainer->add('parentRecordType', $parentRecordType);
        $content = $this->renderChildren();
        $this->templateVariableContainer->remove('parentRecordType');
        $this->templateVariableContainer->remove('parentModelName');

        return $content;
    }
}
