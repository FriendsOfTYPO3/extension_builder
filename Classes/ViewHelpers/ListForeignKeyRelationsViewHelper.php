<?php
namespace EBT\ExtensionBuilder\ViewHelpers;
/*                                                                        *
 * This script belongs to the TYPO3 package "Extension Builder".                  *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Indentation ViewHelper
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope prototype
 */
class ListForeignKeyRelationsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\Extension $extension
	 * @param mixed $domainObject
	 * @return boolean TRUE or FALSE
	 */
	public function render($extension, $domainObject) {
		$expectedDomainObject = $domainObject;
		$results = array();
		foreach ($extension->getDomainObjects() as $domainObject) {
			if (!count($domainObject->getProperties())) continue;
			foreach ($domainObject->getProperties() as $property) {
				if ($property instanceof \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation
					&& $property->getForeignClassName() === $expectedDomainObject->getFullQualifiedClassName()
				) {
					$results[] = $property;
				}
			}
		}
		return $results;
	}

}
