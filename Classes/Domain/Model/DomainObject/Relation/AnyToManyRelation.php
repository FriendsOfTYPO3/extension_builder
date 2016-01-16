<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation;

use EBT\ExtensionBuilder\Domain\Model\DomainObject;

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

abstract class AnyToManyRelation extends AbstractRelation
{
    /**
     * The mm relation table name
     *
     * @var string
     */
    protected $relationTableName = '';

    /**
     * Use tbl1_field1_tbl2_mm as table name to enable multiple relations
     * to the same foreign class
     *
     * @var bool
     */
    protected $useExtendedRelationTableName = false;

    /**
     * @var int
     */
    protected $maxItems = 1;

    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    protected $domainObject = null;


    /**
     * Returns the relation table name. It is build by having 'tx_myextension_' followed by the
     * first domain object name followed by the second domain object name followed by '_mm'.
     *
     * @return string
     */
    public function getRelationTableName()
    {
        if (!empty($this->relationTableName)) {
            return $this->relationTableName;
        }
        $relationTableName = 'tx_' . str_replace('_', '', $this->domainObject->getExtension()->getExtensionKey()) . '_';
        $relationTableName .= strtolower($this->domainObject->getName());

        if ($this->useExtendedRelationTableName) {
            $relationTableName .= '_' . strtolower($this->getName());
        }
        $relationTableName .= '_' . strtolower($this->getForeignModelName()) . '_mm';

        return $relationTableName;
    }

    /**
     * Setter for useExtendedRelationTableName
     * @param bool $useExtendedRelationTableName
     */
    public function setUseExtendedRelationTableName($useExtendedRelationTableName)
    {
        $this->useExtendedRelationTableName = $useExtendedRelationTableName;
    }

    /**
     * setter for relation table name
     * if a table name is configured in TCA the table name is ste to the configured name
     *
     * @param $relationTableName
     * @return void
     */
    public function setRelationTableName($relationTableName)
    {
        $this->relationTableName = $relationTableName;
    }

    /**
     * Is a MM table needed for this relation?
     *
     * @return bool
     */
    public function getUseMMTable()
    {
        if ($this->getInlineEditing()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;
    }

}
