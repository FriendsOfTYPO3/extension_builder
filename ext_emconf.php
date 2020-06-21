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
    'description' => 'The Extension Builder helps you build and manage your Extbase based TYPO3 extensions. Consider using the latest version from https://github.com/FriendsOfTYPO3/extension_builder',
    'category' => 'module',
    'author' => 'Nico de Haen',
    'author_email' => 'mail@ndh-websolutions.de',
    'state' => 'beta',
    'createDirs' => 'uploads/tx_extensionbuilder/backups',
    'version' => '9.11.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.9.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ],
];
