<?php
namespace FIXTURE\TestExtension\Controller;

/***
 * 
 * This file is part of the "ExtensionBuilder Test Extension" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017 John Doe <mail@typo3.com>, TYPO3
 *
 ***/

/**
 * MainController
 */
class MainController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * mainRepository
     *
     * @var \FIXTURE\TestExtension\Domain\Repository\MainRepository
     * @inject
     */
    protected $mainRepository = null;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $mains = $this->mainRepository->findAll();
        $this->view->assign('mains', $mains);
    }

    /**
     * action show
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     * @return void
     */
    public function showAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->view->assign('main', $main);
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
     * @param \FIXTURE\TestExtension\Domain\Model\Main $newMain
     * @return void
     */
    public function createAction(\FIXTURE\TestExtension\Domain\Model\Main $newMain)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->mainRepository->add($newMain);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     * @ignorevalidation $main
     * @return void
     */
    public function editAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->view->assign('main', $main);
    }

    /**
     * action update
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     * @return void
     */
    public function updateAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->mainRepository->update($main);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \FIXTURE\TestExtension\Domain\Model\Main $main
     * @return void
     */
    public function deleteAction(\FIXTURE\TestExtension\Domain\Model\Main $main)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->mainRepository->remove($main);
        $this->redirect('list');
    }
}