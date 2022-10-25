\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
    '{property.fieldName}',
    [
        'appearance' => [
            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
        ],
        'overrideChildTca' => [
            'types' => [
                '0' => [
                    'showitem' => '
                    --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                    --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                    'showitem' => '
                    --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                    --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                    'showitem' => '
                    --palette--;;imageoverlayPalette,
                    --palette--;;filePalette',
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                    'showitem' => '
                    --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                    --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                    'showitem' => '
                    --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                    --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                    'showitem' => '
                    --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                    --palette--;;filePalette'
                ]
            ],
        ],
        'foreign_match_fields' => [
            'fieldname' => '{property.fieldName}',
            'tablenames' => '{property.domainObject.databaseTableName}',
            'table_local' => 'sys_file',
        ]<f:if condition="{property.maxItems}">,
        'maxitems' => {property.maxItems}</f:if><f:if condition="{property.required}">,
        'minitems' => 1</f:if>
    ],
    $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
),
