<?php
namespace EBT\ExtensionBuilder\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;
use TYPO3\CMS\Core\Utility;

/**
 * provides helper methods
 *
 */
class Tools implements \TYPO3\CMS\Core\SingletonInterface {

	public static function parseTableNameFromClassName($className) {
		if (strpos($className,'\\') !== FALSE) {
			if (strpos($className,'\\') === 0) {
				// remove trailing slash
				$className = substr($className, 1);
			}
			$classNameParts = explode('\\', $className, 6);
		} else {
			$classNameParts = explode('_', $className, 6);
		}
		// could be: TYPO3\CMS\Extbase\Domain\Model\FrontendUser
		// or: VENDOR\Extension\Domain\Model\Foo
		if (count($classNameParts) > 5) {
			$tableName= strtolower('tx_' .  implode('_',array_slice($classNameParts,2)));
		} else {
			$tableName= strtolower('tx_' .  implode('_',array_slice($classNameParts, 1)));
		}
		return $tableName;
	}

	/**
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
	 * @param string $methodType (get,set,add,remove,is)
	 * @return string method name
	 */
	static public function getMethodName(AbstractProperty $domainProperty, $methodType) {
		$propertyName = $domainProperty->getName();
		switch ($methodType) {
			case 'set'        :
				return 'set' . ucfirst($propertyName);

			case 'get'        :
				return 'get' . ucfirst($propertyName);

			case 'add'        :
				return 'add' . ucfirst(\EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName));

			case 'remove'    :
				return 'remove' . ucfirst(\EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName));

			case 'is'        :
				return 'is' . ucfirst($propertyName);
		}
	}

	/**
	 *
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $property
	 * @param string $methodType (set,add,remove)
	 * @return string method body
	 */
	static public function getParameterName(AbstractProperty $domainProperty, $methodType) {

		$propertyName = $domainProperty->getName();

		switch ($methodType) {

			case 'set'            :
				return $propertyName;

			case 'add'            :
				return \EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName);

			case 'remove'        :
				return \EBT\ExtensionBuilder\Utility\Inflector::singularize($propertyName) . 'ToRemove';
		}
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $domainProperty
	 * @param string $methodType
	 * @return string
	 */
	static public function getParamTag(AbstractProperty $domainProperty, $methodType) {

		switch ($methodType) {
			case 'set'        :
				return $domainProperty->getTypeForComment() . ' $' . $domainProperty->getName();

			case 'add'        :
				/** @var $domainProperty \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation */
				$paramTag = $domainProperty->getForeignClassName();
				$paramTag .= ' $' . self::getParameterName($domainProperty, 'add');
				return $paramTag;

			case 'remove'    :
				/** @var $domainProperty \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation */
				$paramTag = $domainProperty->getForeignClassName();
				$paramTag .= ' $' . self::getParameterName($domainProperty, 'remove');
				$paramTag .= ' The ' . $domainProperty->getForeignModelName() . ' to be removed';
				return $paramTag;
		}
	}

	/**
	 *
	 * Build record type from TX_Vendor_Package_Modelname
	 * @param $className
	 * @return string
	 */
	static public function convertClassNameToRecordType($className) {
		$classNameParts = explode('\\', $className);
		if (count($classNameParts) > 6) {
			return 'Tx_' . $classNameParts[3] . '_' . $classNameParts[6];
		} elseif (count($classNameParts) == 6) {
			return 'Tx_' . $classNameParts[2] . '_' . $classNameParts[5];
		} else {
			return $className;
		}
	}

}
