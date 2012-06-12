{namespace k=Tx_ExtensionBuilder_ViewHelpers}
$tmp_{domainObject.extension.extensionKey}_columns = array(
<f:for each="{domainObject.properties}" as="property">
	'{property.fieldName}' => array(
		'exclude' => <f:if condition="{property.excludeField}"><f:then>1</f:then><f:else>0</f:else></f:if>,
		'label' => 'LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.{locallangFileFormat}:{property.labelNamespace}',
		'config' => array(
			<k:format.indent indentation="3"><k:render partial="TCA/{property.dataType}.phpt" arguments="{property: property,extension:domainObject.extension,settings:settings}" /></k:format.indent>
		),<f:if condition="{property.useRTE}">
		'defaultExtras' => 'richtext[]',</f:if>
	),</f:for>
);
<f:for each="{k:listForeignKeyRelations(extension: domainObject.extension, domainObject: domainObject)}" as="relation">
$tmp_{domainObject.extension.extensionKey}_columns['{relation.foreignKeyName}'] = array(
	'config' => array(
		'type' => 'passthrough',
	)
);</f:for>

t3lib_extMgm::addTCAcolumns('{domainObject.databaseTableName}',$tmp_{domainObject.extension.extensionKey}_columns);
<f:if condition="{domainObject.mapToTable}">
$TCA['{domainObject.databaseTableName}']['columns'][$TCA['{domainObject.databaseTableName}']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.{locallangFileFormat}:{domainObject.mapToTable}.tx_extbase_type.{domainObject.recordType}','{domainObject.recordType}');
</f:if>
<k:recordType domainObject="{domainObject}">
$TCA['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] = $TCA['{domainObject.databaseTableName}']['types']['{parentRecordType}']['showitem'];
$TCA['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] .= ',--div--;LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.{locallangFileFormat}:{domainObject.labelNamespace},';
$TCA['{domainObject.databaseTableName}']['types']['{domainObject.recordType}']['showitem'] .= '<f:for each="{domainObject.properties}" as="property" iteration="i">{property.fieldName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>';
</k:recordType>