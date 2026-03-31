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
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
