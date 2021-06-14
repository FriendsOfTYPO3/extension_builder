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

use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;

/**
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 */
abstract class AbstractRelation extends AbstractProperty
{
    /**
     * the schema of the foreign class
     *
     * @var DomainObject
     */
    protected $foreignModel;
    /**
     * the schema of the foreign class
     *
     * @var string
     */
    protected $foreignClassName;
    /**
     * @var string
     */
    protected $foreignDatabaseTableName = '';
    /**
     * If this flag is set to true, the relation is rendered as IRRE field (Inline Relational Record Editing).
     * Default is false.
     *
     * @var bool
     */
    protected $inlineEditing = false;
    /**
     * If this flag is set to true, the relation will be lazy loading. Default is false
     *
     * @var bool
     */
    protected $lazyLoading = false;
    /**
     * @var bool
     */
    protected $relatedToExternalModel = false;
    /**
     * allowed file types for this relation
     *
     * @var string (comma separated filetypes)
     */
    protected $allowedFileTypes = '';
    /**
     * not allowed file types for this relation (comma-separated file types)
     *
     * @var string
     */
    protected $disallowedFileTypes = 'php';

    /**
     * @var string
     */
    protected $renderType = '';

    public function setRelatedToExternalModel($relatedToExternalModel): void
    {
        $this->relatedToExternalModel = $relatedToExternalModel;
    }

    public function getRelatedToExternalModel()
    {
        return $this->relatedToExternalModel;
    }

    /**
     * @return DomainObject The foreign class
     */
    public function getForeignModel(): ?DomainObject
    {
        return $this->foreignModel;
    }

    /**
     * @return string
     */
    public function getForeignDatabaseTableName(): string
    {
        if (is_object($this->foreignModel)) {
            return $this->foreignModel->getDatabaseTableName();
        }

        return $this->foreignDatabaseTableName;
    }

    /**
     * @param string
     */
    public function setForeignDatabaseTableName(string $foreignDatabaseTableName): void
    {
        $this->foreignDatabaseTableName = $foreignDatabaseTableName;
    }

    /**
     * @return string The foreign class
     */
    public function getForeignClassName(): ?string
    {
        if (isset($this->foreignClassName)) {
            return $this->foreignClassName;
        }
        if (is_object($this->foreignModel)) {
            return $this->foreignModel->getFullQualifiedClassName();
        }
        return null;
    }

    public function getForeignModelName(): string
    {
        if (is_object($this->foreignModel)) {
            return $this->foreignModel->getName();
        }
        $parts = explode('\\Domain\\Model\\', $this->foreignClassName);
        return $parts[1];
    }

    /**
     * @param DomainObject $foreignModel Set the foreign DomainObject of the relation
     */
    public function setForeignModel(DomainObject $foreignModel): void
    {
        $this->foreignModel = $foreignModel;
    }

    /**
     * @param string $foreignClassName Set the foreign class nsme of the relation
     */
    public function setForeignClassName(string $foreignClassName): void
    {
        $this->foreignClassName = $foreignClassName;
    }

    /**
     * Sets the flag, if the relation should be rendered as IRRE field.
     *
     * @param bool $inlineEditing
     **/
    public function setInlineEditing($inlineEditing): void
    {
        $this->inlineEditing = (bool)$inlineEditing;
    }

    /**
     * Returns the state of the flag, if the relation should be rendered as IRRE field.
     *
     * @return bool true if the field shopuld be rendered as IRRE field; false otherwise
     **/
    public function getInlineEditing(): bool
    {
        return (bool)$this->inlineEditing;
    }

    /**
     * Sets the lazyLoading flag
     *
     * @param  $lazyLoading
     */
    public function setLazyLoading($lazyLoading): void
    {
        $this->lazyLoading = $lazyLoading;
    }

    /**
     * Gets the lazyLoading flag
     *
     * @return bool
     */
    public function getLazyLoading(): bool
    {
        return $this->lazyLoading;
    }

    public function getSqlDefinition(): string
    {
        // store 1:n relationships as comma separated list in case `select*` renderType is used
        if ($this instanceof ZeroToManyRelation && strpos($this->renderType, 'select') === 0) {
            return $this->getFieldName() . ' text NOT NULL,';
        }
        return $this->getFieldName() . " int(11) unsigned NOT NULL DEFAULT '0',";
    }

    /**
     * is displayable in the auto generated properties template
     *
     * this is only true for files and images
     *
     * @return bool
     */
    public function getIsDisplayable(): bool
    {
        return $this->isFileReference();
    }

    /**
     * @return bool
     */
    public function isFileReference(): bool
    {
        return $this->foreignClassName === '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference';
    }

    /**
     * getter for allowed file types
     *
     * @return string
     */
    public function getAllowedFileTypes(): string
    {
        return $this->allowedFileTypes;
    }

    /**
     * setter for allowed file types
     *
     * @param $allowedFileTypes
     *
     * @return string
     */
    public function setAllowedFileTypes($allowedFileTypes): string
    {
        return $this->allowedFileTypes = $allowedFileTypes;
    }

    /**
     * getter for disallowed file types
     *
     * @return string
     */
    public function getDisallowedFileTypes(): string
    {
        return $this->disallowedFileTypes;
    }

    /**
     * setter for disallowed file types
     *
     * @param $disallowedFileTypes
     *
     * @return string
     */
    public function setDisallowedFileTypes($disallowedFileTypes): string
    {
        return $this->disallowedFileTypes = $disallowedFileTypes;
    }

    /**
     * @return string
     */
    public function getRenderType(): string
    {
        return $this->renderType;
    }

    /**
     * @param string $renderType
     */
    public function setRenderType($renderType): void
    {
        $this->renderType = $renderType;
    }
}
