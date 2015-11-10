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

class NativeDateProperty extends AbstractProperty
{
    /**
     * the property's default value
     *
     * @var \DateTime
     */
    protected $defaultValue = null;

    public function getTypeForComment()
    {
        return '\\DateTime';
    }

    public function getTypeHint()
    {
        return '\\DateTime';
    }

    public function getSqlDefinition()
    {
        return $this->getFieldName() . " date DEFAULT '0000-00-00',";
    }

    public function getNameToBeDisplayedInFluidTemplate()
    {
        return $this->name . ' -> f:format.date()';
    }
}
