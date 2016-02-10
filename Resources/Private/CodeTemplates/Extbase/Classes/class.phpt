{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
<f:if condition="{classObject.nameSpace}">namespace {classObject.nameSpace};</f:if>
<f:if condition="{classObject.aliasDeclarations}"><f:for each="{classObject.aliasDeclarations}" as="aliasDeclaration">
use {aliasDeclaration};</f:for></f:if>
{classObject.docComment}
<f:if condition="{classObject.modifier}"><f:for each="{classObject.modifierNames}" as="modifierName">{modifierName} </f:for></f:if>class {classObject.name}<k:class classObject="{classObject}"  renderElement="parentClass" /> <k:class classObject="{classObject}"  renderElement="interfaces" />
{
<f:for each="{classObject.constants}" as="constant">
    /**
     *<f:for each="{constant.docComment.getDescriptionLines}" as="descriptionLine">
     * {descriptionLine}</f:for>
     *<f:for each="{constant.tags}" as="tag">
     * {tag}</f:for>
     */
    const {constant.name} = {constant.value};
</f:for><f:for each="{classObject.properties}" as="property"><f:if condition="{property.precedingBlock}">
    <k:format.removeMultipleNewlines>{property.precedingBlock}</k:format.removeMultipleNewlines>
    </f:if>
    /**<f:for each="{property.descriptionLines}" as="descriptionLine">
     * {descriptionLine}</f:for>
     *<f:for each="{property.annotations}" as="annotation">
     * @{annotation}</f:for>
     */
    <f:for each="{property.modifierNames}" as="modifierName">{modifierName} </f:for>${property.name}<f:if condition="{property.hasValue}"> = {property.value}</f:if>;
</f:for><f:for each="{classObject.methods}" as="method"><f:if condition="{method.precedingBlock}">
    <k:format.removeMultipleNewlines>{method.precedingBlock}</k:format.removeMultipleNewlines>
    </f:if>
    /**<f:for each="{method.descriptionLines}" as="descriptionLine">
     * {descriptionLine}</f:for>
     *<f:for each="{method.annotations}" as="annotation">
     * @{annotation}</f:for>
     */
    <f:for each="{method.modifierNames}" as="modifierName">{modifierName} </f:for>function {method.name}(<k:method methodObject="{method}"  renderElement="parameter" />)
    <![CDATA[{]]>
<f:format.raw>{method.body}</f:format.raw>
    <![CDATA[}]]>
</f:for>
}
{classObject.appendedBlock}
