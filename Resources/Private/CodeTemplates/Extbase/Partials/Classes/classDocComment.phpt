/**
 *<f:if condition="{classSchema}">
 * <f:format.raw>{classSchema.description}</f:format.raw></f:if>
 *
 *<f:for each="{classSchema.annotations}" as="annotation">
 * @{annotation}</f:for>
 */