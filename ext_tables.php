<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}



if (TYPO3_MODE === 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
	/**
	 * Register Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'EBT.' . $_EXTKEY,
		'tools',
		'extensionbuilder',
		'',
		array(
			'BuilderModule' => 'index,domainmodelling,dispatchRpc',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:extension_builder/ext_icon.gif',
			'labels' => 'LLL:EXT:extension_builder/Resources/Private/Language/locallang_mod.xlf',
		)
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
	     'ExtensionBuilder::wiringEditorSmdEndpoint',
		   'EBT\ExtensionBuilder\Configuration\ConfigurationManager->getWiringEditorSmd'
	);

}
