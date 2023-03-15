<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Utility;

use EBT\ExtensionBuilder\Domain\Model\DomainObject\AbstractProperty;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation;
use TYPO3\CMS\Core\SingletonInterface;

class Tools implements SingletonInterface
{
    public static function parseTableNameFromClassName($className): string
    {
        if (str_contains((string) $className, '\\')) {
            if (str_starts_with((string) $className, '\\')) {
                // remove trailing slash
                $className = substr((string) $className, 1);
            }
            $classNameParts = explode('\\', (string) $className, 6);
        } else {
            $classNameParts = explode('_', (string) $className, 6);
        }
        // could be: TYPO3\CMS\Extbase\Domain\Model\FrontendUser
        // or: VENDOR\Extension\Domain\Model\Foo
        if (count($classNameParts) > 5) {
            return strtolower('tx_' . implode('_', array_slice($classNameParts, 2)));
        }

        return strtolower('tx_' . implode('_', array_slice($classNameParts, 1)));
    }

    /**
     * @param string $methodType (set,add,remove)
     * @return string method body
     */
    public static function getParameterName(AbstractProperty $domainProperty, string $methodType): ?string
    {
        $propertyName = $domainProperty->getName();
        return match ($methodType) {
            'set' => $propertyName,
            'add' => Inflector::singularize($propertyName),
            'remove' => Inflector::singularize($propertyName) . 'ToRemove',
            default => null,
        };
    }

    public static function getParamTag(AbstractProperty $domainProperty, string $methodType): ?string
    {
        switch ($methodType) {
            case 'set':
                return $domainProperty->getTypeForComment() . ' $' . $domainProperty->getName();

            case 'add':
                /** @var AbstractRelation $domainProperty */
                $paramTag = $domainProperty->getForeignClassName();
                return $paramTag . (' $' . self::getParameterName($domainProperty, 'add'));

            case 'remove':
                /** @var AbstractRelation $domainProperty */
                $paramTag = $domainProperty->getForeignClassName();
                $paramTag .= ' $' . self::getParameterName($domainProperty, 'remove');
                return $paramTag . (' The ' . $domainProperty->getForeignModelName() . ' to be removed');
        }
        return null;
    }

    /**
     * Build record type from TX_Vendor_Package_Modelname
     */
    public static function convertClassNameToRecordType(string $className): string
    {
        $classNameParts = explode('\\', $className);
        if (count($classNameParts) > 6) {
            return 'Tx_' . $classNameParts[3] . '_' . $classNameParts[6];
        }

        if (count($classNameParts) === 6) {
            return 'Tx_' . $classNameParts[2] . '_' . $classNameParts[5];
        }

        return $className;
    }
}
