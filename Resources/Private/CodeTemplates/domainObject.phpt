<?php
/***************************************************************
*  Copyright notice
*
*  (c)  TODO - INSERT COPYRIGHT
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
 * {domainObject.description}
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
class {domainObject.className} extends {domainObject.baseClass} {
	<f:for each="{domainObject.properties}" as="property">
	/**
	 * {property.description}
	 * @var {property.typeForComment}<f:if condition="{property.validateAnnotation}">
	 * {property.validateAnnotation}</f:if>
	 */
	protected ${property.name};
	</f:for>

	/**
	 * Constructor. Initializes all Tx_Extbase_Persistence_ObjectStorage instances.
	 */
	public function __construct() {
		<k:removeNewlines>
		<f:for each="{domainObject.properties}" as="property"><f:if condition="{k:isOfType(object='{property}' type='Property_Relation_ZeroToManyRelation')}">
		$this->{property.name} = new Tx_Extbase_Persistence_ObjectStorage();
		</f:if></f:for>
	</k:removeNewlines>
	}
	<f:for each="{domainObject.properties}" as="property">
	/**
	 * Getter for {property.name}
	 * 
	 * @return {property.typeForComment} {property.description}
	 */
	public function get<k:uppercaseFirst>{property.name}</k:uppercaseFirst>() {
		return $this->{property.name}
	}
	
	/**
	 * Setter for {property.name}
	 *
	 * @param {property.typeForComment} ${property.name} {property.description}
	 * @return void
	 */
	public function set<k:uppercaseFirst>{property.name}</k:uppercaseFirst>({property.typeHint} ${property.name}) {
		$this->{property.name} = ${property.name};
	}
	<f:if condition="{k:isOfType(object='{property}' type='Property_Relation_ZeroToManyRelation')}">
	/**
	 * Add a <k:uppercaseFirst>{property.foreignClass.name}</k:uppercaseFirst>
	 *
	 * @param {property.foreignClass.className} The <k:uppercaseFirst>{property.foreignClass.name}</k:uppercaseFirst> to add
	 * @return void
	 */
	public function add<k:uppercaseFirst><k:singularize>{property.name}</k:singularize></k:uppercaseFirst>({property.foreignClass.className} $<k:singularize>{property.name}</k:singularize>) {
		$this->{property.name}->attach($<k:singularize>{property.name}</k:singularize>);
	}
	</f:if></f:for>

}
?>