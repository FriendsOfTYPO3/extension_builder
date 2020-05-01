<f:if condition="{property.type}">
    <f:then><f:comment>For files or images</f:comment>
<f:render partial="TCA/{property.type}Property.phpt" arguments="{property: property,extension:domainObject.extension,settings:settings}" />
    </f:then><f:else><f:switch expression="{property.renderType}"><f:case value="selectMultipleSideBySide">[
    'type' => 'select',
    'renderType' => '{property.renderType}',
    'foreign_table' => '{property.foreignDatabaseTableName}',
    'default' => 0,
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
    ],
],</f:case><f:defaultCase>[
    'type' => 'inline',
    'foreign_table' => '{property.foreignDatabaseTableName}',
    'foreign_field' => '{property.foreignKeyName}',<f:if condition="{property.foreignModel.sorting}">
    'foreign_sortby' => 'sorting',</f:if>
    'maxitems' => 9999,
    'appearance' => [
        'collapseAll' => 0,
        'levelLinksPosition' => 'top',
        'showSynchronizationLink' => 1,
        'showPossibleLocalizationRecords' => 1,<f:if condition="{property.foreignModel.sorting}">
        'useSortable' => 1,</f:if>
        'showAllLocalizationLink' => 1
    ],
],</f:defaultCase></f:switch>
    </f:else>
</f:if>
