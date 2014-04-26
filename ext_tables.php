<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}



if (TYPO3_MODE === 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
	\EBT\ExtensionBuilder\Parser\AutoLoader::register();
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

	// \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
	//     'ExtensionBuilder::wiringEditorSmdEndpoint',
	//	   'EBT\ExtensionBuilder\Configuration\ConfigurationManager->getWiringEditorSmd'
	// );
	// To stay compatible with older TYPO3 versions, we register the ajax script the
	// old way. It is also OK to not have this Ajax call to be CSRF protected as it
	// is of no use for an attacker in this scenario even if the result contains the
	// module token.
	$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX']['ExtensionBuilder::wiringEditorSmdEndpoint'] = '' .
		'EBT\ExtensionBuilder\Configuration\ConfigurationManager->getWiringEditorSmd';

}
