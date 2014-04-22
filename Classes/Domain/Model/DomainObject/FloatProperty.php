<?php
namespace EBT\ExtensionBuilder\Domain\Model\DomainObject;
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
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FloatProperty extends AbstractProperty {
	/**
	 * the property's default value
	 *
	 * @var float
	 */
	protected $defaultValue = 0.0;

	public function getTypeForComment() {
		return 'float';
	}

	public function getTypeHint() {
		return '';
	}

	public function getSqlDefinition() {
		return $this->getFieldName() . " double(11,2) DEFAULT '0.00' NOT NULL,";
	}
}
