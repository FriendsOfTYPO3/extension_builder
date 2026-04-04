[
    'type' => 'slug',
    'size' => 50,
    'generatorOptions' => [
        'fields' => ['{domainObject.firstStringPropertyFieldName}'],
        'fieldSeparator' => '-',
        'replacements' => [
            '/' => '',
        ],
    ],
    'fallbackCharacter' => '-',
],
