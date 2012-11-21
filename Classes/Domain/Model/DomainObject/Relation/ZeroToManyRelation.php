<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Ingmar Schlecht
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
 *
 * @version $ID:$
 */
class Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ZeroToManyRelation extends Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_AnyToManyRelation {

	/**
	 * @var string
	 */
	protected $foreignKeyName;

	public function getTypeForComment() {
		return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage<' . $this->getForeignClassName() . '>';
	}

	public function getTypeHint() {
		return '\\TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage';
	}

	public function getForeignKeyName() {
		if(empty($this->foreignKeyName)) {
			return strtolower($this->getDomainObject()->getName());
		} else {
			return $this->foreignKeyName;
		}
	}

	public function setForeignKeyName($foreignKeyName) {
		$this->foreignKeyName = $foreignKeyName;
	}

	/**
	 * Overwrite parent function
	 *
	 * @return void
	 */
	public function getUseMMTable() {
		return FALSE;
	}

}

?>
