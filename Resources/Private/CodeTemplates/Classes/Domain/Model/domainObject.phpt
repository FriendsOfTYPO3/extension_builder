{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}<?php
<k:render partial="Classes/licenseHeader.phpt" arguments="{persons:extension.persons}" />


/**
 * {domainObject.description}
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class {domainObject.className} extends {domainObject.baseClass} {
<f:for each="{domainObject.properties}" as="property">
	/**
	 * {property.description}
	 *
	 * @var {property.typeForComment} ${property.name}<f:if condition="{property.validateAnnotation}">
	 * {property.validateAnnotation}</f:if>
	 */
	protected ${property.name}<f:if condition="{property.default}"> = {property.value}</f:if>;
</f:for><f:if condition="{domainObject.AnyToManyRelationProperties}">
	/**
	 * The constructor.
	 *
	 * @return void
	 */
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
		* It will be rewritten on each save in the kickstarter
		* You may modify the constructor of this class instead
		*/
		<k:removeNewlines>
		<f:for each="{domainObject.AnyToManyRelationProperties}" as="property">
		$this->{property.name} = new Tx_Extbase_Persistence_ObjectStorage();
		</f:for>
		</k:removeNewlines>
	}
</f:if><f:for each="{domainObject.properties}" as="property">
	/**
	 * Setter for {property.name}
	 *
	 * @param {property.typeForComment} ${property.name} {property.description}
	 * @return void
	 */
	public function set{property.name -> k:uppercaseFirst()}({property.typeHintWithTrailingWhiteSpace}${property.name}) {
		$this->{property.name} = ${property.name};
	}

	/**
	 * Getter for {property.name}
	 *
	 * @return {property.typeForComment} {property.description}
	 */
	public function get{property.name -> k:uppercaseFirst()}() {
		return $this->{property.name};
	}
<f:if condition="{k:isOfType(object:property, type:'DomainObject_BooleanProperty')}">
	/**
	 * Returns the state of {property.name}
	 *
	 * @return boolean the state of {property.name}
	 */
	public function is{property.name -> k:uppercaseFirst()}() {
		return $this->get{property.name -> k:uppercaseFirst()}();
	}
</f:if><f:if condition="{k:isOfType(object:property, type:'DomainObject_Relation_AnyToManyRelation')}">
	/**
	 * Adds a {property.foreignClass.name -> k:uppercaseFirst()}
	 *
	 * @param {property.foreignClass.className} the {property.foreignClass.name -> k:uppercaseFirst()} to be added
	 * @return void
	 */
	public function add{property.name->k:singularize()->k:uppercaseFirst()}({property.foreignClass.className} ${property.name->k:singularize()}) {
		$this->{property.name}->attach(${property.name -> k:singularize()});
	}

	/**
	 * Removes a {property.foreignClass.name -> k:uppercaseFirst()}
	 *
	 * @param {property.foreignClass.className} the {property.foreignClass.name -> k:uppercaseFirst()} to be removed
	 * @return void
	 */
	public function remove{property.name->k:singularize()->k:uppercaseFirst()}({property.foreignClass.className} ${property.name->k:singularize()}ToRemove) {
		$this->{property.name}->detach(${property.name -> k:singularize()}ToRemove);
	}
</f:if></f:for>
}
?>