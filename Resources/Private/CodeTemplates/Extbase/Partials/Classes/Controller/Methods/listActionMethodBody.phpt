{namespace k=EBT\ExtensionBuilder\ViewHelpers}
${domainObject.name -> k:format.lowercaseFirst() -> k:pluralize()} = $this->{domainObject.name -> k:format.lowercaseFirst()}Repository->findAll();
$this->view->assign('{domainObject.name -> k:format.lowercaseFirst() -> k:pluralize()}', ${domainObject.name -> k:format.lowercaseFirst() -> k:pluralize()});