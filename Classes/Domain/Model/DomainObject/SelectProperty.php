<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Rens Admiraal
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

class SelectProperty extends AbstractProperty {
	/**
	 * the property's default value
	 *
	 * @var int
	 */
	protected $defaultValue = 0;

	public function getTypeForComment() {
		return 'integer';
	}

	public function getTypeHint() {
		return '';
	}

	public function getSqlDefinition() {
		return $this->getFieldName() . " int(11) DEFAULT '0' NOT NULL,";
	}
}
