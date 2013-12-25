{namespace k=EBT\ExtensionBuilder\ViewHelpers}
/**
 * Do not modify this method!
 * It will be rewritten on each save in the extension builder
 * You may modify the constructor of this class instead
 */
<k:format.trim>
<f:for each="{domainObject.AnyToManyRelationProperties}" as="anyToManyRelationProperty">
<f:format.raw>$this->{anyToManyRelationProperty.name} = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();</f:format.raw>
</f:for>
</k:format.trim>