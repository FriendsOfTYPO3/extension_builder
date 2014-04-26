<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


class ExtensionSchemaBuilderTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * @var \EBT\ExtensionBuilder\Service;\ExtensionSchemaBuilder
	 */
	protected $extensionSchemaBuilder = NULL;

	/**
	 * @var string
	 */
	protected $extensionKey = '';

	protected function setUp() {
		$this->extension = $this->getMock('EBT\ExtensionBuilder\Domain\Model\Extension', array('getOverWriteSettings'));
		$this->extensionSchemaBuilder = $this->getMock($this->buildAccessibleProxy('\EBT\ExtensionBuilder\Service\ExtensionSchemaBuilder'), array('dummy'));
		$this->extensionSchemaBuilder->injectConfigurationManager(new \EBT\ExtensionBuilder\Configuration\ConfigurationManager());
		/** @var $objectSchemaBuilder \EBT\ExtensionBuilder\Service\ObjectSchemaBuilder */
		$objectSchemaBuilder = $this->getMock($this->buildAccessibleProxy('EBT\\ExtensionBuilder\\Service\\ObjectSchemaBuilder'), array('dummy'));
		$objectSchemaBuilder->injectConfigurationManager(new \EBT\ExtensionBuilder\Configuration\ConfigurationManager());
		$this->extensionSchemaBuilder->injectObjectSchemaBuilder($objectSchemaBuilder);
		$this->extensionKey = 'dummy';
	}

	/**
	 * @test
	 */
	public function conversionExtractsExtensionProperties() {

		$description = 'My cool fancy description';
		$name = 'ExtName';
		$extensionKey = $this->extensionKey;
		$state = 0;
		$version = '1.0.4';

		$input = array(
			'properties' => array(
				'description' => $description,
				'extensionKey' => $extensionKey,
				'name' => $name,
				'emConf' => array(
					'state' => $state,
					'version' => $version
				)
			)
		);

		$extension = new \EBT\ExtensionBuilder\Domain\Model\Extension();
		$extension->setDescription($description);
		$extension->setName($name);
		$extension->setExtensionKey($extensionKey);
		$extension->setState($state);
		$extension->setVersion($version);
		$extension->setExtensionDir('');

		$actual = $this->extensionSchemaBuilder->build($input);
		$this->assertEquals($extension, $actual, 'Extension properties were not extracted.');
	}

	/**
	 * @test
	 */
	public function conversionExtractsPersons() {
		$persons = array();
		$persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\Person');
		$persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('EBT\\ExtensionBuilder\\Domain\\Model\\Person');
		$persons[0]->setName('name0');
		$persons[0]->setRole('role0');
		$persons[0]->setEmail('email0');
		$persons[0]->setCompany('company0');
		$persons[1]->setName('name1');
		$persons[1]->setRole('role1');
		$persons[1]->setEmail('email1');
		$persons[1]->setCompany('company1');

		$input = array(
			'properties' => array(
				'description' => 'myDescription',
				'extensionKey' => 'myExtensionKey',
				'name' => 'myName',
				'emConf' => array(
					'state' => 'beta'
				),
				'persons' => array(
					array(
						'company' => 'company0',
						'email' => 'email0',
						'name' => 'name0',
						'role' => 'role0'
					),
					array(
						'company' => 'company1',
						'email' => 'email1',
						'name' => 'name1',
						'role' => 'role1'
					),
				),
				'state' => 'beta'
			)
		);
		$extension = $this->extensionSchemaBuilder->build($input);
		$this->assertEquals($extension->getPersons(), $persons, 'Persons set wrong in ObjectBuilder.');
	}


	/**
	 * @test
	 */
	public function conversionExtractsWholeExtensionMetadataWithRelations() {
		$input = array(
			'modules' => array(
				0 => array(
					// config
					// name
					'value' => array(
						'name' => 'Blog',
						'objectsettings' => array(
							'description' => 'A blog object',
							'aggregateRoot' => FALSE,
							'type' => 'Entity'
						),
						'propertyGroup' => array(
							'properties' => array(
								0 => array(
									'propertyName' => 'name',
									'propertyType' => 'String'
								),
								1 => array(
									'propertyName' => 'description',
									'propertyType' => 'String'
								)
							)
						),
						'relationGroup' => array(
							'relations' => array(
								0 => array(
									'relationName' => 'posts',
									'relationType' => 'zeroToMany',
									'propertyIsExcludeField' => 1
								)
							)
						)
					)
				),
				1 => array(
					// config
					// name
					'value' => array(
						'name' => 'Post',
						'objectsettings' => array(
							'description' => 'A blog post',
							'aggregateRoot' => FALSE,
							'type' => 'Entity'
						),
						'propertyGroup' => array(
							'properties' => array(
							)
						),
						'relationGroup' => array(
							'relations' => array(
								0 => array(
									'relationName' => 'comments',
									'relationType' => 'zeroToMany',
									'propertyIsExcludeField' => 1
								)
							)
						)
					)
				),
				2 => array(
					// config
					// name
					'value' => array(
						'name' => 'Comment',
						'objectsettings' => array(
							'description' => '',
							'aggregateRoot' => FALSE,
							'type' => 'Entity'
						),
						'propertyGroup' => array(
							'properties' => array(
							)
						),
						'relationGroup' => array(
							'relations' => array()
						)
					)
				),
			),
			'properties' => array(
				'description' => 'Some description',
				'extensionKey' => $this->extensionKey,
				'name' => 'My ext name',
				'emConf' => array(
					'state' => 'beta'
				)
			),
			'wires' => array(
				0 => array(
					'tgt' => array(
						'moduleId' => 1,
						'terminal' => 'SOURCES'
					),
					'src' => array(
						'moduleId' => 0, // hier stand leerstring drin
						'terminal' => 'relationWire_0'
					)
				),
				1 => array(
					'tgt' => array(
						'moduleId' => 2,
						'terminal' => 'SOURCES'
					),
					'src' => array(
						'moduleId' => 1,
						'terminal' => 'relationWire_0'
					)
				)
			)
		);

		$extension = new \EBT\ExtensionBuilder\Domain\Model\Extension();
		$extension->setName('My ext name');
		$extension->setState(\EBT\ExtensionBuilder\Domain\Model\Extension::STATE_BETA);
		$extension->setExtensionKey($this->extensionKey);
		$extension->setDescription('Some description');
		$extension->setExtensionDir('');

		$blog = new \EBT\ExtensionBuilder\Domain\Model\DomainObject();
		$blog->setName('Blog');
		$blog->setDescription('A blog object');
		$blog->setEntity(TRUE);
		$blog->setAggregateRoot(FALSE);
		$property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
		$blog->addProperty($property);
		$property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('description');
		$blog->addProperty($property);

		$extension->addDomainObject($blog);

		$post = new \EBT\ExtensionBuilder\Domain\Model\DomainObject();
		$post->setName('Post');
		$post->setDescription('A blog post');
		$post->setEntity(TRUE);
		$post->setAggregateRoot(FALSE);
		$extension->addDomainObject($post);

		$comment = new \EBT\ExtensionBuilder\Domain\Model\DomainObject();
		$comment->setName('Comment');
		$comment->setDescription('');
		$comment->setEntity(TRUE);
		$comment->setAggregateRoot(FALSE);
		$extension->addDomainObject($comment);

		$relation = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation('posts');
		$relation->setForeignModel($post);
		$relation->setExcludeField(1);
		$blog->addProperty($relation);

		$relation = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ZeroToManyRelation('comments');
		$relation->setForeignModel($comment);
		$relation->setExcludeField(1);
		$post->addProperty($relation);
		$actualExtension = $this->extensionSchemaBuilder->build($input);
		$this->assertEquals($extension->getDomainObjects(), $actualExtension->getDomainObjects(), 'The extensions differ');
	}
}
