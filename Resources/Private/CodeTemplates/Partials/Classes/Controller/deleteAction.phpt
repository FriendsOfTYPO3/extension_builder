{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
			/**
	 * Deletes an existing {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} the {domainObject.name} to be deleted
	 * @return void
	 */
	public function deleteAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository->remove(${domainObject.name -> k:lowercaseFirst()});
		$this->flashMessageContainer->add('Your {domainObject.name} was removed.');
		$this->redirect('list');
	}