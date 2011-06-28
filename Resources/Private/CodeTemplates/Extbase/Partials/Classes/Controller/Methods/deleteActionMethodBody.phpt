{namespace k=Tx_ExtensionBuilder_ViewHelpers}
$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->remove(${domainObject.name -> k:format.lowercaseFirst()});
$this->flashMessageContainer->add('Your {domainObject.name} was removed.');
$this->redirect('list');