{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}<?php
<k:render partial="Classes/licenseHeader.phpt" arguments="{persons:extension.persons}" />

/**
 * Controller for the {domainObject.name} object
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class {domainObject.controllerName} extends Tx_Extbase_MVC_Controller_ActionController {
	<f:if condition="{domainObject.aggregateRoot}">
	/**
	 * {domainObject.name -> k:lowercaseFirst()}Repository
	 * 
	 * @var {domainObject.domainRepositoryClassName}
	 */
	protected ${domainObject.name -> k:lowercaseFirst()}Repository;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository = t3lib_div::makeInstance('{domainObject.domainRepositoryClassName}');
	}
	</f:if>
	<f:for each="{domainObject.actions}" as="action">
		<k:render partial="Classes/Controller/{action.name}Action.phpt" arguments="{domainObject:domainObject,extension:extension,settings:settings}" />
	</f:for>

}
?>