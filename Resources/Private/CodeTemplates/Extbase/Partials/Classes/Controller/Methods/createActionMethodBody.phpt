{namespace k=Tx_ExtensionBuilder_ViewHelpers}
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