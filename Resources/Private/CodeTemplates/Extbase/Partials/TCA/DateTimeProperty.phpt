array(
	'type' => 'input',
	'size' => 10,
	'eval' => 'datetime<f:if condition="{property.required}">,required</f:if>',
	'checkbox' => 1,
	'default' => time()
),