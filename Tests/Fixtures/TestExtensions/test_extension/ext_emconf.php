<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ExtensionBuilder Test Extension',
    'description' => 'This is just a test extension created by the ExtensionBuilder',
    'category' => 'misc',
    'author' => 'John Doe',
    'author_email' => 'mail@typo3.com',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
