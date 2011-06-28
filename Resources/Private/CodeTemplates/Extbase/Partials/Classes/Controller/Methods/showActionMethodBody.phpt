{namespace k=Tx_ExtensionBuilder_ViewHelpers}
$this->view->assign('{domainObject.name -> k:format.lowercaseFirst()}', ${domainObject.name -> k:format.lowercaseFirst()});