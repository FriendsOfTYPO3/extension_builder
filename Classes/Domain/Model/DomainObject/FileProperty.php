<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nico de Haen
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
 * File property
 */
class FileProperty extends AbstractProperty {
	/**
	 * the property's default value
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $defaultValue = NULL;

	/**
	 * allowed file types for this property
	 *
	 * @var string (comma separated filetypes)
	 */
	protected $allowedFileTypes = '*';

	/**
	 * not allowed file types for this property (comma-separated file types)
	 *
	 * @var string
	 */
	protected $disallowedFileTypes = 'php';

	/**
	 * flag that this property needs an upload folder
	 *
	 * @var bool
	 */
	protected $needsUploadFolder = TRUE;

	/**
	 * @var int
	 */
	protected $maxItems = 1;

	/**
	 * @var string
	 */
	protected $type = 'File';

	public function getTypeForComment() {
		return '\TYPO3\CMS\Extbase\Domain\Model\FileReference';
	}

	public function getTypeHint() {
		return '\TYPO3\CMS\Extbase\Domain\Model\FileReference';
	}

	public function getSqlDefinition() {
		return $this->getFieldName() . " int(11) unsigned NOT NULL default '0',";
	}

	/**
	 * getter for allowed file types
	 *
	 * @return boolean
	 */
	public function getAllowedFileTypes() {
		return $this->allowedFileTypes;
	}

	/**
	 * setter for allowed file types
	 *
	 * @return string
	 */
	public function setAllowedFileTypes($allowedFileTypes) {
		return $this->allowedFileTypes = $allowedFileTypes;
	}

	/**
	 * getter for disallowed file types
	 *
	 * @return boolean
	 */
	public function getDisallowedFileTypes() {
		return $this->disallowedFileTypes;
	}

	/**
	 * setter for disallowed file types
	 *
	 * @return string
	 */
	public function setDisallowedFileTypes($disallowedFileTypes) {
		return $this->disallowedFileTypes = $disallowedFileTypes;
	}

	/**
	 * The string to be used inside object accessors to display this property.
	 *
	 * @return string
	 */
	public function getNameToBeDisplayedInFluidTemplate() {
		return $this->name . '.originalResource.name';
	}

	/**
	 * @return int
	 */
	public function getMaxItems() {
		return $this->maxItems;
	}

	/**
	 * @param int $maxItems
	 */
	public function setMaxItems($maxItems) {
		$this->maxItems = $maxItems;
	}

}
