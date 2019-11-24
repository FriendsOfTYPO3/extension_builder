<f:switch expression="{property.renderType}"><f:case value="selectSingle">[
    'type' => 'select',
    'renderType' => 'selectSingle',
    'foreign_table' => '{property.foreignDatabaseTableName}',
    'default' => 0,
    'minitems' => 0,
    'maxitems' => 1,
],</f:case><f:case value="selectMultipleSideBySide">[
    'type' => 'select',
    'renderType' => '{property.renderType}',
    'foreign_table' => '{property.foreignDatabaseTableName}',
    'default' => 0,
    'size' => 10,
    'autoSizeMax' => 30,
    'maxitems' => 1,
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
    ],
],</f:case><f:defaultCase>[
    'type' => 'inline',
    'foreign_table' => '{property.foreignDatabaseTableName}',
    'minitems' => 0,
    'maxitems' => 1,
    'appearance' => [
        'collapseAll' => 0,
        'levelLinksPosition' => 'top',
        'showSynchronizationLink' => 1,
        'showPossibleLocalizationRecords' => 1,
        'showAllLocalizationLink' => 1
    ],
],</f:defaultCase></f:switch>





