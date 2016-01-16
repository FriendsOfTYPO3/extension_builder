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
abstract class AbstractRelation extends \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty
{
    /**
     * the schema of the foreign class
     *
     * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject
     */
    protected $foreignModel = null;
    /**
     * the schema of the foreign class
     *
     * @var string
     */
    protected $foreignClassName = null;
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

    public function setRelatedToExternalModel($relatedToExternalModel)
    {
        $this->relatedToExternalModel = $relatedToExternalModel;
    }

    public function getRelatedToExternalModel()
    {
        return $this->relatedToExternalModel;
    }

    /**
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject The foreign class
     */
    public function getForeignModel()
    {
        return $this->foreignModel;
    }

    /**
     * @return string
     */
    public function getForeignDatabaseTableName()
    {
        if (is_object($this->foreignModel)) {
            return $this->foreignModel->getDatabaseTableName();
        } else {
            return $this->foreignDatabaseTableName;
        }
    }

    /**
     * @param string
     */
    public function setForeignDatabaseTableName($foreignDatabaseTableName)
    {
        $this->foreignDatabaseTableName = $foreignDatabaseTableName;
    }

    /**
     *
     * @return string The foreign class
     */
    public function getForeignClassName()
    {
        if (isset($this->foreignClassName)) {
            return $this->foreignClassName;
        }
        if (is_object($this->foreignModel)) {
            return $this->foreignModel->getFullQualifiedClassName();
        }
        return null;
    }

    public function getForeignModelName()
    {
        if (is_object($this->foreignModel)) {
            return $this->foreignModel->getName();
        }
        $parts = explode('\\Domain\\Model\\', $this->foreignClassName);
        return $parts[1];
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $foreignModel Set the foreign DomainObject of the relation
     */
    public function setForeignModel(\EBT\ExtensionBuilder\Domain\Model\DomainObject $foreignModel)
    {
        $this->foreignModel = $foreignModel;
    }

    /**
     *
     * @param string $foreignClassName Set the foreign class nsme of the relation
     */
    public function setForeignClassName($foreignClassName)
    {
        $this->foreignClassName = $foreignClassName;
    }

    /**
     * Sets the flag, if the relation should be rendered as IRRE field.
     *
     * @param bool $inlineEditing
     * @return void
     **/
    public function setInlineEditing($inlineEditing)
    {
        $this->inlineEditing = (bool)$inlineEditing;
    }

    /**
     * Returns the state of the flag, if the relation should be rendered as IRRE field.
     *
     * @return bool true if the field shopuld be rendered as IRRE field; false otherwise
     **/
    public function getInlineEditing()
    {
        return (bool)$this->inlineEditing;
    }

    /**
     * Sets the lazyLoading flag
     *
     * @param  $lazyLoading
     * @return void
     */
    public function setLazyLoading($lazyLoading)
    {
        $this->lazyLoading = $lazyLoading;
    }

    /**
     * Gets the lazyLoading flag
     *
     * @return bool
     */
    public function getLazyLoading()
    {
        return $this->lazyLoading;
    }

    public function getSqlDefinition()
    {
        return $this->getFieldName() . " int(11) unsigned DEFAULT '0' NOT NULL,";
    }

    /**
     * is displayable in the auto generated properties template
     *
     * this is only true for files and images
     *
     * @return bool
     */
    public function getIsDisplayable()
    {
        return $this->isFileReference();
    }

    /**
     * @return bool
     */
    public function isFileReference()
    {
        if ($this->foreignClassName == '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference') {
            return true;
        }
        return false;
    }

    /**
     * getter for allowed file types
     *
     * @return string
     */
    public function getAllowedFileTypes()
    {
        return $this->allowedFileTypes;
    }

    /**
     * setter for allowed file types
     *
     * @return string
     */
    public function setAllowedFileTypes($allowedFileTypes)
    {
        return $this->allowedFileTypes = $allowedFileTypes;
    }

    /**
     * getter for disallowed file types
     *
     * @return string
     */
    public function getDisallowedFileTypes()
    {
        return $this->disallowedFileTypes;
    }

    /**
     * setter for disallowed file types
     *
     * @return string
     */
    public function setDisallowedFileTypes($disallowedFileTypes)
    {
        return $this->disallowedFileTypes = $disallowedFileTypes;
    }


    /**
     * @return string
     */
    public function getRenderType()
    {
        return $this->renderType;
    }

    /**
     * @param string $renderType
     */
    public function setRenderType($renderType)
    {
        $this->renderType = $renderType;
    }
}
