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

namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

class PassThroughProperty extends AbstractProperty
{
    /**
     * Is this property displayable inside a Fluid template?
     *
     * @return bool true if this property can be displayed inside a fluid template
     */
    public function getIsDisplayable(): bool
    {
        return false;
    }

    public function getTypeForComment(): string
    {
        return 'mixed';
    }

    public function getTypeHint(): string
    {
        return '';
    }

    public function getSqlDefinition(): string
    {
        return $this->getFieldName() . " varchar(255) NOT NULL DEFAULT '',";
    }
}
