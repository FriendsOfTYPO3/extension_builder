{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3') || die();

(static function() {<f:for each="{extension.plugins}" as="plugin">
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        '{extension.extensionName}',
        '{plugin.key -> k:format.uppercaseFirst()}',
        [<f:if condition="{plugin.controllerActionCombinations}"><f:then>
            <f:for each="{plugin.controllerActionCombinations}" as="actionNames" key="controllerName" iteration="j">\{extension.vendorName}\{extension.extensionName}\Controller\{controllerName}Controller::class => '<f:for each="{actionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>'<f:if condition="{j.isLast} == 0">,
            </f:if></f:for></f:then><f:else>
            <f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject" iteration="j">\{extension.vendorName}\{extension.extensionName}\Controller\{domainObject.name}Controller::class => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:for>'<f:if condition="{j.isLast} == 0">,
            </f:if></f:for></f:else></f:if>
        ],
        // non-cacheable actions
        [<f:if condition="{plugin.noncacheableControllerActions}"><f:then>
            <f:for each="{plugin.noncacheableControllerActions}" as="noncachableActionNames" key="noncachableControllerName" iteration="j">\{extension.vendorName}\{extension.extensionName}\Controller\{noncachableControllerName}Controller::class => '<f:for each="{noncachableActionNames}" as="actionName" iteration="i">{actionName}<f:if condition="{i.isLast} == 0">, </f:if></f:for>'<f:if condition="{j.isLast} == 0">,
            </f:if></f:for></f:then><f:else>
            <f:for each="{extension.domainObjectsForWhichAControllerShouldBeBuilt}" as="domainObject" iteration="j">\{extension.vendorName}\{extension.extensionName}\Controller\{domainObject.name}Controller::class => '<f:for each="{domainObject.actions}" as="action" iteration="actionIterator"><f:if condition="{action.cacheable} == 0">{action.name}<f:if condition="{actionIterator.isLast} == 0">, </f:if></f:if></f:for>'<f:if condition="{j.isLast} == 0">,
            </f:if></f:for></f:else></f:if>
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
</f:for>
})();
