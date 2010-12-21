{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}<?php
/***************************************************************
*  Copyright notice
*
*  (c) <f:format.date format="Y">now</f:format.date> <f:for each="{extension.persons}" as="person">{person.name} <f:if condition="{person.email}"><{person.email}></f:if><f:if condition="{person.company}">, {person.company}</f:if>
*  			</f:for>
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
	 * @var {property.typeForComment}<f:if condition="{property.validateAnnotation}">
	 * {property.validateAnnotation}</f:if>
	 */
	protected ${property.name};
	</f:for><k:removeNewlines><f:if condition="{domainObject.AnyToManyRelationProperties}">
	/**
	 * The constructor.
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}
	
	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage instances.
	 *
	 */
	protected function initStorageObjects(){
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
	
	</f:if></k:removeNewlines><f:for each="{domainObject.properties}" as="property">
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
	<f:if condition="{k:isOfType(object:property, type:'Property_BooleanProperty')}">
	/**
	 * Returns the state of {property.name}
	 *
	 * @return boolean the state of {property.name}
	 */
	public function is{property.name -> k:uppercaseFirst()}() {
		return $this->get{property.name -> k:uppercaseFirst()}();
	}
	</f:if><f:if condition="{k:isOfType(object:property, type:'Property_Relation_AnyToManyRelation')}">
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