{namespace k=EBT\ExtensionBuilder\ViewHelpers}
/**
 *<f:if condition="{classSchema}">
 * {classSchema.description}</f:if>
 *
 *<f:for each="{classSchema.annotations}" as="annotation">
 * @{annotation}</f:for>
 */