[
    'type' => 'slug',
    'size' => 50,
    'generatorOptions' => [
        'fields' => ['title'], // TODO: adjust this field to the one you want to use
        'fieldSeparator' => '-',
        'replacements' => [
            '/' => '',
        ],
    ],
    'fallbackCharacter' => '-',
    'eval' => 'uniqueInPid',
],
