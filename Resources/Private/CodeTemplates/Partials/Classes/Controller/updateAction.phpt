{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	/**
	 * Displays a form to edit an existing {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} the {domainObject.name} to display
	 * @dontvalidate ${domainObject.name -> k:lowercaseFirst()}
	 */
	public function editAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->view->assign('{domainObject.name -> k:lowercaseFirst()}', ${domainObject.name -> k:lowercaseFirst()});
	}

	/**
	 * Updates an existing {domainObject.name} and forwards to the index action afterwards.
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} the {domainObject.name} to display
	 * @param boolean $isEdited is set to true, if the action is called after the form has been displayed
	 */
	public function updateAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}, $isEdited = false) {
		if($isEdited){
			$this->{domainObject.name -> k:lowercaseFirst()}Repository->update(${domainObject.name -> k:lowercaseFirst()});
			$this->flashMessageContainer->add('Your {domainObject.name} was updated.');
			$this->redirect('list');
		}
		$this->view->assign('{domainObject.name -> k:lowercaseFirst()}', ${domainObject.name -> k:lowercaseFirst()});
	}