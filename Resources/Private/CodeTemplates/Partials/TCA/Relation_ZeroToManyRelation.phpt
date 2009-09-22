'type' => 'inline',
'loadingStrategy' => 'proxy', // TODO: was "storage"
'foreign_class' => '{property.foreignClass.className}',
'foreign_table' => '{property.foreignClass.databaseTableName}',
//     TODO Re-enable the foreign key references by uncommenting the following lines
'foreign_field' => 'blog', // TODO: FILL!!! This is still missing!
'maxitems'      => 999999, // TODO This is only necessary because of a bug in tcemain
'appearance' => array(
 'newRecordLinkPosition' => 'bottom',
 'collapseAll' => 1,
 'expandSingle' => 1,
),