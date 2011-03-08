{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	/**
	 * Displays a form for creating a new  {domainObject.name}
	 *
	 * @param {domainObject.className} $new{domainObject.name} a fresh {domainObject.name} object which has not yet been added to the repository
	 * @return void
	 * @dontvalidate $new{domainObject.name}
	 */
	public function newAction({domainObject.className} $new{domainObject.name} = NULL) {
		$this->view->assign('new{domainObject.name}', $new{domainObject.name});
	}