[
    'type' => 'text',
    'enableRichtext' => true,
    'richtextConfiguration' => 'default',
    'fieldControl' => [
        'fullScreenRichtext' => [
            'disabled' => false,
        ],
    ],
    'cols' => 40,
    'rows' => 15,
    'eval' => 'trim<f:if condition="{property.required}">,required</f:if>',
],
