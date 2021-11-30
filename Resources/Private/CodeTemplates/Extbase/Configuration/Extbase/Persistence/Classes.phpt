{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php

declare(strict_types=1);

return [<f:for each="{extension.domainObjectsThatNeedMappingStatements}" as="domainObject">
    \{domainObject.qualifiedClassName}::class => [
        <f:if condition="{domainObject.mapToTable}">'tableName' => '{domainObject.databaseTableName}',
        </f:if><f:if condition="{domainObject.hasPropertiesWithMappingStatements}">'properties' => [<f:for each="{domainObject.propertiesThatNeedMappingStatements}" as="property">
           '{property.name}' => [
                'fieldName' => '{property.fieldName}'
            ],</f:for>
        ]</f:if>
    ],
</f:for>];
