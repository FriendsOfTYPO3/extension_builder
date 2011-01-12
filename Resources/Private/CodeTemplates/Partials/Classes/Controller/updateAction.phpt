{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}

	/**
	 * Updates an existing {domainObject.name} and forwards to the list action afterwards.
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} the {domainObject.name} to display
	 */
	public function updateAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository->update(${domainObject.name -> k:lowercaseFirst()});
		$this->flashMessageContainer->add('Your {domainObject.name} was updated.');
		$this->redirect('list');
	}