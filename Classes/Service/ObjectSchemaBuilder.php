<?php
namespace EBT\ExtensionBuilder\Service;

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

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Action;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;
use EBT\ExtensionBuilder\Service\ValidationService;
use EBT\ExtensionBuilder\Utility\Tools;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 * Builder for domain objects
 */
class ObjectSchemaBuilder implements SingletonInterface
{
    /**
     * @var \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager
     */
    protected $configurationManager = null;
    /**
     * @var string[]
     */
    protected $relatedForeignTables = [];

    /**
     * @param \EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager
     * @return void
     */
    public function injectConfigurationManager(ExtensionBuilderConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     *
     * @param array $jsonDomainObject
     * @throws \Exception
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     */
    public function build(array $jsonDomainObject)
    {
        $domainObject = GeneralUtility::makeInstance(DomainObject::class);
        $domainObject->setUniqueIdentifier($jsonDomainObject['objectsettings']['uid']);

        $domainObject->setName($jsonDomainObject['name']);
        $domainObject->setDescription($jsonDomainObject['objectsettings']['description']);
        if ($jsonDomainObject['objectsettings']['type'] === 'Entity') {
            $domainObject->setEntity(true);
        } else {
            $domainObject->setEntity(false);
        }
        $domainObject->setAggregateRoot($jsonDomainObject['objectsettings']['aggregateRoot']);
        $domainObject->setSorting($jsonDomainObject['objectsettings']['sorting']);
        $domainObject->setAddDeletedField($jsonDomainObject['objectsettings']['addDeletedField']);
        $domainObject->setAddHiddenField($jsonDomainObject['objectsettings']['addHiddenField']);
        $domainObject->setAddStarttimeEndtimeFields($jsonDomainObject['objectsettings']['addStarttimeEndtimeFields']);
        $domainObject->setCategorizable($jsonDomainObject['objectsettings']['categorizable']);

        // extended settings
        if (!empty($jsonDomainObject['objectsettings']['mapToTable'])) {
            $domainObject->setMapToTable($jsonDomainObject['objectsettings']['mapToTable']);
        }
        if (!empty($jsonDomainObject['objectsettings']['parentClass'])) {
            $domainObject->setParentClass($jsonDomainObject['objectsettings']['parentClass']);
        }
        // properties
        if (isset($jsonDomainObject['propertyGroup']['properties'])) {
            foreach ($jsonDomainObject['propertyGroup']['properties'] as $propertyJsonConfiguration) {
                $propertyType = $propertyJsonConfiguration['propertyType'];
                if (in_array($propertyType, ['Image', 'File']) && !empty($propertyJsonConfiguration['maxItems']) && $propertyJsonConfiguration['maxItems'] > 1) {
                    $propertyJsonConfiguration['relationType'] = 'zeroToMany';
                    $propertyJsonConfiguration['relationName'] = $propertyJsonConfiguration['propertyName'];
                    $propertyJsonConfiguration['relationDescription'] = $propertyJsonConfiguration['propertyDescription'];
                    $propertyJsonConfiguration['foreignRelationClass'] = FileReference::class;
                    $propertyJsonConfiguration['type'] = $propertyJsonConfiguration['propertyType'];

                    $property = $this->buildRelation($propertyJsonConfiguration, $domainObject);
                } else {
                    $property = self::buildProperty($propertyJsonConfiguration);
                }
                $domainObject->addProperty($property);
            }
        }

        if (isset($jsonDomainObject['relationGroup']['relations'])) {
            foreach ($jsonDomainObject['relationGroup']['relations'] as $relationJsonConfiguration) {
                $relation = $this->buildRelation($relationJsonConfiguration, $domainObject);
                $domainObject->addProperty($relation);
            }
        }

        //actions
        if (isset($jsonDomainObject['actionGroup'])) {
            foreach ($jsonDomainObject['actionGroup'] as $jsonActionName => $actionValue) {
                if ($jsonActionName == 'customActions' && !empty($actionValue)) {
                    $actionNames = $actionValue;
                } elseif ($actionValue == 1) {
                    $jsonActionName = preg_replace('/^_default[0-9]_*/', '', $jsonActionName);
                    if ($jsonActionName == 'edit_update' || $jsonActionName == 'new_create') {
                        $actionNames = explode('_', $jsonActionName);
                    } else {
                        $actionNames = [$jsonActionName];
                    }
                } else {
                    $actionNames = [];
                }

                if (!empty($actionNames)) {
                    foreach ($actionNames as $actionName) {
                        $action = GeneralUtility::makeInstance(Action::class);
                        $action->setName($actionName);
                        $domainObject->addAction($action);
                    }
                }
            }
        }
        return $domainObject;
    }

    /**
     * @param array $relationJsonConfiguration
     * @param \EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject
     * @throws \Exception
     * @return \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation
     */
    public function buildRelation($relationJsonConfiguration, $domainObject)
    {
        $relationSchemaClassName = 'EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\Relation\\';
        $relationSchemaClassName .= ucfirst($relationJsonConfiguration['relationType']) . 'Relation';
        if (!class_exists($relationSchemaClassName)) {
            throw new \Exception(
                'Relation of type ' . $relationSchemaClassName . ' not found (configured in "' .
                $relationJsonConfiguration['relationName'] . '")'
            );
        }
        /**
         * @var $relation \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\AbstractRelation
         */
        $relation = new $relationSchemaClassName;
        $relation->setName($relationJsonConfiguration['relationName']);
        $relation->setLazyLoading((bool)$relationJsonConfiguration['lazyLoading']);
        $relation->setExcludeField($relationJsonConfiguration['propertyIsExcludeField']);
        $relation->setDescription($relationJsonConfiguration['relationDescription']);
        $relation->setUniqueIdentifier($relationJsonConfiguration['uid']);
        $relation->setType($relationJsonConfiguration['type']);

        if (!empty($relationJsonConfiguration['foreignRelationClass'])) {
            // relations without wires
            if (strpos($relationJsonConfiguration['foreignRelationClass'], '\\') > 0) {
                // add trailing slash if not set
                $relationJsonConfiguration['foreignRelationClass'] = '\\' . $relationJsonConfiguration['foreignRelationClass'];
            }
            $relation->setForeignClassName($relationJsonConfiguration['foreignRelationClass']);
            $relation->setRelatedToExternalModel(true);
            $extbaseClassConfiguration = $this->configurationManager->getExtbaseClassConfiguration(
                $relationJsonConfiguration['foreignRelationClass']
            );
            if (isset($extbaseClassConfiguration['tableName'])) {
                $foreignDatabaseTableName = $extbaseClassConfiguration['tableName'];
            } else {
                $foreignDatabaseTableName = Tools::parseTableNameFromClassName(
                    $relationJsonConfiguration['foreignRelationClass']
                );
            }
            $relation->setForeignDatabaseTableName($foreignDatabaseTableName);
            if ($relation instanceof ZeroToManyRelation) {
                $foreignKeyName = strtolower($domainObject->getName());
                if (ValidationService::isReservedMYSQLWord($foreignKeyName)) {
                    $foreignKeyName = 'tx_' . $foreignKeyName;
                }
                if (isset($this->relatedForeignTables[$foreignDatabaseTableName])) {
                    $foreignKeyName .= $this->relatedForeignTables[$foreignDatabaseTableName];
                    $this->relatedForeignTables[$foreignDatabaseTableName]++;
                } else {
                    $this->relatedForeignTables[$foreignDatabaseTableName] = 1;
                }
                $relation->setForeignKeyName($foreignKeyName);
                $relation->setForeignDatabaseTableName($foreignDatabaseTableName);
            }
            if ($relation->isFileReference() && !empty($relationJsonConfiguration['maxItems'])) {
                /** @var $relation \EBT\ExtensionBuilder\Domain\Model\DomainObject\FileProperty */
                $relation->setMaxItems($relationJsonConfiguration['maxItems']);
                if (!empty($relationJsonConfiguration['allowedFileTypes'])) {
                    $relation->setAllowedFileTypes($relationJsonConfiguration['allowedFileTypes']);
                }
            }
        }
        return $relation;
    }

    /**
     * @param $propertyJsonConfiguration
     * @return object
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public static function buildProperty($propertyJsonConfiguration)
    {
        $propertyType = $propertyJsonConfiguration['propertyType'];
        $propertyClassName = 'EBT\\ExtensionBuilder\\Domain\Model\\DomainObject\\' . $propertyType . 'Property';
        if (!class_exists($propertyClassName)) {
            throw new \Exception('Property of type ' . $propertyType . ' not found');
        }
        $property = GeneralUtility::makeInstance($propertyClassName);
        $property->setUniqueIdentifier($propertyJsonConfiguration['uid']);
        $property->setName($propertyJsonConfiguration['propertyName']);
        $property->setDescription($propertyJsonConfiguration['propertyDescription']);

        if ($propertyType == 'File' && !empty($propertyJsonConfiguration['allowedFileTypes'])) {
            $property->setAllowedFileTypes($propertyJsonConfiguration['allowedFileTypes']);
        }

        if (isset($propertyJsonConfiguration['propertyIsRequired'])) {
            $property->setRequired($propertyJsonConfiguration['propertyIsRequired']);
        }
        if (isset($propertyJsonConfiguration['propertyIsExcludeField'])) {
            $property->setExcludeField($propertyJsonConfiguration['propertyIsExcludeField']);
        }
        if ($property->isFileReference() && !empty($propertyJsonConfiguration['maxItems'])) {
            $property->setMaxItems($propertyJsonConfiguration['maxItems']);
        }
        return $property;
    }
}
