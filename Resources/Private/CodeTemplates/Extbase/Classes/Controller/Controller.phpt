<?php

declare(strict_types=1);

namespace VENDOR\Package\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use VENDOR\Package\Domain\Repository\DomainObjectRepository;
use Psr\Http\Message\ResponseInterface;

/**
 * MyController
 */
class MyController extends ActionController
{
    public function __construct(
        private readonly DomainObjectRepository $domainObjectRepository,
    ) {}

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $domainObjects = $this->domainObjectRepository->findAll();
        $this->view->assign('domainObjects', $domainObjects);
        return $this->htmlResponse();
    }

    /**
     * action show
     */
    public function showAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): ResponseInterface
    {
        $this->view->assign('domainObject', $domainObject);
        return $this->htmlResponse();
    }

    /**
     * action new
     */
    public function newAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     */
    public function createAction(\VENDOR\Package\Domain\Model\DomainObject $newDomainObject): ResponseInterface
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/main/en-us/User/Index.html', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING);
        $this->domainObjectRepository->add($newDomainObject);
        return $this->redirect('list');
    }

    /**
     * action edit
     */
    public function editAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): ResponseInterface
    {
        $this->view->assign('domainObject', $domainObject);
        return $this->htmlResponse();
    }

    /**
     * action update
     */
    public function updateAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): ResponseInterface
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/main/en-us/User/Index.html', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING);
        $this->domainObjectRepository->update($domainObject);
        return $this->redirect('list');
    }

    /**
     * action delete
     */
    public function deleteAction(\VENDOR\Package\Domain\Model\DomainObject $domainObject): ResponseInterface
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/main/en-us/User/Index.html', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING);
        $this->domainObjectRepository->remove($domainObject);
        return $this->redirect('list');
    }

    /**
     */
    public function genericAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

}
