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

	protected $domainObjectClassName;

	protected $domainObjectName;

	/**
	 *
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $reflectionService;

	public function __construct() {
		parent::__construct();
		$this->codeGenerator = t3lib_div::makeInstance('Tx_ExtbaseKickstarter_Service_CodeGenerator');
		$this->reflectionService = t3lib_div::makeInstance('Tx_Extbase_Reflection_Service');
	}

	public function setDomainObjectClassName($domainObjectClassName) {
		$this->domainObjectClassName = $domainObjectClassName;
	}
	public function setDomainObjectName($domainObjectName) {
		$this->domainObjectName = $domainObjectName;
	}

	protected function resolveTemplatePathAndFilename($actionName = NULL) {
		$actionName = ($actionName !== NULL ? $actionName : $this->controllerContext->getRequest()->getControllerActionName());
		$actionName = strtolower($actionName);

		return $actionName;
	}

	protected function parseTemplate($actionName) {
		$allowedActionNames = array('index', 'new', 'edit');
		if (!in_array($actionName, $allowedActionNames)) {
			throw new Exception('There is no scaffolding template for action "' . $actionName . '"'); // TODO: Replace by proper exception!
		}

		$domainObject = $this->buildDomainObjectByReflection();
		$action = new Tx_ExtbaseKickstarter_Domain_Model_Action();
		$action->setName($actionName);
		$template = $this->codeGenerator->generateDomainTemplate($domainObject, $action);
		return $this->templateParser->parse($template);
	}

	/**
	 * @return Tx_ExtbaseKickstarter_Domain_Model_DomainObject
	 */
	protected function buildDomainObjectByReflection() {
		$domainObject = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject();
		$domainObject->setName($this->domainObjectName);
		$classSchema = $this->reflectionService->getClassSchema($this->domainObjectClassName);

		foreach ($classSchema->getProperties() as $propertyName => $propertyDescription) {
			if ($propertyName == 'uid') continue;
			$propertyType = 'Tx_ExtbaseKickstarter_Domain_Model_Property_' . $this->resolveKickstarterPropertyTypeFromPropertyDescription($propertyDescription['type'], $propertyDescription['elementType']);
			$property = new $propertyType;
			$property->setName($propertyName);
			$domainObject->addProperty($property);
		}

		return $domainObject;
	}


	/**
	 * See Tx_Extbase_Reflection_ClassSchema::ALLOWED_TYPES_PATTERN
	 */
	protected function resolveKickstarterPropertyTypeFromPropertyDescription($phpPropertyType, $elementType) {
		switch ($phpPropertyType) {
			case 'integer': return 'IntegerProperty';
			case 'float': return 'FloatProperty';
			case 'boolean': return 'BooleanProperty';
			case 'string' : return 'StringProperty';
			case 'DateTime': return 'DateTimeProperty';
			case 'array':
			case 'ArrayObject':
			case 'Tx_Extbase_Persistence_ObjectStorage':
				return 'Relation_ZeroToManyRelation'; // TODO: Is this correct?
			default:
			// Tx_*
				return 'Relation_ZeroToOneRelation';
		}
	}
}
?>