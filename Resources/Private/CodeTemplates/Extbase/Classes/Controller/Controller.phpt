<?php
namespace VENDOR\Package\Controller;

/**
 * MyController
 */
class MyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController{

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Repository
	 * @inject
	 */
	protected $domainObjectRepository = NULL;

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$domainObjects = $this->domainObjectRepository->findAll();
		$this->view->assign('domainObjects', $domainObjects);
	}


	/**
	 * action show
	 *
	 * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
	 * @return void
	 */
	public function showAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject) {
		$this->view->assign('domainObject', $domainObject);
	}

	/**
	 * action new
	 *
	 * @param \VENDOR\Package\Domain\Model\DomainObject $newDomainObject
	 * @ignorevalidation $newDomainObject
	 * @return void
	 */
	public function newAction(\VENDOR\Package\Domain\Model\DomainObject $newDomainObject = NULL) {
		$this->view->assign('newDomainObject', $newDomainObject);
	}

	/**
	 * action create
	 *
	 * @param \VENDOR\Package\Domain\Model\DomainObject $newDomainObject
	 * @return void
	 */
	public function createAction(\VENDOR\Package\Domain\Model\DomainObject $newDomainObject) {
		$this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->domainObjectRepository->add($newDomainObject);
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
	 * @return void
	 */
	public function editAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject) {
		$this->view->assign('domainObject', $domainObject);
	}

	/**
	 * action update
	 *
	 * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
	 * @return void
	 */
	public function updateAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject) {
		$this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->domainObjectRepository->update($domainObject);
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
	 * @return void
	 */
	public function deleteAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject) {
		$this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->domainObjectRepository->remove($domainObject);
		$this->redirect('list');
	}

	/**
	 * @return void
	 */
	public function genericAction() {

	}

}
