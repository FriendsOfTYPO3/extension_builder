'type' => 'input',
'size' => 12,
'max' => 20,
'eval' => 'datetime<f:if condition="{property.required}">,required</f:if>',
'checkbox' => 1,
'default' => time()