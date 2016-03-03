<?php
namespace EBT\ExtensionBuilder\Utility;

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

use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;

/**
 * provides helper methods
 *
 */
class Tools implements \TYPO3\CMS\Core\SingletonInterface
{
    public static function parseTableNameFromClassName($className)
    {
        if (strpos($className, '\\') !== false) {
            if (strpos($className, '\\') === 0) {
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
            $tableName = strtolower('tx_' . implode('_', array_slice($classNameParts, 2)));
        } else {
            $tableName = strtolower('tx_' . implode('_', array_slice($classNameParts, 1)));
        }
        return $tableName;
    }

    /**
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty $property
     * @param string $methodType (set,add,remove)
     * @return string method body
     */
    public static function getParameterName(AbstractProperty $domainProperty, $methodType)
    {
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
    public static function getParamTag(AbstractProperty $domainProperty, $methodType)
    {
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
    public static function convertClassNameToRecordType($className)
    {
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
