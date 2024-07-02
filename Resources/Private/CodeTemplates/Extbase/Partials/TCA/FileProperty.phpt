[
    'type' => 'file',<f:if condition="{property.allowedFileTypes} !== ''"><f:then>
    'allowed' => '{property.allowedFileTypes}',</f:then><f:else>
    'allowed' => 'common-image-types',</f:else></f:if><f:if condition="{property.minItems} !== 0 && {property.minItems} !== ''">
    'minitems' => {property.minItems},</f:if><f:if condition="{property.maxItems} !== 0 && {property.maxItems} !== ''">
    'maxitems' => {property.maxItems},</f:if>
],