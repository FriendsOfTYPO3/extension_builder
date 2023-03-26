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

use EBT\ExtensionBuilder\Service\ValidationService;

/**
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 */
class ZeroToManyRelation extends AnyToManyRelation
{
    protected string $foreignKeyName = '';

    protected bool $cascadeRemove = true;

    public function getTypeForComment(): string
    {
        return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $this->getForeignClassName() . '>';
    }

    public function getTypeHint(): string
    {
        return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage';
    }

    public function getForeignKeyName(): string
    {
        if (empty($this->foreignKeyName)) {
            $foreignKeyName = strtolower($this->getDomainObject()->getName());
            if (ValidationService::isReservedMYSQLWord($foreignKeyName)) {
                $foreignKeyName = 'tx_' . $foreignKeyName;
            }
            return $foreignKeyName;
        }

        return $this->foreignKeyName;
    }

    public function setForeignKeyName($foreignKeyName): void
    {
        $this->foreignKeyName = $foreignKeyName;
    }

    public function getUseMMTable(): bool
    {
        return false;
    }
}
