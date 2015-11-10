<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

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

class FloatProperty extends AbstractProperty
{
    /**
     * the property's default value
     *
     * @var float
     */
    protected $defaultValue = 0.0;

    public function getTypeForComment()
    {
        return 'float';
    }

    public function getTypeHint()
    {
        return '';
    }

    public function getSqlDefinition()
    {
        return $this->getFieldName() . " double(11,2) DEFAULT '0.00' NOT NULL,";
    }
}
