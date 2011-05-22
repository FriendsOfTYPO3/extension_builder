{namespace k=Tx_ExtensionBuilder_ViewHelpers}
	/**
	 * Displays a form for creating a new  {domainObject.name}
	 *
	 * @param {domainObject.className} $new{domainObject.name} a fresh {domainObject.name} object which has not yet been added to the repository
	 * @return void
	 * @dontvalidate $new{domainObject.name}
	 */
	public function newAction({domainObject.className} $new{domainObject.name} = NULL) {
		<f:if condition="{domainObject.hasBooleanProperties}">if ($new{domainObject.name} == NULL) { // workaround for fluid bug ##5636
			$new{domainObject.name} = t3lib_div::makeInstance('{domainObject.className}');
		}</f:if>
		$this->view->assign('new{domainObject.name}', $new{domainObject.name});
	}