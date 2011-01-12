{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	
	/**
	 * Updates an existing {domainObject.name} and forwards to the index action afterwards.
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} the {domainObject.name} to display
	 * @return string A form to edit a {domainObject.name} 
	 */
	public function editAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->view->assign('{domainObject.name -> k:lowercaseFirst()}', ${domainObject.name -> k:lowercaseFirst()});
	}