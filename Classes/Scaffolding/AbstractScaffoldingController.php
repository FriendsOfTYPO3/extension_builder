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
	 * @api
	 * @var string
	 */
	protected $className = "Blog";

	protected $fullClassName; // TODO

	public function initializeAction() {
		// TODO: Fill $this->className
		$this->lcfirst(Tx_ExtbaseKickstarter_Utility_Inflector::pluralize($this->className)); // TODO: lcfirst >= php 5.3
		
		
	}

	/**
	 * Index action for this controller. Displays a list of blogs.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		$this->view->assign('blogs', $this->blogRepository->findAll());
	}


	public function initializeNewAction() {
		$this->arguments->addNewArgument('new' . $this->className, $this->fullClassName, FALSE);
		// Argument IS NOT VALIDATED (but that is correct)
	}

	/**
	 * Displays a form for creating a new blog
	 *
	 * @return string An HTML form for creating a new blog
	 */
	public function newAction() {
		$this->view->assign('new' . $this->className, $this->arguments['new' . $this->className]);
	}

	/**
	 * Creates a new blog
	 *
	 * @param Tx_BlogExample_Domain_Model_Blog $newBlog A fresh Blog object which has not yet been added to the repository
	 * @return void
	 */
	public function createAction(Tx_BlogExample_Domain_Model_Blog $newBlog) {
		$this->blogRepository->add($newBlog);
		$this->flashMessages->add('Your new blog was created.');
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
