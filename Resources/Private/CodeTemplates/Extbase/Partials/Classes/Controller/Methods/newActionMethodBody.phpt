{namespace k=Tx_ExtensionBuilder_ViewHelpers}
<f:if condition="{domainObject.hasBooleanProperties}">if ($new{domainObject.name} == NULL) { // workaround for fluid bug ##5636
	$new{domainObject.name} = t3lib_div::makeInstance('{domainObject.className}');
}</f:if>
$this->view->assign('new{domainObject.name}', $new{domainObject.name});