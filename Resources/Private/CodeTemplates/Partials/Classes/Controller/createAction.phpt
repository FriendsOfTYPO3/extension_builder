{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	/**
	 * Creates a new {domainObject.name} and forwards to the list action.
	 *
	 * @param {domainObject.className} $new{domainObject.name} a fresh {domainObject.name} object which has not yet been added to the repository
	 */
	public function createAction({domainObject.className} $new{domainObject.name} = NULL) {
		if( $new{domainObject.name}){
			$this->{domainObject.name -> k:lowercaseFirst()}Repository->add($new{domainObject.name});
			$this->flashMessageContainer->add('Your new {domainObject.name} was created.');<k:comment>TODO check flash messages</k:comment>
			$this->redirect('list');
		}
		else {
			$this->view->assign('new{domainObject.name}', $new{domainObject.name});
		}
	}