'type' => 'inline',
'loadingStrategy' => 'proxy', // TODO: was "storage"
'foreign_class' => '{property.foreignClass.className}',
'foreign_table' => '{property.foreignClass.databaseTableName}',
'foreign_field' => '{property.foreignKeyName}',
'maxitems'      => 999999, // TODO This is only necessary because of a bug in tcemain
'appearance' => array(
	'newRecordLinkPosition' => 'bottom',
	'collapseAll' => 1,
	'expandSingle' => 1,
),