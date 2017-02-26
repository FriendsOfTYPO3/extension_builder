<?php
namespace VENDOR\Package\Controller;

/**
 * MyController
 */
class MyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Repository
     * @inject
     */
    protected $domainObjectRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $domainObjects = $this->domainObjectRepository->findAll();
        $this->view->assign('domainObjects', $domainObjects);
    }

    /**
     * action show
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     * @return void
     */
    public function showAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject)
    {
        $this->view->assign('domainObject', $domainObject);
    }

    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {
    }

    /**
     * action create
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $newDomainObject
     * @return void
     */
    public function createAction(\VENDOR\Package\Domain\Model\DomainObject $newDomainObject)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->domainObjectRepository->add($newDomainObject);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     * @return void
     */
    public function editAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject)
    {
        $this->view->assign('domainObject', $domainObject);
    }

    /**
     * action update
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     * @return void
     */
    public function updateAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->domainObjectRepository->update($domainObject);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     * @return void
     */
    public function deleteAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->domainObjectRepository->remove($domainObject);
        $this->redirect('list');
    }

    /**
     * @return void
     */
    public function genericAction()
    {
    }

}
