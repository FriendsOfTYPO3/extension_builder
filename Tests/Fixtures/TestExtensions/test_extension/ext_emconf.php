<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ExtensionBuilder Test Extension',
    'description' => 'This is just a test extension created by the ExtensionBuilder',
    'category' => 'misc',
    'author' => 'John Doe',
    'author_email' => 'mail@typo3.com',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '0.9',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
