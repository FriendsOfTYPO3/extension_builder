{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	/**
	 * Creates a new {domainObject.name} and forwards to the list action.
	 *
	 * @param {domainObject.className} $new{domainObject.name} a fresh {domainObject.name} object which has not yet been added to the repository
	 * @return string An HTML form for creating a new {domainObject.name}
	 * @dontvalidate $new{domainObject.name}
	 */
	public function newAction({domainObject.className} $new{domainObject.name} = NULL) {
		$this->view->assign('new{domainObject.name}', $new{domainObject.name});
	}