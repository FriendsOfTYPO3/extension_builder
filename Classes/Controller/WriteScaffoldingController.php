<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Sebastian Kurfürst <sebastian@typo3.org>
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
 * @package     ExtbaseKickstarter
 * @subpackage
 * @author      Sebastian Kurfürst <sebastian@typo3.org>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_ExtbaseKickstarter_Controller_WriteScaffoldingController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_ExtbaseKickstarter_Service_CodeGenerator
	 */
	protected $codeGenerator;

	public function initializeAction() {
		$this->codeGenerator = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_CodeGenerator');
	}
	public function indexAction() {
		$controllersWithScaffoldingEnabled = $this->findControllersWithScaffoldingEnabled();
		$this->view->assign('extensions', $controllersWithScaffoldingEnabled);
	}

	protected function findControllersWithScaffoldingEnabled() {
		$extensions = array();

		$directoryIterator = new RecursiveDirectoryIterator(PATH_typo3conf . 'ext/');
		$recursiveIterator = new RecursiveIteratorIterator($directoryIterator);
		$controllerFilesIterator = new RegexIterator($recursiveIterator, '/^.+\Controller.php$/i', RecursiveRegexIterator::GET_MATCH);
		foreach ($controllerFilesIterator as $pathAndFileName => $tmp) {
			if ($this->isFileScaffoldingController($pathAndFileName)) {
				$pathRelativeToExtensionDirectory = substr($pathAndFileName, strlen(PATH_typo3conf . 'ext/'), -4);
				list($extensionKey, , , $controllerName) = explode('/', $pathRelativeToExtensionDirectory);
				$extensions[$extensionKey][] = $controllerName;
			}
		}
		return $extensions;
	}

	protected function isFileScaffoldingController($pathAndFileName) {
		$tokens = token_get_all(file_get_contents($pathAndFileName));
		foreach ($tokens as $singleToken) {
			if ($singleToken[0] == T_EXTENDS) {
				$nextToken = next($tokens);
				if ($nextToken[1] == 'Tx_ExtbaseKickstarter_Scaffolding_AbstractScaffoldingController') {
					return TRUE;
				} else {
					return FALSE;
				}
			}
		}
	}

	/**
	 *
	 * @param string $extensionKey
	 * @param string $controllerName
	 */
	public function generateFilesAction($extensionKey, $controllerName) {
		$objectSchemaBuilder = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_ObjectSchemaBuilder');

		$domainObjectName = substr($controllerName, 0, -10);

		$allowedActionNames = array('index', 'new', 'edit');
		$domainObject = $objectSchemaBuilder->buildDomainObjectByReflection(t3lib_div::underscoredToUpperCamelCase($extensionKey), $domainObjectName);

		t3lib_div::mkdir_deep(PATH_typo3conf . 'ext/' . $extensionKey . '/Resources/Private/', 'Templates/' . $domainObjectName);
		$templateDirectory = PATH_typo3conf . 'ext/' . $extensionKey . '/Resources/Private/Templates/' . $domainObjectName . '/';

		foreach ($allowedActionNames as $actionName) {
			$action = new Tx_ExtbaseKickstarter_Domain_Model_Action();
			$action->setName($actionName);
			$template = $this->codeGenerator->generateDomainTemplate($domainObject, $action);

			file_put_contents($templateDirectory . $actionName . '.html', $template);
		}

		$this->rewriteScaffoldingController($extensionKey, $controllerName, $domainObject);
	}

	protected function rewriteScaffoldingController($extensionKey, $controllerName, $domainObject) {
		$pathAndFileName = PATH_typo3conf . 'ext/' . $extensionKey . '/Classes/Controller/' . $controllerName . '.php';

		$tokens = token_get_all(file_get_contents($pathAndFileName));
		$output = '';
		reset($tokens);
		while($singleToken = current($tokens)) {
			if (is_array($singleToken)) {
				if ($singleToken[0] == T_EXTENDS) {
					$output .= $singleToken[1];
					$singleToken = next($tokens); // space
					$singleToken = next($tokens); // Tx_ExtbaseKickstarter_Scaffolding_AbstractScaffoldingController
					$output .= ' Tx_Extbase_MVC_Controller_ActionController'; // replace the controller name
				} else {
					$output .= $singleToken[1];
				}
			} else {
				$output .= $singleToken;
			}
			next($tokens);
		}

		$output = preg_replace('/##TOKEN FOR SCAFFOLDING(.*)##/s', $this->codeGenerator->generateActionControllerCrudActions($domainObject), $output);
		file_put_contents($pathAndFileName, $output);
	}
}
?>