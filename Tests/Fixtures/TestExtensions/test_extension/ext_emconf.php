<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Extension Builder Test Extension',
    'description' => 'This is just a test extension created by the ExtensionBuilder',
    'category' => 'misc',
    'author' => 'John Doe',
    'author_email' => 'mail@typo3.com',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => '1',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.9',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-7.6.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
