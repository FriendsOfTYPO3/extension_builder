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
class ZeroToOneRelation extends AbstractRelation
{
    public function getTypeForComment()
    {
        return $this->getForeignClassName();
    }

    public function getTypeHint()
    {
        return $this->getForeignClassName();
    }

    public function getSqlDefinition()
    {
        return $this->getFieldName() . " int(11) unsigned DEFAULT '0',";
    }

    public function getUnqualifiedType()
    {
        return $this->getForeignModelName();
    }
}
