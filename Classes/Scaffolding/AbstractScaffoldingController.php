<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class Tx_ExtbaseKickstarter_Scaffolding_AbstractScaffoldingController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Extbase_Persistence_RepositoryInterface
	 */
	protected $repository;

	/**
	 * Name of the Domain Object this Controller should provide CRUD functionality for. Example: "Blog".
	 *
	 * If not set, this is divised from the name of the Controller
	 * (by using the last part of the controller class name without "Controller")
	 *
	 * @api
	 * @var string
	 */
	protected $domainObjectName = NULL;

	/**
	 * Full class name of the Domain Object this controller should provide CRUD functionality for.
	 * Example: "Tx_BlogExample_Domain_Model_Blog"
	 *
	 * If not set, this is divised from $domainObjectName and $extensionName.
	 * 
	 * @var string
	 */
	protected $domainObjectClassName = NULL;

	/**
	 *
	 * @var string
	 */
	protected $repositoryClassName = NULL;


	public function __construct() {
		parent::__construct();
		$this->setNameOfAssociatedDomainObject();
	}

	protected function setNameOfAssociatedDomainObject() {
		$controllerClassName = get_class($this);

		if ($this->domainObjectName === NULL) {
			$this->domainObjectName = substr(array_pop(explode('_', $controllerClassName)), 0, - strlen('Controller'));
		}

		if ($this->domainObjectClassName === NULL) {
			$this->domainObjectClassName = 'Tx_' . $this->extensionName . '_Domain_Model_' . $this->domainObjectName;
		}

		if ($this->repositoryClassName === NULL) {
			$this->repositoryClassName = 'Tx_' . $this->extensionName . '_Domain_Repository_' . $this->domainObjectName . 'Repository';
			// TODO: Exceptions if classes not found.
		}

		$this->repository = t3lib_div::makeInstance($this->repositoryClassName);
	}

	protected function resolveViewObjectName() {
		if (class_exists(parent::resolveViewObjectName())) {
			return parent::resolveViewObjectName();
		} else {
			return 'Tx_ExtbaseKickstarter_Scaffolding_ScaffoldingView';
		}
	}

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * @param Tx_Extbase_View_ViewInterface $view The view to be initialized
	 * @return void
	 * @api
	 */
	protected function initializeView(Tx_Extbase_MVC_View_ViewInterface $view) {
		if ($view instanceof Tx_ExtbaseKickstarter_Scaffolding_ScaffoldingView) {
			$view->setDomainObjectClassName($this->domainObjectClassName);
			$view->setDomainObjectName($this->domainObjectName);
		}
	}

	/**
	 * Index action for this controller. Displays a list of blogs.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		$lowercasedAndPluralizedDomainObjectName = Tx_ExtbaseKickstarter_Utility_Inflector::pluralize(lcfirst($this->domainObjectName)); // TODO: lcfirst >= php 5.3
		$this->view->assign($lowercasedAndPluralizedDomainObjectName, $this->repository->findAll());
	}


	public function initializeNewAction() {
		$this->arguments->addNewArgument('new' . $this->domainObjectName, $this->domainObjectClassName, FALSE);
	}

	/**
	 * Displays a form for creating a new domain object
	 *
	 * @return string An HTML form for creating a new domain object
	 */
	public function newAction() {
		$this->view->assign('new' . $this->domainObjectName, $this->arguments['new' . $this->domainObjectName]->getValue());
	}

	public function initializeCreateAction() {
		$argument = $this->arguments->addNewArgument('new' . $this->domainObjectName, $this->domainObjectClassName, TRUE);
		$argument->setValidator($this->validatorResolver->getBaseValidatorConjunction($this->domainObjectClassName));
	}
	/**
	 * Creates a new domain object
	 *
	 * @return void
	 */
	public function createAction() {
		$this->repository->add($this->arguments['new' . $this->domainObjectName]->getValue());
		$this->flashMessages->add('Your new ' . $this->domainObjectName . ' was created.');
		$this->redirect('index');
	}
	/**
	 * Edits an existing blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog to be edited. This might also be a clone of the original blog already containing modifications if the edit form has been submitted, contained errors and therefore ended up in this action again.
	 * @return string Form for editing the existing blog
	 * @dontvalidate $blog
	 */
	public function editAction(Tx_BlogExample_Domain_Model_Blog $blog) {
		$this->view->assign('blog', $blog);
		$this->view->assign('administrators', $this->administratorRepository->findAll());
	}

	/**
	 * Updates an existing blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog A not yet persisted clone of the original blog containing the modifications
	 * @return void
	 */
	public function updateAction(Tx_BlogExample_Domain_Model_Blog $blog) {
		$this->blogRepository->update($blog);
		$this->flashMessages->add('Your blog has been updated.');
		$this->redirect('index');
	}

	/**
	 * Deletes an existing blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $blog The blog to delete
	 * @return void
	 */
	public function deleteAction(Tx_BlogExample_Domain_Model_Blog $blog) {
		$this->blogRepository->remove($blog);
		$this->flashMessages->add('Your blog has been removed.');
		$this->redirect('index');
	}

}
?>
