<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use FIXTURE\TestExtension\Domain\Repository\MainRepository;
use Psr\Http\Message\ResponseInterface;
use FIXTURE\TestExtension\Domain\Model\Main;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
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
class MainController extends ActionController
{

    /**
     * mainRepository
     *
     * @var MainRepository
     */
    protected $mainRepository;

    public function injectMainRepository(MainRepository $mainRepository)
    {
        $this->mainRepository = $mainRepository;
    }

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $mains = $this->mainRepository->findAll();
        $this->view->assign('mains', $mains);
        return $this->htmlResponse();
    }

    /**
     * action show
     */
    public function showAction(Main $main): ResponseInterface
    {
        $this->view->assign('main', $main);
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
    public function createAction(Main $newMain)
    {
        $message = 'The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html';
        $this->addFlashMessage($message, '', ContextualFeedbackSeverity::WARNING);
        $this->mainRepository->add($newMain);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @IgnoreValidation("main")
     */
    public function editAction(Main $main): ResponseInterface
    {
        $this->view->assign('main', $main);
        return $this->htmlResponse();
    }

    /**
     * action update
     */
    public function updateAction(Main $main)
    {
        $message = 'The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html';
        $this->addFlashMessage($message, '', ContextualFeedbackSeverity::WARNING);
        $this->mainRepository->update($main);
        $this->redirect('list');
    }

    /**
     * action delete
     */
    public function deleteAction(Main $main)
    {
        $message = 'The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html';
        $this->addFlashMessage($message, '', ContextualFeedbackSeverity::WARNING);
        $this->mainRepository->remove($main);
        $this->redirect('list');
    }

    /**
     * action custom
     */
    public function customAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
}
