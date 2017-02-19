{namespace k=EBT\ExtensionBuilder\ViewHelpers}
if (!isset($GLOBALS['TCA']['{databaseTableName}']['ctrl']['type'])) {
    if (file_exists($GLOBALS['TCA']['{databaseTableName}']['ctrl']['dynamicConfigFile'])) {
        require_once($GLOBALS['TCA']['{databaseTableName}']['ctrl']['dynamicConfigFile']);
    }
    // no type field defined, so we define it here. This will only happen the first time the extension is installed!!
    $GLOBALS['TCA']['{databaseTableName}']['ctrl']['type'] = 'tx_extbase_type';
    $tempColumns{extension.shortExtensionKey}_{databaseTableName} = [];
    $tempColumns{extension.shortExtensionKey}_{databaseTableName}[$GLOBALS['TCA']['{databaseTableName}']['ctrl']['type']] = [
        'exclude' => true,
        'label'   => 'LLL:EXT:{extension.extensionKey}/Resources/Private/Language/locallang_db.xlf:{extension.shortExtensionKey}.tx_extbase_type',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['{domainObject.name}','{domainObject.recordType}']
            ],
            'default' => '{domainObject.recordType}',
            'size' => 1,
            'maxitems' => 1,
        ]
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('{databaseTableName}', $tempColumns{extension.shortExtensionKey}_{databaseTableName});
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    '{databaseTableName}',
    $GLOBALS['TCA']['{databaseTableName}']['ctrl']['type'],
    '',
    'after:' . $GLOBALS['TCA']['{databaseTableName}']['ctrl']['label']
);