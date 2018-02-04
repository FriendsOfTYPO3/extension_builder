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

class BooleanProperty extends AbstractProperty
{
    /**
     * the property's default value
     *
     * @var bool
     */
    protected $defaultValue = false;

    /**
     * @return string
     */
    public function getTypeForComment()
    {
        return 'bool';
    }

    /**
     * @return string
     */
    public function getTypeHint()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getSqlDefinition()
    {
        return $this->getFieldName() . " smallint(5) unsigned DEFAULT '0' NOT NULL,";
    }
}
