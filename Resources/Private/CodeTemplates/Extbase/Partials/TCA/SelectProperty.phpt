[
    'type' => 'select',
    'renderType' => 'selectSingle',
    'items' => [
        <f:if condition="{property.hasSelectItems}"><f:then><f:for each="{property.selectItems}" as="item">['label' => '{item.label}', 'value' => '{item.value}'],
        </f:for></f:then><f:else>['label' => '-- Label --', 'value' => 0],
        </f:else></f:if>
    ],
    'size' => 1,
    'maxitems' => 1,<f:if condition="{property.required}">
    'required' => true,</f:if>
],