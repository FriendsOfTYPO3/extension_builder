{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	/**
	 * Displays single {domainObject.name}
	 *
	 * @return string The rendered view
	 */
	public function showAction() {
		${domainObject.name -> k:lowercaseFirst() -> k:pluralize()} = $this->{domainObject.name -> k:lowercaseFirst()}Repository->findAll();
		$this->view->assign('{domainObject.name -> k:lowercaseFirst() -> k:pluralize()}', ${domainObject.name -> k:lowercaseFirst() -> k:pluralize()});
	}