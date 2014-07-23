\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
	'{property.name}',
	array(<f:if condition="{property.maxItems}">'maxitems' => {property.maxItems},</f:if>
		'appearance' => array(
			'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
		),
		'foreign_types' => array(
			'0' => array(
				'showitem' => '
				--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
				--palette--;;filePalette'
			),
			\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
				'showitem' => '
				--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
				--palette--;;filePalette'
			),
			\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
				'showitem' => '
				--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
				--palette--;;filePalette'
			),
			\TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
				'showitem' => '
				--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
				--palette--;;filePalette'
			),
			\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
				'showitem' => '
				--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
				--palette--;;filePalette'
			),
			\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
				'showitem' => '
				--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
				--palette--;;filePalette'
			)
		)
	),
	$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
),