{namespace k=Tx_ExtensionBuilder_ViewHelpers}<?php
<k:render partial="Classes/licenseHeader.phpt" arguments="{persons:extension.persons}" />

/**
 * Controller for the {domainObject.name} object
 */
class {domainObject.controllerName} extends Tx_Extbase_MVC_Controller_ActionController {
<f:if condition="{domainObject.aggregateRoot}">
	/**
	 * @var {domainObject.domainRepositoryClassName}
	 */
	protected ${domainObject.name -> k:lowercaseFirst()}Repository;

	/**
	 * @param {domainObject.domainRepositoryClassName} ${domainObject.name -> k:lowercaseFirst()}Repository
 	 * @return void
	 */
	public function inject{domainObject.name}Repository({domainObject.domainRepositoryClassName} ${domainObject.name -> k:lowercaseFirst()}Repository) {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository = ${domainObject.name -> k:lowercaseFirst()}Repository;
	}
</f:if>
<f:for each="{domainObject.actions}" as="action">
<k:render partial="Classes/Controller/{action.name}Action.phpt" arguments="{domainObject:domainObject,extension:extension,settings:settings}" />
</f:for>

}
?>