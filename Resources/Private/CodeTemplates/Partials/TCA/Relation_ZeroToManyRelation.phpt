<f:if condition="{property.inlineEditing}">
<f:then>'type' => 'inline',
'foreign_table' => '{property.foreignClass.databaseTableName}',
'foreign_field' => '{property.foreignKeyName}',
'maxitems'      => 9999,
'appearance' => array(
	'collapse' => 0,
	'newRecordLinkPosition' => 'bottom',
),</f:then><f:else>'type' => 'select',
'foreign_table' => '{property.foreignClass.databaseTableName}',
'foreign_field' => '{property.foreignKeyName}',
'size' => 10,
'autoSizeMax' => 30,
'maxitems' => 9999,
'wizards' => array(
	'_PADDING' => 1,
	'_VERTICAL' => 1,
	'edit' => array(
		'type' => 'popup',
		'title' => 'Edit',
		'script' => 'wizard_edit.php',
		'icon' => 'edit2.gif',
		'popup_onlyOpenIfSelected' => 1,
		'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
		),
	'add' => Array(
		'type' => 'script',
		'title' => 'Create new',
		'icon' => 'add.gif',
		'params' => array(
			'table'=>'{property.foreignClass.databaseTableName}',
			'pid' => '###CURRENT_PID###',
			'setValue' => 'prepend'
			),
		'script' => 'wizard_add.php',
	),
),</f:else>
</f:if>