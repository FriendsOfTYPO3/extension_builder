<?php
{namespace k=EBT\ExtensionBuilder\ViewHelpers}{escaping off}

$EM_CONF[$_EXTKEY] = [
    'title' => '{extension.name -> k:format.quoteString()}',
    'description' => '{extension.description -> k:format.quoteString()}',
    'category' => '{extension.category}',
    'author' => '<f:for each="{extension.persons}" as="person" iteration="counter"><f:if condition="{counter.index} > 0">, </f:if>{person.name}</f:for>',
    'author_email' => '<f:for each="{extension.persons}" as="person" iteration="counter"><f:if condition="{person.email}"><f:if condition="{counter.index} > 0">, </f:if>{person.email}</f:if></f:for>',
    'state' => '{extension.readableState}',
    'clearCacheOnLoad' => 0,
    'version' => '{extension.version}',
    'constraints' => [
        'depends' => [<f:for each="{extension.dependencies}" as="version" key="extensionKey">
            '{extensionKey}' => '{version}',</f:for>
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
