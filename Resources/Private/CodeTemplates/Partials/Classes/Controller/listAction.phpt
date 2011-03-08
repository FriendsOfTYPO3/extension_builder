{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}
	/**
	 * Displays all {domainObject.name -> k:pluralize()}
	 *
	 * @return void
	 */
	public function listAction() {
		${domainObject.name -> k:lowercaseFirst() -> k:pluralize()} = $this->{domainObject.name -> k:lowercaseFirst()}Repository->findAll();
		<f:if condition="{settings.advancedMode}==0">
		if(count(${domainObject.name -> k:lowercaseFirst() -> k:pluralize()}) < 1){
			$settings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
			if(empty($settings['persistence']['storagePid'])){
				$this->flashMessageContainer->add('No storagePid configured!');
			}
		}
		</f:if>
		$this->view->assign('{domainObject.name -> k:lowercaseFirst() -> k:pluralize()}', ${domainObject.name -> k:lowercaseFirst() -> k:pluralize()});
	}