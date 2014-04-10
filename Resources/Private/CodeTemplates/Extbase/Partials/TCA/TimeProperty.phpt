array(
	'type' => 'input',
	'size' => 4,
	'eval' => 'time<f:if condition="{property.required}">,required</f:if>',
	'checkbox' => 1,
	'default' => time()
)