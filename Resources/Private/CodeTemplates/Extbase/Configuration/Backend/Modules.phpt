{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
return [<f:for each="{extension.BackendModules}" as="backendModule">
    '{extension.extensionKey}_{backendModule.key}' => [
        'parent' => '{backendModule.mainModule}',
        'position' => [],
        'access' => 'user,group',
        'iconIdentifier' => '{extension.extensionKey}-module-{backendModule.key}',
        'labels' => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_{backendModule.key}.xlf',
        'extensionName' => '{extension.extensionName}',
        'controllerActions' => [<f:if condition="{backendModule.controllerActionCombinations}"><f:then>
            <f:for each="{backendModule.controllerActionCombinations}" as="actionNames" key="controllerName">\{extension.vendorName}\{extension.extensionName}\Controller\{controllerName}Controller::class => [<f:for each="{actionNames}" as="actionName" iteration="i">'{actionName}'<f:if condition="{i.isLast} == 0">, </f:if></f:for>],
            </f:for></f:then><f:else>
            <f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">\{extension.vendorName}\{extension.extensionName}\Controller\{domainObject.name}Controller::class => [<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">'{action.name}'<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>],
            </f:for></f:else></f:if>
        ],
    ],
</f:for>];
