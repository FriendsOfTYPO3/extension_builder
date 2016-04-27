<?php
defined('TYPO3_MODE') || die('Access denied.');
call_user_func(
    function($extKey)
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'FIXTURE.TestExtension',
            'Testplugin',
            [
                'Main' => 'list, show, new, create, edit, update, delete'
            ],
            // non-cacheable actions
            [
                'Main' => 'create, update, delete'
            ]
        );
    },
    $_EXTKEY
);
