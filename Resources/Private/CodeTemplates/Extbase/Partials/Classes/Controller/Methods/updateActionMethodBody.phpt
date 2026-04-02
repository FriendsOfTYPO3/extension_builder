{namespace k=EBT\ExtensionBuilder\ViewHelpers}
$this->{domainObject.name -> k:format.lowercaseFirst()}Repository->update(${domainObject.name -> k:format.lowercaseFirst()});
$this->addFlashMessage('Your {domainObject.name} was updated.', '', \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::OK);
$this->redirect('list');