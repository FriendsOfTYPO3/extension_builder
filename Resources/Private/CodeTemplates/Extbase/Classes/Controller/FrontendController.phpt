<?php

declare(strict_types=1);

namespace VENDOR\Package\Controller;

/**
 * MyController
 */
class MyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var ModuleTemplate $moduleTemplate
     */
    protected ModuleTemplate $moduleTemplate;

    /**
     * @var ModuleTemplateFactory $moduleTemplateFactory
     */
    protected ModuleTemplateFactory $moduleTemplateFactory;

    /**
     * @var \VENDOR\Package\Domain\Repository\DomainObjectRepository
     */
    protected $domainObjectRepository;

    public function __construct() {

    }

    /**
     * @param \VENDOR\Package\Domain\Repository\DomainObjectRepository
     */
    public function injectDomainObjectRepository(VENDOR\Package\Domain\Repository\DomainObjectRepository $domainObjectRepository): void
    {
        $this->domainObjectRepository = $domainObjectRepository;
    }

    /**
     * @return void
     */
    protected function initializeAction()
    {

    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $domainObjects = $this->domainObjectRepository->findAll();
        $this->view->assign('domainObjects', $domainObjects);

        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('domainObject', $domainObject);

        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function newAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $newDomainObject
     */
    public function createAction(\VENDOR\Package\Domain\Model\DomainObject $newDomainObject): \Psr\Http\Message\ResponseInterface
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->domainObjectRepository->add($newDomainObject);
        return $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('domainObject', $domainObject);

        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     */
    public function updateAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): \Psr\Http\Message\ResponseInterface
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->domainObjectRepository->update($domainObject);
        return $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \VENDOR\Package\Domain\Model\DomainObject $domainObject
     */
    public function deleteAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): \Psr\Http\Message\ResponseInterface
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->domainObjectRepository->remove($domainObject);
        return $this->redirect('list');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function genericAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }
}
