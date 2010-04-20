{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	/**
	 * List action for this controller. Displays all {domainObject.name -> k:pluralize()}.
	 */
	public function indexAction() {
		${domainObject.name -> k:lowercaseFirst() -> k:pluralize()} = $this->{domainObject.name -> k:lowercaseFirst()}Repository->findAll();
		$this->view->assign('{domainObject.name -> k:lowercaseFirst() -> k:pluralize()}', ${domainObject.name -> k:lowercaseFirst() -> k:pluralize()});
	}

	/**
	 * Action that displays a single {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} The {domainObject.name} to display
	 */
	public function showAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->view->assign('{domainObject.name -> k:lowercaseFirst()}', ${domainObject.name -> k:lowercaseFirst()});
	}

	/**
	 * Displays a form for creating a new {domainObject.name}
	 *
	 * @param {domainObject.className} $new{domainObject.name} A fresh {domainObject.name} object taken as a basis for the rendering
	 * @dontvalidate $new{domainObject.name}
	 */
	public function newAction({domainObject.className} $new{domainObject.name} = NULL) {
		$this->view->assign('new{domainObject.name}', $new{domainObject.name});
	}

	/**
	 * Creates a new {domainObject.name} and forwards to the index action.
	 *
	 * @param {domainObject.className} $new{domainObject.name} A fresh {domainObject.name} object which has not yet been added to the repository
	 */
	public function createAction({domainObject.className} $new{domainObject.name}) {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository->add($new{domainObject.name});
		$this->flashMessageContainer->add('Your new {domainObject.name} was created.');<k:comment>TODO check flash messages</k:comment>
		$this->redirect('index');
	}

	/**
	 * Displays a form to edit an existing {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} The {domainObject.name} to display
	 * @dontvalidate ${domainObject.name -> k:lowercaseFirst()}
	 */
	public function editAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->view->assign('{domainObject.name -> k:lowercaseFirst()}', ${domainObject.name -> k:lowercaseFirst()});
	}

	/**
	 * Updates an existing {domainObject.name} and forwards to the index action afterwards.
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} The {domainObject.name} to display
	 */
	public function updateAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository->update(${domainObject.name -> k:lowercaseFirst()});
		$this->flashMessageContainer->add('Your {domainObject.name} was updated.');
		$this->redirect('index');
	}

	/**
	 * Deletes an existing {domainObject.name}
	 *
	 * @param {domainObject.className} ${domainObject.name -> k:lowercaseFirst()} The {domainObject.name} to be deleted
	 */
	public function deleteAction({domainObject.className} ${domainObject.name -> k:lowercaseFirst()}) {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository->remove(${domainObject.name -> k:lowercaseFirst()});
		$this->flashMessageContainer->add('Your {domainObject.name} was removed.');
		$this->redirect('index');
	}