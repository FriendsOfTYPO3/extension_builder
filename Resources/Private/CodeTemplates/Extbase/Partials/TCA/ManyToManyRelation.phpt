[
    'type' => 'select',
    'renderType' => '{property.renderType}',
    'foreign_table' => '{property.foreignDatabaseTableName}',
    'MM' => '{property.relationTableName}',<f:if condition="{property.renderType} == 'selectMultipleSideBySide'">
    'size' => 10,
    'autoSizeMax' => 30,
    'maxitems' => 9999,
    'multiple' => 0,
    'fieldControl' => [
        'editPopup' => [
            'disabled' => false,
        ],
        'addRecord' => [
            'disabled' => false,
        ],
        'listModule' => [
            'disabled' => true,
        ],
    ],</f:if>
],
