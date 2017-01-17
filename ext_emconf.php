<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "extension_builder".
 *
 * Auto generated 13-05-2014 05:27
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Extension Builder',
    'description' => 'The Extension Builder helps you build and manage your Extbase based TYPO3 extensions.',
    'category' => 'module',
    'author' => 'Nico de Haen',
    'author_email' => 'mail@ndh-websolutions.de',
    'state' => 'beta',
    'uploadfolder' => 1,
    'createDirs' => 'uploads/tx_extensionbuilder/backups',
    'clearCacheOnLoad' => 0,
    'version' => '7.6.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-7.6.99'
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
            'phpunit' => '',
        ),
    ),
);
