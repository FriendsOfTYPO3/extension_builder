<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class Tx_ExtbaseKickstarter_Scaffolding_ScaffoldingView extends Tx_Fluid_View_TemplateView {

	/**
	 * @var Tx_ExtbaseKickstarter_Service_CodeGenerator
	 */
	protected $codeGenerator;

	/**
	 * @var Tx_ExtbaseKickstarter_ObjectSchemaBuilder
	 */
	protected $objectSchemaBuilder;

	/**
	 * @var string
	 */
	protected $domainObjectName;

	/**
	 * Constructor. Initializes the object schema builder and the code generator.
	 */
	public function __construct() {
		parent::__construct();
		$this->objectSchemaBuilder = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_ObjectSchemaBuilder');
		$this->codeGenerator = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_CodeGenerator');
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
		$allowedActionNames = array('index', 'new', 'edit');
		if (!in_array($actionName, $allowedActionNames)) {
			throw new Exception('There is no scaffolding template for action "' . $actionName . '"'); // TODO: Replace by proper exception!
		}

		$domainObject = $this->objectSchemaBuilder->buildDomainObjectByReflection($this->controllerContext->getRequest()->getControllerExtensionName(), $this->domainObjectName);
		$action = new Tx_ExtbaseKickstarter_Domain_Model_Action();
		$action->setName($actionName);
		$template = $this->codeGenerator->generateDomainTemplate($domainObject, $action);
		return $this->templateParser->parse($template);
	}

	
}
?>