[
    'type' => 'file',
    'allowed' => 'common-image-types',
    'appearance' => [
        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
    ],
    'overrideChildTca' => [
        'types' => [
            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
            ],
        ],
    ],<f:if condition="{property.maxItems}">
    'maxitems' => {property.maxItems},</f:if><f:if condition="{property.required}">
    'minitems' => 1,</f:if>
],
