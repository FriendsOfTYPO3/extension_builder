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
class ZeroToOneRelation extends AbstractRelation
{
    public function getTypeForComment(): ?string
    {
        return $this->getForeignClassName();
    }

    public function getTypeHint(): ?string
    {
        return $this->getForeignClassName();
    }

    public function getSqlDefinition(): string
    {
        return $this->getFieldName() . " int(11) unsigned DEFAULT '0',";
    }

    public function getUnqualifiedType(): string
    {
        return $this->getForeignModelName();
    }
}
