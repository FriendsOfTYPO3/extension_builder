<?php

return [<f:for each="{extension.backendModules}" as="backendModule">
    '{backendModule.key}' => [
        'parent' => '{backendModule.mainModule}',
        'position' => ['bottom'],
        <f:switch expression="{backendModule.mainModule}">
             <f:case value="tools">'access' => 'systemMaintainer',</f:case>
             <f:case value="system">'access' => 'admin',</f:case>
             <f:defaultCase>'access' => 'user',</f:defaultCase>
        </f:switch>
        'workspaces' => 'live',
        'path' => '/module/{extension.vendorName}/{backendModule.key}',
        'labels' => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_{backendModule.key}.xlf',
        'extensionName' => '{extension.extensionName}',
        'controllerActions' => [<f:for each="{backendModule.controllerActionCombinations}" as="controller" key="controllerKey" iteration="controllerIterator">
            \{extension.namespaceName}\Controller\{controllerKey}Controller::class => [
                <f:for each="{controller}" as="action" iteration="actionIterator">'{action}'{f:if(condition: actionIterator.isLast, then: '', else: ',')}</f:for>
            ]{f:if(condition: controllerIterator.isLast, then: '', else: ',')}</f:for>
        ],
    ],
</f:for>
];
