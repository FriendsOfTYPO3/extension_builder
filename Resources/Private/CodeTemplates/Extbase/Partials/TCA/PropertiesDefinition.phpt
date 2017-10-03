{namespace k=EBT\ExtensionBuilder\ViewHelpers}
{escaping off}
<f:for each="{domainObject.properties}" as="property">
    '{property.fieldName}' => [
        'exclude' => <f:if condition="{property.excludeField}"><f:then>true</f:then><f:else>false</f:else></f:if>,<f:if condition="{property.l10nModeExclude}">
        'l10n_mode' => 'exclude',</f:if>
        'label' => 'LLL:EXT:{domainObject.extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{property.labelNamespace}',
        'config' => <k:format.indent indentation="2"><f:render partial="TCA/{property.dataType}.phpt" arguments="{property: property,extension:domainObject.extension,settings:settings}" /></k:format.indent>
    ],</f:for>
