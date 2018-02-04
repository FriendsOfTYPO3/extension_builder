<?php
namespace EBT\ExtensionBuilder\Tests\Unit;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use EBT\ExtensionBuilder\Configuration\ExtensionBuilderConfigurationManager;
use EBT\ExtensionBuilder\Domain\Model\DomainObject;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation;
use EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty;
use EBT\ExtensionBuilder\Domain\Model\Extension;
use EBT\ExtensionBuilder\Domain\Model\Person;
use EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder;
use EBT\ExtensionBuilder\Service\ObjectSchemaBuilder;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionSchemaBuilderTest extends BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder
     */
    protected $extensionSchemaBuilder = null;
    /**
     * @var string
     */
    protected $extensionKey = '';

    protected function setUp()
    {
        parent::setUp();
        $this->extension = $this->createMock(Extension::class, ['getOverWriteSettings']);
        $this->extensionSchemaBuilder = $this->getAccessibleMock(ExtensionSchemaBuilder::class, ['dummy']);
        $this->extensionSchemaBuilder->injectConfigurationManager(new ExtensionBuilderConfigurationManager());
        /** @var $objectSchemaBuilder \EBT\ExtensionBuilder\Service\ObjectSchemaBuilder */
        $objectSchemaBuilder = $this->getAccessibleMock(ObjectSchemaBuilder::class, ['dummy']);
        $objectSchemaBuilder->injectConfigurationManager(new ExtensionBuilderConfigurationManager());
        $this->extensionSchemaBuilder->injectObjectSchemaBuilder($objectSchemaBuilder);
        $this->extensionKey = 'dummy';
    }

    /**
     * @test
     */
    public function conversionExtractsExtensionProperties()
    {
        $description = 'My cool fancy description';
        $name = 'ExtName';
        $extensionKey = $this->extensionKey;
        $state = 0;
        $version = '1.0.4';

        $input = [
            'properties' => [
                'description' => $description,
                'extensionKey' => $extensionKey,
                'name' => $name,
                'emConf' => [
                    'state' => $state,
                    'version' => $version
                ]
            ]
        ];

        $extension = new Extension();
        $extension->setDescription($description);
        $extension->setName($name);
        $extension->setExtensionKey($extensionKey);
        $extension->setState($state);
        $extension->setVersion($version);
        $extension->setExtensionDir('');

        $actual = $this->extensionSchemaBuilder->build($input);
        self::assertEquals($extension, $actual, 'Extension properties were not extracted.');
    }

    /**
     * @test
     */
    public function conversionExtractsPersons()
    {
        $persons = [];
        $persons[] = GeneralUtility::makeInstance(Person::class);
        $persons[] = GeneralUtility::makeInstance(Person::class);
        $persons[0]->setName('name0');
        $persons[0]->setRole('role0');
        $persons[0]->setEmail('email0');
        $persons[0]->setCompany('company0');
        $persons[1]->setName('name1');
        $persons[1]->setRole('role1');
        $persons[1]->setEmail('email1');
        $persons[1]->setCompany('company1');

        $input = [
            'properties' => [
                'description' => 'myDescription',
                'extensionKey' => 'myExtensionKey',
                'name' => 'myName',
                'emConf' => [
                    'state' => 'beta'
                ],
                'persons' => [
                    [
                        'company' => 'company0',
                        'email' => 'email0',
                        'name' => 'name0',
                        'role' => 'role0'
                    ],
                    [
                        'company' => 'company1',
                        'email' => 'email1',
                        'name' => 'name1',
                        'role' => 'role1'
                    ],
                ],
                'state' => 'beta'
            ]
        ];
        $extension = $this->extensionSchemaBuilder->build($input);
        self::assertEquals($extension->getPersons(), $persons, 'Persons set wrong in ObjectBuilder.');
    }

    /**
     * @test
     */
    public function conversionExtractsWholeExtensionMetadataWithRelations()
    {
        $input = [
            'modules' => [
                0 => [
                    // config
                    // name
                    'value' => [
                        'name' => 'Blog',
                        'objectsettings' => [
                            'description' => 'A blog object',
                            'aggregateRoot' => false,
                            'type' => 'Entity'
                        ],
                        'propertyGroup' => [
                            'properties' => [
                                0 => [
                                    'propertyName' => 'name',
                                    'propertyType' => 'String'
                                ],
                                1 => [
                                    'propertyName' => 'description',
                                    'propertyType' => 'String'
                                ]
                            ]
                        ],
                        'relationGroup' => [
                            'relations' => [
                                0 => [
                                    'relationName' => 'posts',
                                    'relationType' => 'zeroToMany',
                                    'propertyIsExcludeField' => 1
                                ]
                            ]
                        ]
                    ]
                ],
                1 => [
                    // config
                    // name
                    'value' => [
                        'name' => 'Post',
                        'objectsettings' => [
                            'description' => 'A blog post',
                            'aggregateRoot' => false,
                            'type' => 'Entity'
                        ],
                        'propertyGroup' => [
                            'properties' => []
                        ],
                        'relationGroup' => [
                            'relations' => [
                                0 => [
                                    'relationName' => 'comments',
                                    'relationType' => 'zeroToMany',
                                    'propertyIsExcludeField' => 1
                                ]
                            ]
                        ]
                    ]
                ],
                2 => [
                    // config
                    // name
                    'value' => [
                        'name' => 'Comment',
                        'objectsettings' => [
                            'description' => '',
                            'aggregateRoot' => false,
                            'type' => 'Entity'
                        ],
                        'propertyGroup' => [
                            'properties' => []
                        ],
                        'relationGroup' => [
                            'relations' => []
                        ]
                    ]
                ],
            ],
            'properties' => [
                'description' => 'Some description',
                'extensionKey' => $this->extensionKey,
                'name' => 'My ext name',
                'emConf' => [
                    'state' => 'beta'
                ]
            ],
            'wires' => [
                0 => [
                    'tgt' => [
                        'moduleId' => 1,
                        'terminal' => 'SOURCES'
                    ],
                    'src' => [
                        'moduleId' => 0, // hier stand leerstring drin
                        'terminal' => 'relationWire_0'
                    ]
                ],
                1 => [
                    'tgt' => [
                        'moduleId' => 2,
                        'terminal' => 'SOURCES'
                    ],
                    'src' => [
                        'moduleId' => 1,
                        'terminal' => 'relationWire_0'
                    ]
                ]
            ]
        ];

        $extension = new Extension();
        $extension->setName('My ext name');
        $extension->setState(Extension::STATE_BETA);
        $extension->setExtensionKey($this->extensionKey);
        $extension->setDescription('Some description');
        $extension->setExtensionDir('');

        $blog = new DomainObject();
        $blog->setName('Blog');
        $blog->setDescription('A blog object');
        $blog->setEntity(true);
        $blog->setAggregateRoot(false);
        $property = new StringProperty('name');
        $blog->addProperty($property);
        $property = new StringProperty('description');
        $blog->addProperty($property);

        $extension->addDomainObject($blog);

        $post = new DomainObject();
        $post->setName('Post');
        $post->setDescription('A blog post');
        $post->setEntity(true);
        $post->setAggregateRoot(false);
        $extension->addDomainObject($post);

        $comment = new DomainObject();
        $comment->setName('Comment');
        $comment->setDescription('');
        $comment->setEntity(true);
        $comment->setAggregateRoot(false);
        $extension->addDomainObject($comment);

        $relation = new ZeroToManyRelation('posts');
        $relation->setForeignModel($post);
        $relation->setExcludeField(1);
        $blog->addProperty($relation);

        $relation = new ZeroToManyRelation('comments');
        $relation->setForeignModel($comment);
        $relation->setExcludeField(1);
        $post->addProperty($relation);
        $actualExtension = $this->extensionSchemaBuilder->build($input);
        self::assertEquals($extension->getDomainObjects(), $actualExtension->getDomainObjects(), 'The extensions differ');
    }
}
