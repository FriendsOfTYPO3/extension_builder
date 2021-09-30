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

class TimeProperty extends AbstractProperty
{
    /**
     * the property's default value
     *
     * @var int
     */
    protected $defaultValue = 0;
    protected static bool $isNullable = true;

    public function getTypeForComment(): string
    {
        return ($this->nullable) ? 'int|null' : 'int';
    }

    public function getTypeHint(): string
    {
        return ($this->nullable) ? '?int' : 'int';
    }

    public function getSqlDefinition(): string
    {
        return ($this->nullable)
            ? $this->getFieldName() . ' int(11) DEFAULT NULL,'
            : $this->getFieldName() . " int(11) NOT NULL DEFAULT '0',";
    }
}
