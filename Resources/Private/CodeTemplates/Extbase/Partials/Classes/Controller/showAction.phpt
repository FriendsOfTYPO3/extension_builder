{namespace k=Tx_ExtensionBuilder_ViewHelpers}
	/**
	 * Displays a single {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:format.lowercaseFirst()} the {domainObject.name} to display
	 * @return string The rendered view
	 */
	public function showAction({domainObject.className} ${domainObject.name -> k:format.lowercaseFirst()}) {
		$this->view->assign('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});
	}