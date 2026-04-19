<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Extension Builder Test Extension',
    'description' => 'This is just a test extension created by the Extension Builder',
    'category' => 'misc',
    'author' => 'John Doe',
    'author_email' => 'mail@typo3.com',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.3.99',
            'extbase' => '14.0.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
