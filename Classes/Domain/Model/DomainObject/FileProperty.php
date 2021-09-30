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

namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;

/**
 * File property
 */
class FileProperty extends AbstractProperty
{
    /**
     * the property's default value
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $defaultValue;
    /**
     * allowed file types for this property
     *
     * @var string (comma separated filetypes)
     */
    protected string $allowedFileTypes = '';
    /**
     * not allowed file types for this property (comma-separated file types)
     */
    protected string $disallowedFileTypes = 'php';
    protected int $maxItems = 1;
    protected ?string $type = 'File';
    protected bool $cascadeRemove = true;

    public function getTypeForComment(): string
    {
        return '\TYPO3\CMS\Extbase\Domain\Model\FileReference';
    }

    public function getTypeHint(): string
    {
        return '\TYPO3\CMS\Extbase\Domain\Model\FileReference';
    }

    public function getSqlDefinition(): string
    {
        return ($this->nullable)
            ? $this->getFieldName() . ' int(11) unsigned DEFAULT NULL,'
            : $this->getFieldName() . " int(11) unsigned NOT NULL DEFAULT '0',";
    }

    public function getAllowedFileTypes(): string
    {
        return $this->allowedFileTypes;
    }

    public function setAllowedFileTypes(string $allowedFileTypes): string
    {
        return $this->allowedFileTypes = $allowedFileTypes;
    }

    public function getDisallowedFileTypes(): string
    {
        return $this->disallowedFileTypes;
    }

    public function setDisallowedFileTypes(string $disallowedFileTypes): string
    {
        return $this->disallowedFileTypes = $disallowedFileTypes;
    }

    /**
     * The string to be used inside object accessors to display this property.
     *
     * @return string
     */
    public function getNameToBeDisplayedInFluidTemplate(): string
    {
        return $this->name . '.originalResource.name';
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
