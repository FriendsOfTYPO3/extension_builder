{namespace k=EBT\ExtensionBuilder\ViewHelpers}
$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->add($new{domainObject.name});
$this->addFlashMessage('Your new {domainObject.name} was created.', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::OK);
return $this->redirect('list');