{namespace k=EBT\ExtensionBuilder\ViewHelpers}
<f:if condition="{domainObject.hasBooleanProperties}">if ($new{domainObject.name} == null) { // workaround for fluid bug ##5636
    $new{domainObject.name} = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance({domainObject.className}::class);
}</f:if>
$this->view->assign('new{domainObject.name}', $new{domainObject.name});
