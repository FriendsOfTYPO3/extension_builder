{namespace k=Tx_ExtensionBuilder_ViewHelpers}<?php
<k:render partial="Classes/licenseHeader.phpt" arguments="{persons:extension.persons,settings:settings}" />

/**
 * {domainObject.description}
 */
class {domainObject.className} extends {domainObject.baseClass} {
<f:for each="{domainObject.properties}" as="property">
	/**
	 * {property.description}
	 *
	 * @var {property.typeForComment}<f:if condition="{property.validateAnnotation}">
	 * {property.validateAnnotation}</f:if><f:if condition="{property.lazyLoading}">
	 * @lazy</f:if>
	 */
	protected ${property.name}<f:if condition="{property.default}"> = {property.value}</f:if>;
</f:for><f:if condition="{domainObject.AnyToManyRelationProperties}">

	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage instances.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		/**
		* Do not modify this method!
		* It will be rewritten on each save in the extension builder
		* You may modify the constructor of this class instead
		*/
		<k:format.trim>
		<f:for each="{domainObject.AnyToManyRelationProperties}" as="property">
		$this->{property.name} = new Tx_Extbase_Persistence_ObjectStorage();
		</f:for>
		</k:format.trim>
	}
</f:if><f:for each="{domainObject.properties}" as="property">
	/**
	 * @param {property.typeForComment} ${property.name}
	 * @return void
	 */
	public function set{property.name -> k:format.uppercaseFirst()}({property.typeHintWithTrailingWhiteSpace}${property.name}) {
		$this->{property.name} = ${property.name};
	}

	/**
	 * @return {property.typeForComment}
	 */
	public function get{property.name -> k:format.uppercaseFirst()}() {
		return $this->{property.name};
	}
<f:if condition="{k:isOfType(object:property, type:'DomainObject_BooleanProperty')}">
	/**
	 * @return boolean
	 */
	public function is{property.name -> k:format.uppercaseFirst()}() {
		return $this->get{property.name -> k:format.uppercaseFirst()}();
	}
</f:if><f:if condition="{k:isOfType(object:property, type:'DomainObject_Relation_AnyToManyRelation')}">
	/**
	 * @param {property.foreignClass.className} the {property.foreignClass.name -> k:format.uppercaseFirst()} to be added
	 * @return void
	 */
	public function add{property.name->k:singularize()->k:format.uppercaseFirst()}({property.foreignClass.className} ${property.name->k:singularize()}) {
		$this->{property.name}->attach(${property.name -> k:singularize()});
	}

	/**
	 * @param {property.foreignClass.className} the {property.foreignClass.name -> k:format.uppercaseFirst()} to be removed
	 * @return void
	 */
	public function remove{property.name->k:singularize()->k:format.uppercaseFirst()}({property.foreignClass.className} ${property.name->k:singularize()}ToRemove) {
		$this->{property.name}->detach(${property.name -> k:singularize()}ToRemove);
	}
</f:if></f:for>
}
?>