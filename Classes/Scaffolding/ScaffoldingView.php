<?php
/***************************************************************
*  Copyright notice
*
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

class Tx_ExtbaseKickstarter_Scaffolding_ScaffoldingView extends Tx_Fluid_View_TemplateView {

	/**
	 * @var string
	 */
	protected $domainObjectName;

	/**
	 * @var Tx_ExtbaseKickstarter_ObjectSchemaBuilder
	 */
	protected $objectSchemaBuilder;
	
	/**
	 * @var Tx_ExtbaseKickstarter_Service_CodeGenerator
	 */
	protected $codeGenerator;

	public function initializeAction() {
		if (Tx_ExtbaseKickstarter_Utility_Compatibility::compareFluidVersion('1.3.0', '<')) {
			$this->objectSchemaBuilder = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_ObjectSchemaBuilder');
			$this->codeGenerator = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_CodeGenerator');
		}
	}

	/**
	 * @param Tx_ExtbaseKickstarter_ObjectSchemaBuilder $objectSchemaBuilder
	 * @return void
	 */
	public function injectObjectSchemaBuilder(Tx_ExtbaseKickstarter_ObjectSchemaBuilder $objectSchemaBuilder) {
		$this->objectSchemaBuilder = $objectSchemaBuilder;
	}

	/**
	 * @param Tx_ExtbaseKickstarter_Service_CodeGenerator $codeGenerator
	 * @return void
	 */
	public function injectCodeGenerator(Tx_ExtbaseKickstarter_Service_CodeGenerator $codeGenerator) {
		$this->codeGenerator = $codeGenerator;
	}

	/**
	 * Name of this domain object.
	 * @param string $domainObjectName the name of this domain object
	 */
	public function setDomainObjectName($domainObjectName) {
		$this->domainObjectName = $domainObjectName;
	}

	/**
	 * We just pass the action name through here, as we need it in parseTemplate.
	 * @param string $actionName
	 * @return string
	 */
	protected function resolveTemplatePathAndFilename($actionName = NULL) {
		$actionName = ($actionName !== NULL ? $actionName : $this->controllerContext->getRequest()->getControllerActionName());
		$actionName = strtolower($actionName);

		return $actionName;
	}

	/**
	 *
	 * @param string $actionName
	 * @return string the HTML code
	 */
	protected function parseTemplate($actionName) {
		$allowedActionNames = array('index', 'new', 'edit', 'show');
		if (!in_array($actionName, $allowedActionNames)) {
			return parent::parseTemplate($actionName);
		}

		$domainObject = $this->objectSchemaBuilder->buildDomainObjectByReflection($this->controllerContext->getRequest()->getControllerExtensionName(), $this->domainObjectName);
		$action = new Tx_ExtbaseKickstarter_Domain_Model_Action();
		$action->setName($actionName);
		$template = $this->codeGenerator->generateDomainTemplate($domainObject, $action);
		return $this->templateParser->parse($template);
	}

	public function resolvePartialPathAndFilename($partialName) {
		if ($partialName !== 'formErrors') {
			return parent::resolvePartialPathAndFilename($partialName);
		}
		return t3lib_extMgm::extPath('extbase_kickstarter').'Resources/Private/CodeTemplates/Resources/Private/Partials/formErrors.htmlt';	
	}	
}

?>