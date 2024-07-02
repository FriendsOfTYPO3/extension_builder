<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Tests\Unit\Service;

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
    protected ExtensionSchemaBuilder $extensionSchemaBuilder;
    protected string $extensionKey = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = $this->createMock(Extension::class);

        $this->extensionSchemaBuilder = $this->getAccessibleMock(ExtensionSchemaBuilder::class, ['dummy']);
        $this->extensionSchemaBuilder->injectConfigurationManager(new ExtensionBuilderConfigurationManager());

        $objectSchemaBuilder = $this->getAccessibleMock(ObjectSchemaBuilder::class, ['dummy']);
        $objectSchemaBuilder->injectConfigurationManager(new ExtensionBuilderConfigurationManager());

        $this->extensionSchemaBuilder->injectObjectSchemaBuilder($objectSchemaBuilder);
        $this->extensionKey = 'dummy';
    }

    /**
     * @test
     */
    public function conversionExtractsExtensionProperties(): void
    {
        $description = 'My cool fancy description';
        $name = 'ExtName';
        $extensionKey = $this->extensionKey;
        $version = '1.0.4';
        $vendor = 'Vendor';

        $input = [
            'properties' => [
                'description' => $description,
                'extensionKey' => $extensionKey,
                'name' => $name,
                'vendorName' => $vendor,
                'emConf' => [
                    'state' => 'alpha',
                    'version' => $version
                ]
            ],
            'storagePath' => 'tmp'
        ];

        $extension = new Extension();
        $extension->setDescription($description);
        $extension->setName($name);
        $extension->setExtensionKey($extensionKey);
        $extension->setState(Extension::STATE_ALPHA);
        $extension->setVendorName($vendor);
        $extension->setVersion($version);
        $extension->setExtensionDir('');
        $extension->setStoragePath('tmp');

        $actual = $this->extensionSchemaBuilder->build($input);
        self::assertEquals($extension, $actual, 'Extension properties were not extracted.');
    }

    /**
     * @test
     */
    public function conversionExtractsPersons(): void
    {
        $persons = [];
        $persons[] = (new Person())
            ->setName('name0')
            ->setRole('role0')
            ->setEmail('email0')
            ->setCompany('company0');

        $persons[] = (new Person())
            ->setName('name1')
            ->setRole('role1')
            ->setEmail('email1')
            ->setCompany('company1');

        $input = [
            'properties' => [
                'description' => 'myDescription',
                'extensionKey' => 'myExtensionKey',
                'name' => 'myName',
                'vendorName' => 'Vendor',
                'emConf' => [
                    'state' => 'beta',
                    'version' => ''
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
            ],
            'storagePath' => 'tmp'
        ];
        $extension = $this->extensionSchemaBuilder->build($input);
        self::assertEquals($persons, $extension->getPersons(), 'Persons set wrong in ObjectBuilder.');
    }

    /**
     * @test
     */
    public function conversionExtractsWholeExtensionMetadataWithRelations(): void
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
                                    'excludeField' => 1
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
                                    'excludeField' => 1
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
                'vendorName' => 'Vendor',
                'emConf' => [
                    'state' => 'beta',
                    'version' => '1.0.0',
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
            ],
            'storagePath' => 'tmp'
        ];

        $extension = new Extension();
        $extension->setName('My ext name');
        $extension->setState(Extension::STATE_BETA);
        $extension->setVersion('1.0.0');
        $extension->setExtensionKey($this->extensionKey);
        $extension->setDescription('Some description');
        $extension->setExtensionDir('');
        $extension->setStoragePath('tmp');
        $extension->setVendorName('Vendor');

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
        $relation->setExcludeField(true);
        $blog->addProperty($relation);

        $relation = new ZeroToManyRelation('comments');
        $relation->setForeignModel($comment);
        $relation->setExcludeField(true);
        $post->addProperty($relation);

        $actualExtension = $this->extensionSchemaBuilder->build($input);
        self::assertEquals(
            $extension->getDomainObjects(),
            $actualExtension->getDomainObjects(),
            'The extensions differ'
        );
    }
}
