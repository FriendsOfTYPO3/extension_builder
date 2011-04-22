{namespace k=Tx_ExtensionBuilder_ViewHelpers}

	/**
	 * Displays a form for editing an existing {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} the {domainObject.name} to display
	 * @return string A form to edit a {domainObject.name}
	 */
	public function editAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->view->assign('{domainObject.name -> k:lowercaseFirst()}', ${domainObject.name -> k:lowercaseFirst()});
	}