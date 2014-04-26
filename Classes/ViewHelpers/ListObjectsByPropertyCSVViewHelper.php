<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Sebastian Gebhard
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
 * Renders a comma separated list of a specific property and a list of objects
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:listObjectsByPropertyCSV objects="{persons}" property="name" />
 * </code>
 *
 * Output:
 * Anthony,Billy,Chris
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class ListObjectsByPropertyCSVViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Renders a comma separated list of a specific property and a list of objects
	 * @param array $objects
	 * @param string $property
	 * @return string comma separated list of values
	 */
	public function render($objects, $property) {
		$values = array();
		foreach ($objects as $object) {
			if (method_exists($object, 'get' . ucfirst($property))) {
				eval('$values[] = $object->get' . ucfirst($property) . '();');
			}
		}
		return join(',', $values);
	}
}
