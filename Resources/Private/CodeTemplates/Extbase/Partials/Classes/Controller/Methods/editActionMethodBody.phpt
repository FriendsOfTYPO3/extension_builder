{namespace k=EBT\ExtensionBuilder\ViewHelpers}
$this->view->assign('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});