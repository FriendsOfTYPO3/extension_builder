{namespace k=Tx_ExtensionBuilder_ViewHelpers}
	/**
	 * Deletes an existing {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:format.lowercaseFirst()} the {domainObject.name} to be deleted
	 * @return void
	 */
	public function deleteAction({domainObject.className} ${domainObject.name -> k:format.lowercaseFirst()}) {
		$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->remove(${domainObject.name -> k:format.lowercaseFirst()});
		$this->flashMessageContainer->add('Your {domainObject.name} was removed.');
		$this->redirect('list');
	}