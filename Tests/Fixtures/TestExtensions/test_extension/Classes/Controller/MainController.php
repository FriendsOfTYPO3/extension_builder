<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Controller;


/**
 * This file is part of the "Extension Builder Test Extension" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) ###YEAR### John Doe <mail@typo3.com>, TYPO3
 */

/**
 * MainController
 */
class MainController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * mainRepository
     *
     * @var \FIXTURE\TestExtension\Domain\Repository\MainRepository
     */
    protected $mainRepository;

    public function injectMainRepository(\FIXTURE\TestExtension\Domain\Repository\MainRepository $mainRepository)
    {
        $this->mainRepository = $mainRepository;
    }

    /**
     * action list
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $mains = $this->mainRepository->findAll();
        $this->view->assign('mains', $mains);
        return $this->htmlResponse();
    }

    /**
     * action show
     */
    public function showAction(\FIXTURE\TestExtension\Domain\Model\Main $main): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('main', $main);
        return $this->htmlResponse();
    }

    /**
     * action new
     */
    public function newAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     */
    public function createAction(\FIXTURE\TestExtension\Domain\Model\Main $newMain)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->mainRepository->add($newMain);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("main")
     */
    public function editAction(\FIXTURE\TestExtension\Domain\Model\Main $main): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('main', $main);
        return $this->htmlResponse();
    }

    /**
     * action update
     */
    public function updateAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->mainRepository->update($main);
        $this->redirect('list');
    }

    /**
     * action delete
     */
    public function deleteAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->mainRepository->remove($main);
        $this->redirect('list');
    }

    /**
     * action custom
     */
    public function customAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }
}
