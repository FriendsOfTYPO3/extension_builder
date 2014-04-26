<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Ingmar Schlecht, 2013 Nico de Haen
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Creates a request an dispatches it to the controller which was specified
 * by TS Setup, Flexform and returns the content to the v4 framework.
 *
 * This class is the main entry point for extbase extensions in the frontend.
 */
abstract class AbstractRelation extends \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty {
	/**
	 * the schema of the foreign class
	 *
	 * @var \EBT\ExtensionBuilder\Domain\Model\DomainObject
	 */
	protected $foreignModel = NULL;

	/**
	 * the schema of the foreign class
	 *
	 * @var string
	 */
	protected $foreignClassName = NULL;

	/**
	 * @var string
	 */
	protected $foreignDatabaseTableName = '';

	/**
	 * If this flag is set to TRUE, the relation is rendered as IRRE field (Inline Relational Record Editing).
	 * Default is FALSE.
	 *
	 * @var bool
	 */
	protected $inlineEditing = FALSE;

	/**
	 * If this flag is set to TRUE, the relation will be lazy loading. Default is FALSE
	 *
	 * @var bool
	 */
	protected $lazyLoading = FALSE;

	/**
	 * @var bool
	 */
	protected $relatedToExternalModel = FALSE;

	public function setRelatedToExternalModel($relatedToExternalModel) {
		$this->relatedToExternalModel = $relatedToExternalModel;
	}

	public function getRelatedToExternalModel() {
		return $this->relatedToExternalModel;
	}

	/**
	 *
	 * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject The foreign class
	 */
	public function getForeignModel() {
		return $this->foreignModel;
	}

	/**
	 * @return string
	 */
	public function getForeignDatabaseTableName() {
		if (is_object($this->foreignModel)) {
			return $this->foreignModel->getDatabaseTableName();
		} else {
			return $this->foreignDatabaseTableName;
		}
	}

	/**
	 * @param string
	 */
	public function setForeignDatabaseTableName( $foreignDatabaseTableName) {
		$this->foreignDatabaseTableName = $foreignDatabaseTableName;
	}

	/**
	 *
	 * @return string The foreign class
	 */
	public function getForeignClassName() {
		if (isset($this->foreignClassName)) {
			return $this->foreignClassName;
		}
		if (is_object($this->foreignModel)) {
			return $this->foreignModel->getFullQualifiedClassName();
		}
		return NULL;
	}

	public function getForeignModelName() {
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
	public function setForeignModel(\EBT\ExtensionBuilder\Domain\Model\DomainObject $foreignModel) {
		$this->foreignModel = $foreignModel;
	}

	/**
	 *
	 * @param string $foreignClassName Set the foreign class nsme of the relation
	 */
	public function setForeignClassName( $foreignClassName) {
		$this->foreignClassName = $foreignClassName;
	}

	/**
	 * Sets the flag, if the relation should be rendered as IRRE field.
	 *
	 * @param bool $inlineEditing
	 * @return void
	 **/
	public function setInlineEditing($inlineEditing) {
		$this->inlineEditing = (bool)$inlineEditing;
	}

	/**
	 * Returns the state of the flag, if the relation should be rendered as IRRE field.
	 *
	 * @return bool TRUE if the field shopuld be rendered as IRRE field; FALSE otherwise
	 **/
	public function getInlineEditing() {
		return (bool)$this->inlineEditing;
	}

	/**
	 * Sets the lazyLoading flag
	 *
	 * @param  $lazyLoading
	 * @return void
	 */
	public function setLazyLoading($lazyLoading) {
		$this->lazyLoading = $lazyLoading;
	}

	/**
	 * Gets the lazyLoading flag
	 *
	 * @return bool
	 */
	public function getLazyLoading() {
		return $this->lazyLoading;
	}

	public function getSqlDefinition() {
		return $this->getFieldName() . " int(11) unsigned DEFAULT '0' NOT NULL,";
	}

	/**
	 * is displayable in the auto generated properties template
	 *
	 * this is only true for files and images
	 *
	 * @return bool
	 */
	public function getIsDisplayable() {
		return $this->isFileReference();
	}

	/**
	 * @return bool
	 */
	public function isFileReference() {
		if ($this->foreignClassName == '\\TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference') {
			return TRUE;
		}
		return FALSE;
	}

}
