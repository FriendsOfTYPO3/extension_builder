{namespace k=EBT\ExtensionBuilder\ViewHelpers}
<f:for each="{domainObject.properties}" as="property">
    '{property.fieldName}' => [
        'exclude' => <f:if condition="{property.excludeField}"><f:then>1</f:then><f:else>0</f:else></f:if>,
        'label' => 'LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{property.labelNamespace}',
        'config' => <k:format.indent indentation="2"><f:render partial="TCA/{property.dataType}.phpt" arguments="{property: property,extension:domainObject.extension,settings:settings}" /></k:format.indent>
        <f:if condition="{property.useRTE}">'defaultExtras' => 'richtext:rte_transform[mode=ts_css]'</f:if>
    ],</f:for>