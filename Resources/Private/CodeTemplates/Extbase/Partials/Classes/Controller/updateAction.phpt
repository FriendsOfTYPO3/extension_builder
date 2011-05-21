{namespace k=Tx_ExtensionBuilder_ViewHelpers}

	/**
	 * Updates an existing {domainObject.name} and forwards to the list action afterwards.
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:format.lowercaseFirst()} the {domainObject.name} to display
	 */
	public function updateAction({domainObject.className} ${domainObject.name -> k:format.lowercaseFirst()}) {
		$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->update(${domainObject.name -> k:format.lowercaseFirst()});
		$this->flashMessageContainer->add('Your {domainObject.name} was updated.');
		$this->redirect('list');
	}