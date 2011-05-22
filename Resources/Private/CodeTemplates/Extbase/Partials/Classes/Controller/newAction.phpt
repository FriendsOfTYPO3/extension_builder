{namespace k=Tx_ExtensionBuilder_ViewHelpers}
	/**
	 * Displays a form for creating a new  {domainObject.name}
	 *
	 * @param {domainObject.className} $new{domainObject.name} a fresh {domainObject.name} object which has not yet been added to the repository
	 * @return void
	 * @dontvalidate $new{domainObject.name}
	 */
	public function newAction({domainObject.className} $new{domainObject.name} = NULL) {
		<k:format.trim><f:if condition="{settings.extConf.advancedMode}==0">
		$configuration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		if(empty($configuration['persistence']['storagePid'])){
			$this->flashMessageContainer->add('No storagePid! You have to include the static template of this extension and set the constant plugin.tx_' . t3lib_div::lcfirst($this->extensionName) . '.persistence.storagePid in the constant editor');
		}
		</f:if>
		</k:format.trim>
		$this->view->assign('new{domainObject.name}', $new{domainObject.name});
	}