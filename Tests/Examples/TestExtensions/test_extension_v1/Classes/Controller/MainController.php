<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) ###YEAR### John Doe <mail@typo3.com>, TYPO3
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 *
 *
 * @package test_extension
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_TestExtension_Controller_MainController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * mainRepository
	 *
	 * @var Tx_TestExtension_Domain_Repository_MainRepository
	 */
	protected $mainRepository;

	/**
	 * injectMainRepository
	 *
	 * @param Tx_TestExtension_Domain_Repository_MainRepository $mainRepository
	 * @return void
	 */
	public function injectMainRepository(Tx_TestExtension_Domain_Repository_MainRepository $mainRepository) {
		$this->mainRepository = $mainRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$mains = $this->mainRepository->findAll();
		$this->view->assign('mains', $mains);
	}

	/**
	 * action show
	 *
	 * @param Tx_TestExtension_Domain_Model_Main $main
	 * @return void
	 */
	public function showAction(Tx_TestExtension_Domain_Model_Main $main) {
		$this->view->assign('main', $main);
	}

	/**
	 * action new
	 *
	 * @param Tx_TestExtension_Domain_Model_Main $newMain
	 * @dontvalidate $newMain
	 * @return void
	 */
	public function newAction(Tx_TestExtension_Domain_Model_Main $newMain = NULL) {
		$this->view->assign('newMain', $newMain);
	}

	/**
	 * action create
	 *
	 * @param Tx_TestExtension_Domain_Model_Main $newMain
	 * @return void
	 */
	public function createAction(Tx_TestExtension_Domain_Model_Main $newMain) {
		$this->mainRepository->add($newMain);
		$this->flashMessageContainer->add('Your new Main was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param Tx_TestExtension_Domain_Model_Main $main
	 * @return void
	 */
	public function editAction(Tx_TestExtension_Domain_Model_Main $main) {
		$this->view->assign('main', $main);
	}

	/**
	 * action update
	 *
	 * @param Tx_TestExtension_Domain_Model_Main $main
	 * @return void
	 */
	public function updateAction(Tx_TestExtension_Domain_Model_Main $main) {
		$this->mainRepository->update($main);
		$this->flashMessageContainer->add('Your Main was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param Tx_TestExtension_Domain_Model_Main $main
	 * @return void
	 */
	public function deleteAction(Tx_TestExtension_Domain_Model_Main $main) {
		$this->mainRepository->remove($main);
		$this->flashMessageContainer->add('Your Main was removed.');
		$this->redirect('list');
	}

}
?>