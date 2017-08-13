<?php
defined('TYPO3_MODE') || die();

<f:if condition="{addRecordTypeField}"><f:render partial="TCA/TypeField.phpt" arguments="{domainObject:rootDomainObject,databaseTableName:rootDomainObject.databaseTableName, extension:extension, settings:settings}" /></f:if>

<f:for each="{domainObjects}" as="domainObject"><f:if condition="{domainObject.mappedToExistingTable}">
<f:render partial="TCA/Columns.phpt" arguments="{domainObject:domainObject, settings:settings}" />
$GLOBALS['TCA']['{domainObject.databaseTableName}']['columns'][$GLOBALS['TCA']['{domainObject.databaseTableName}']['ctrl']['type']]['config']['items'][] = ['LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{domainObject.mapToTable}.tx_extbase_type.{domainObject.recordType}','{domainObject.recordType}'];
</f:if><f:if condition="{domainObject.categorizable}">
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
   '{extension.extensionKey}',
   '{domainObject.databaseTableName}'
);</f:if>
</f:for>
<f:if condition="{domainObject.mappedToExistingTable}">
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    '{domainObject.mapToTable}',
    'EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_csh_{domainObject.databaseTableName}.xlf'
);
</f:if>
<f:if condition="{domainObject.mappedToExistingTable}">//{domainObject.mapToTable}</f:if>
