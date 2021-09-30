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

namespace EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation;

abstract class AnyToManyRelation extends AbstractRelation
{
    /**
     * The mm relation table name
     */
    protected string $relationTableName = '';

    /**
     * Use tbl1_field1_tbl2_mm as table name to enable multiple relations
     * to the same foreign class
     */
    protected bool $useExtendedRelationTableName = false;

    protected int $maxItems = 1;

    /**
     * Returns the relation table name. It is build by having 'tx_myextension_' followed by the
     * first domain object name followed by the second domain object name followed by '_mm'.
     *
     * @return string
     */
    public function getRelationTableName(): string
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

    public function setUseExtendedRelationTableName(bool $useExtendedRelationTableName): void
    {
        $this->useExtendedRelationTableName = $useExtendedRelationTableName;
    }

    /**
     * setter for relation table name
     * if a table name is configured in TCA the table name is ste to the configured name
     *
     * @param string $relationTableName
     */
    public function setRelationTableName(string $relationTableName): void
    {
        $this->relationTableName = $relationTableName;
    }

    /**
     * Is a MM table needed for this relation?
     *
     * @return bool
     */
    public function getUseMMTable(): bool
    {
        if ($this->getInlineEditing()) {
            return false;
        }

        return true;
    }

    public function getMaxItems(): int
    {
        return $this->maxItems;
    }

    public function setMaxItems(int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }
}
