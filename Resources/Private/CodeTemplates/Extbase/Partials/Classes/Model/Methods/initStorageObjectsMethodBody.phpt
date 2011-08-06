{namespace k=Tx_ExtensionBuilder_ViewHelpers}
/**
 * Do not modify this method!
 * It will be rewritten on each save in the extension builder
 * You may modify the constructor of this class instead
 */
<k:format.trim>
<f:for each="{domainObject.AnyToManyRelationProperties}" as="anyToManyRelationProperty">
$this->{anyToManyRelationProperty.name} = new Tx_Extbase_Persistence_ObjectStorage();
</f:for>
</k:format.trim>