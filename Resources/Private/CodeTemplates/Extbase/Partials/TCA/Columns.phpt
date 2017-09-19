{namespace k=EBT\ExtensionBuilder\ViewHelpers}{escaping off}
<f:if condition="{domainObject.properties}">
$tmp_{domainObject.extension.extensionKey}_columns = [<f:render partial="TCA/PropertiesDefinition.phpt" arguments="{domainObject:domainObject,settings:settings}"/>
];
<f:for each="{k:listForeignKeyRelations(extension: domainObject.extension, domainObject: domainObject)}" as="relation">
$tmp_{domainObject.extension.extensionKey}_columns['{relation.foreignKeyName}'] = [
    'config' => [
        'type' => 'passthrough',
    ]
];</f:for>

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('{domainObject.databaseTableName}',$tmp_{domainObject.extension.extensionKey}_columns);
</f:if>
<k:recordType domainObject="{domainObject}">
/* inherit and extend the show items from the parent class */

if (isset($GLOBALS['TCA']['{domainObject.databaseTableName}']['types']['{parentRecordType}']['showitem'])) {
    $GLOBALS['TCA']['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] = $GLOBALS['TCA']['{domainObject.databaseTableName}']['types']['{parentRecordType}']['showitem'];
} elseif(is_array($GLOBALS['TCA']['{domainObject.databaseTableName}']['types'])) {
    // use first entry in types array
    ${domainObject.databaseTableName}_type_definition = reset($GLOBALS['TCA']['{domainObject.databaseTableName}']['types']);
    $GLOBALS['TCA']['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] = ${domainObject.databaseTableName}_type_definition['showitem'];
} else {
    $GLOBALS['TCA']['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] = '';
}
$GLOBALS['TCA']['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] .= ',--div--;LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{domainObject.labelNamespace},';
$GLOBALS['TCA']['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] .= '<f:for each="{domainObject.properties}" as="property" iteration="i">{property.fieldName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>';
</k:recordType>
