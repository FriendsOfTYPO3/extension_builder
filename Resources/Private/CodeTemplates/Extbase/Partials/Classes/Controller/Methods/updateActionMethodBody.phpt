{namespace k=EBT\ExtensionBuilder\ViewHelpers}
$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->update(${domainObject.name -> k:format.lowercaseFirst()});
$this->flashMessageContainer->add('Your {domainObject.name} was updated.');
$this->redirect('list');