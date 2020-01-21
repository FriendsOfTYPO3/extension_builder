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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Indentation ViewHelper
 */
class ListForeignKeyRelationsViewHelper extends AbstractViewHelper
{

    /**
    * Arguments Initialization
    */
    public function initializeArguments()
    {
        $this->registerArgument('domainObject', \EBT\ExtensionBuilder\Domain\Model\DomainObject::class, 'domainObject', TRUE);
    }

    /**
     * @return array
     */
    public function render()
    {
        $expectedDomainObject = $this->arguments['domainObject'];
        $extension = $expectedDomainObject->getExtension();
        $results = [];
        foreach ($extension->getDomainObjects() as $domainObject) {
            if (!count($domainObject->getProperties())) {
                continue;
            }
            foreach ($domainObject->getProperties() as $property) {
                if ($property instanceof ZeroToManyRelation
                    && $property->getRenderType() === 'inline'
                    && $property->getForeignClassName() === $expectedDomainObject->getFullQualifiedClassName()
                ) {
                    $results[] = $property;
                }
            }
        }
        return $results;
    }
}
