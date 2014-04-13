<f:if condition="{property.type}">
	<f:then><f:comment>For files or images</f:comment>
<f:render partial="TCA/{property.type}Property.phpt" arguments="{property: property,extension:domainObject.extension,settings:settings}" />
	</f:then><f:else>array(
	'type' => 'inline',
	'foreign_table' => '{property.foreignDatabaseTableName}',
	'foreign_field' => '{property.foreignKeyName}',
	'maxitems'      => 9999,
	'appearance' => array(
		'collapseAll' => 0,
		'levelLinksPosition' => 'top',
		'showSynchronizationLink' => 1,
		'showPossibleLocalizationRecords' => 1,<f:if condition="{property.foreignModel.sorting}">
		'useSortable' => 1,</f:if>
		'showAllLocalizationLink' => 1
	),
),
	</f:else>
</f:if>
