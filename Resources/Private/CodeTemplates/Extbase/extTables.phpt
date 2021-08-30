{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3') || die();

(static function() {<f:if condition="{extension.BackendModules}"><f:for each="{extension.BackendModules}" as="backendModule">
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        '{extension.extensionName}',
        '{backendModule.mainModule}',
        '{backendModule.key}',
        '',
        [<f:if condition="{backendModule.controllerActionCombinations}"><f:then>
            <f:for each="{backendModule.controllerActionCombinations}" as="actionNames" key="controllerName">\{extension.vendorName}\{extension.extensionName}\Controller\{controllerName}Controller::class => '<f:for each="{actionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>',
            </f:for></f:then><f:else>
            <f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject">\{extension.vendorName}\{extension.extensionName}\Controller\{domainObject.name}Controller::class => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>',</f:for></f:else></f:if>
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:{extension.extensionKey}/Resources/Public/Icons/user_mod_{backendModule.key}.svg',
            'labels' => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_{backendModule.key}.xlf',
        ]
    );
</f:for>
</f:if><f:for each="{extension.domainObjects}" as="domainObject"><f:if condition="{domainObject.mappedToExistingTable}"><f:else>
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('{domainObject.databaseTableName}', 'EXT:{extension.extensionKey}/Resources/Private/Language/locallang_csh_{domainObject.databaseTableName}.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('{domainObject.databaseTableName}');
</f:else></f:if></f:for>})();
