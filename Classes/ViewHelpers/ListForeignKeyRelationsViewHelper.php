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
 * Indentation ViewHelper
 *
 */
class ListForeignKeyRelationsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
     * @param mixed $domainObject
     * @return bool true or false
     */
    public function render($extension, $domainObject)
    {
        $expectedDomainObject = $domainObject;
        $results = array();
        foreach ($extension->getDomainObjects() as $domainObject) {
            if (!count($domainObject->getProperties())) {
                continue;
            }
            foreach ($domainObject->getProperties() as $property) {
                if ($property instanceof \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation
                    && $property->getForeignClassName() === $expectedDomainObject->getFullQualifiedClassName()
                ) {
                    $results[] = $property;
                }
            }
        }
        return $results;
    }
}
