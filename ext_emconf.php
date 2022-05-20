<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "extension_builder".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Extension Builder',
    'description' => 'The Extension Builder helps you to develop a TYPO3 extension based on the domain-driven MVC framework Extbase and the templating engine Fluid.',
    'category' => 'module',
    'author' => 'Extension Builder Team',
    'author_email' => '',
    'state' => 'stable',
    'version' => '11.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '11.4.0-11.9.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ],
];
