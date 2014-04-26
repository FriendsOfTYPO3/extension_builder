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
class ManyToManyRelation extends AnyToManyRelation {
	/**
	 * Returns the type for an ObjectStorage and its contained type based on a mm-relation.
	 *
	 * @return string The type.
	 */
	public function getTypeForComment() {
		return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $this->getForeignClassName() . '>';
	}

	/**
	 * returns the type hint to be used in the arguments list of the method.
	 *
	 * @return string The type hint.
	 */
	public function getTypeHint() {
		return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage';
	}


	/**
	 *
	 * @return boolean
	 */
	public function isOfTypeObjectStorage() {
		return TRUE;
	}

	/**
	 * Is a MM table needed for this relation?
	 *
	 * @return boolean
	 */
	public function getUseMMTable() {
		return TRUE;
	}

}
