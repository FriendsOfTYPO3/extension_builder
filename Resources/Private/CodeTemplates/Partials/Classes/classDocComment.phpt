{namespace k=Tx_ExtensionBuilder_ViewHelpers}<k:render partial="Classes/licenseHeader.phpt" arguments="{persons:extension.persons}" />
/**
<f:if condition="{classSchema}">
 * {classSchema.description}</f:if>
 *
 * @package {extension.extensionKey}
 *<f:for each="{classSchema.annotations}" as="annotation">
 * @{annotation}</f:for>
 */