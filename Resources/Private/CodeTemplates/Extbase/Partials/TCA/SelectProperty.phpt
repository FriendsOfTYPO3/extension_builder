{namespace k=EBT\ExtensionBuilder\ViewHelpers}[
    'type' => 'select',
    'renderType' => 'selectSingle',
    'items' => [
        <f:if condition="{property.selectItems}"><f:then><f:for each="{property.selectItems}" as="item">['label' => '{item.label -> k:format.quoteString()}', 'value' => '{item.value -> k:format.quoteString()}'],
        </f:for></f:then><f:else>['label' => '-- Label --', 'value' => 0],
        </f:else></f:if>
    ],
    'size' => 1,
    'maxitems' => 1,<f:if condition="{property.required}">
    'required' => true,</f:if>
],