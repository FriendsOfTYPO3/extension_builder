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

/**
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 */
class ManyToManyRelation extends AnyToManyRelation
{
    /**
     * Returns the type for an ObjectStorage and its contained type based on a mm-relation.
     *
     * @return string The type.
     */
    public function getTypeForComment(): string
    {
        return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $this->getForeignClassName() . '>';
    }

    /**
     * returns the type hint to be used in the arguments list of the method.
     *
     * @return string The type hint.
     */
    public function getTypeHint(): string
    {
        return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage';
    }

    public function isOfTypeObjectStorage(): bool
    {
        return true;
    }

    /**
     * Is a MM table needed for this relation?
     *
     * @return bool
     */
    public function getUseMMTable(): bool
    {
        return true;
    }
}
