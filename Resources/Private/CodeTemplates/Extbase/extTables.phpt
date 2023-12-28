{namespace k=EBT\ExtensionBuilder\ViewHelpers}<?php
defined('TYPO3') || die();

(static function() {<f:for each="{extension.domainObjects}" as="domainObject"><f:if condition="{domainObject.mappedToExistingTable}"><f:else>
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('{domainObject.databaseTableName}');
</f:else></f:if></f:for>})();
