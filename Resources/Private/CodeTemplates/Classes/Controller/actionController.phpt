{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}<?php
<k:render partial="Classes/licenseHeader.phpt" arguments="{persons:extension.persons}" />

/**
 * Controller for the {domainObject.name} object
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
	 * Dependency injection of the {domainObject.name} Repository
 	 *
	 * @param {domainObject.domainRepositoryClassName} ${domainObject.name -> k:lowercaseFirst()}Repository
 	 * @return void
-	 */
	public function injectBlogRepository(Tx_BlogExample_Domain_Repository_BlogRepository $blogRepository) {
		$this->{domainObject.name -> k:lowercaseFirst()}Repository = ${domainObject.name -> k:lowercaseFirst()}Repository;
	}

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