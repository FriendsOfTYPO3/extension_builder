[
    'type' => 'select',
    'renderType' => 'selectSingle',
    'items' => [
        ['label' => '-- Label --', 'value' => 0],
    ],
    'size' => 1,
    'maxitems' => 1,<f:if condition="{property.required}">
    'required' => true,</f:if>
],