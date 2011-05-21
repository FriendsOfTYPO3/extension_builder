{namespace k=Tx_ExtensionBuilder_ViewHelpers}
	/**
	 * Creates a new {domainObject.name} and forwards to the list action.
	 *
	 * @param {domainObject.className} $new{domainObject.name} a fresh {domainObject.name} object which has not yet been added to the repository
	 * @return void
	 */
	public function createAction({domainObject.className} $new{domainObject.name}) {
		$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->add($new{domainObject.name});
		$this->flashMessageContainer->add('Your new {domainObject.name} was created.');
		<k:format.trim>
		<f:if condition="{settings.extConf.advancedMode}==0">
		<f:if condition="{extension.needsUploadFolder}">
		if(!empty($_FILES)){
			$this->flashMessageContainer->add('File upload is not yet supported by the Persistence Manager. You have to implement it yourself.');
		}
		</f:if>
		</f:if>
		</k:format.trim>
		$this->redirect('list');
	}