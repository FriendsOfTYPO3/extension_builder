{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	
	/**
	 * Updates an existing {domainObject.name} and forwards to the index action afterwards.
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} the {domainObject.name} to display
	 * @param boolean $isEdited is set to true, if the action is called after the form has been displayed
	 * @return string A form to edit a {domainObject.name} 
	 */
	public function updateAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}, $isEdited = false) {
		if($isEdited){
			$this->{domainObject.name -> k:lowercaseFirst()}Repository->update(${domainObject.name -> k:lowercaseFirst()});
			$this->flashMessageContainer->add('Your {domainObject.name} was updated.');
			$this->redirect('list');
		}
		$this->view->assign('{domainObject.name -> k:lowercaseFirst()}', ${domainObject.name -> k:lowercaseFirst()});
	}