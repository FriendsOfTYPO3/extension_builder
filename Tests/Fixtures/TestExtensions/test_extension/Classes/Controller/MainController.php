<?php

declare(strict_types=1);

namespace FIXTURE\TestExtension\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use FIXTURE\TestExtension\Domain\Repository\MainRepository;
use Psr\Http\Message\ResponseInterface;

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
     * @param DomainObjectRepository $domainObjectRepository
     */
    public function __construct(\FIXTURE\TestExtension\Domain\Repository\MainRepository $mainRepository)
    {
    }

    /**
     * action list
     */
    public function listAction()
    {
        $mains = $this->mainRepository->findAll();
        $this->view->assign('mains', $mains);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     */
    public function showAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->view->assign('main', $main);
        return $this->htmlResponse();
    }

    /**
     * action new
     */
    public function newAction()
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $newMain
     */
    public function createAction(\FIXTURE\TestExtension\Domain\Model\Main $newMain)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/main/en-us/User/Index.html', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING);
        $this->mainRepository->add($newMain);
        return $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("main")
     */
    public function editAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->view->assign('main', $main);
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     */
    public function updateAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/main/en-us/User/Index.html', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING);
        $this->mainRepository->update($main);
        return $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     */
    public function deleteAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/main/en-us/User/Index.html', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::WARNING);
        $this->mainRepository->remove($main);
        return $this->redirect('list');
    }

    /**
     * action custom
     */
    public function customAction()
    {
        return $this->htmlResponse();
    }
}
