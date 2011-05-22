{namespace k=Tx_ExtensionBuilder_ViewHelpers}
	/**
	 * Displays all {domainObject.name -> k:pluralize()}
	 *
	 * @return void
	 */
	public function listAction() {
		<k:format.trim><f:if condition="{settings.extConf.advancedMode}==0">
		$configuration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		if(empty($configuration['persistence']['storagePid'])){
			$this->flashMessageContainer->add('No storagePid! You have to include the static template of this extension and set the constant plugin.tx_' . t3lib_div::lcfirst($this->extensionName) . '.persistence.storagePid in the constant editor');
			$this->redirect('list');
		}
		</f:if>
		</k:format.trim>
		${domainObject.name -> k:format.lowercaseFirst() -> k:pluralize()} = $this->{domainObject.name -> k:format.lowercaseFirst()}Repository->findAll();
		$this->view->assign('{domainObject.name -> k:format.lowercaseFirst() -> k:pluralize()}', ${domainObject.name -> k:format.lowercaseFirst() -> k:pluralize()});
	}