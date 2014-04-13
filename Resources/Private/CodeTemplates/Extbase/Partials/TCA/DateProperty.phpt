array(
	'type' => 'input',
	'size' => 7,
	'eval' => 'date<f:if condition="{property.required}">,required</f:if>',
	'checkbox' => 1,
	'default' => time()
),