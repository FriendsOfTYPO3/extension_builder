array(
	'type' => 'input',
	'size' => 6,
	'eval' => 'timesec<f:if condition="{property.required}">,required</f:if>',
	'checkbox' => 1,
	'default' => time()
)