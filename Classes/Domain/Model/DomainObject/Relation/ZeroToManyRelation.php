<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation;

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

/**
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 */
class ZeroToManyRelation extends AnyToManyRelation
{
    /**
     * @var string
     */
    protected $foreignKeyName = '';

    public function getTypeForComment()
    {
        return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $this->getForeignClassName() . '>';
    }

    public function getTypeHint()
    {
        return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage';
    }

    public function getForeignKeyName()
    {
        if (empty($this->foreignKeyName)) {
            $foreignKeyName = strtolower($this->getDomainObject()->getName());
            if (\EBT\ExtensionBuilder\Service\ValidationService::isReservedMYSQLWord($foreignKeyName)) {
                $foreignKeyName = 'tx_' . $foreignKeyName;
            }
            return $foreignKeyName;
        } else {
            return $this->foreignKeyName;
        }
    }

    public function setForeignKeyName($foreignKeyName)
    {
        $this->foreignKeyName = $foreignKeyName;
    }

    /**
     * Overwrite parent function
     *
     * @return bool
     */
    public function getUseMMTable()
    {
        return false;
    }

    /**
     *
     * @return bool true (if property is of type relation any to many)
     */
    public function isZeroToManyRelation()
    {
        return true;
    }
}
