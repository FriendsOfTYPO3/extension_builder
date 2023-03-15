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

use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ListForeignKeyRelationsViewHelper extends AbstractViewHelper
{
    /**
    * Arguments Initialization
    */
    public function initializeArguments(): void
    {
        $this->registerArgument('domainObject', DomainObject::class, 'domainObject', true);
    }

    public function render(): array
    {
        $expectedDomainObject = $this->arguments['domainObject'];
        $extension = $expectedDomainObject->getExtension();
        $results = [];
        foreach ($extension->getDomainObjects() as $domainObject) {
            if ((is_countable($domainObject->getProperties()) ? count($domainObject->getProperties()) : 0) === 0) {
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
