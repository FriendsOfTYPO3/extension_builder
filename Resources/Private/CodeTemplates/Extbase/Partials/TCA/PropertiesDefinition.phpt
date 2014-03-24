{namespace k=EBT\ExtensionBuilder\ViewHelpers}
<f:for each="{domainObject.properties}" as="property">
	'{property.fieldName}' => array(
		'exclude' => <f:if condition="{property.excludeField}"><f:then>1</f:then><f:else>0</f:else></f:if>,
		'label' => 'LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{property.labelNamespace}',
		'config' => array(
			<k:format.indent indentation="3"><f:render partial="TCA/{property.dataType}.phpt" arguments="{property: property,extension:domainObject.extension,settings:settings}" /></k:format.indent>
		),<f:if condition="{property.useRTE}">
		'defaultExtras' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',</f:if>
	),</f:for>