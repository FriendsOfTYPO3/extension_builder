{namespace k=EBT\ExtensionBuilder\ViewHelpers}
$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->add($new{domainObject.name});
$this->flashMessageContainer->add('Your new {domainObject.name} was created.');
$this->redirect('list');