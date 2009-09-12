<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ingmar Schlecht <ingmar@typo3.org> 
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * Backend Module of the Extbase Kickstarter extension
 *
 * @category    Controller
 * @package     TYPO3
 * @subpackage  tx_mvcextjssamples
 * @author      Ingmar Schlecht <ingmar@typo3.org> 
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_ExtbaseKickstarter_Controller_KickstarterModuleController extends Tx_Extbase_MVC_Controller_ActionController {
	
	/**
	 * Holds reference to the template class
	 * 
	 * @var template
	 */
	protected $doc;
	
	/**
	 * Holds reference to t3lib_SCbase
	 * 
	 * @var t3lib_SCbase
	 */
	protected $scBase;


	protected $objectSchemaBuilder;

	protected $codeGenerator;

	public function initializeAction() {
		$this->objectSchemaBuilder = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_ObjectSchemaBuilder');
		$this->codeGenerator = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_CodeGenerator');
	}

	/**
	 * Index action for this controller.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		
			// Prepare scBase
		$this->scBase = t3lib_div::makeInstance('t3lib_SCbase'); 
		$this->scBase->MCONF['name'] = $this->request->getControllerName();
		$this->scBase->init();
		
			// Prepare template class
		$this->doc = t3lib_div::makeInstance('template'); 
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		
		$this->menuConfig();
		
			// Template page
		$this->view->assign('title', 'Backend Module!');  
		
		$this->view->assign('FUNC_MENU', t3lib_BEfunc::getFuncMenu(0, 'SET[function]', $this->scBase->MOD_SETTINGS['function'], $this->scBase->MOD_MENU['function']));
		
		$this->view->assign('CSH', t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']));
		$this->view->assign('SAVE', '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />');
		$this->view->assign('SHORTCUT',  $this->doc->makeShortcutIcon('', 'function', $settings['pluginName']));
		
		$this->view->assign('startPage', $this->doc->startPage('OldStyle Module'));
		$this->view->assign('endPage', $this->doc->endPage());
                            
	}

	public function generateCodeAction() {
		$jsonObject = json_decode(file_get_contents('php://input'),true);
		$extensionConfigurationFromJson = json_decode($jsonObject['params']['working'],true);
		$extensionSchema = $this->objectSchemaBuilder->build($extensionConfigurationFromJson);

		$extensionDirectory = PATH_typo3conf . 'ext/' . $extensionSchema->getExtensionKey();
		t3lib_div::mkdir($extensionDirectory);
		t3lib_div::mkdir_deep($extensionDirectory, 'Classes/Domain/Model');

		$domainModelDirectory = $extensionDirectory . '/Classes/Domain/Model/';
		foreach ($extensionSchema->getDomainObjects() as $lastPartOfClassName => $domainObject) {
			$fileContents = $this->codeGenerator->generateDomainObjectCode($domainObject);
			t3lib_div::writeFile($domainModelDirectory . $lastPartOfClassName . '.php', $fileContents);
		}
		$extensionConfigurationFromJson = $jsonObject['params']['working'];
		
		return json_encode(array('saved'));
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		$this->scBase->MOD_MENU = Array (
			'function' => Array (
				'1' => 'Menu 1',
				'2' => 'Menu 2',
				'3' => 'Menu 3',
			)
		);
		$this->scBase->menuConfig();
	}
	
}
?>