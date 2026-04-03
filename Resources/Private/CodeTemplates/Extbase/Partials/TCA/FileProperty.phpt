[
    'type' => 'file',
    'allowed' => <f:if condition="{property.allowedFileTypes}"><f:then>'{property.allowedFileTypes}'</f:then><f:else>'common-media-types'</f:else></f:if>,
    'appearance' => [
        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference',
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
