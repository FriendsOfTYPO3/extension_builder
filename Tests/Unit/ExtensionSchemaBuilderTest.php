<?php
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


class Tx_ExtensionBuilder_ExtensionSchemaBuilderTest extends Tx_ExtensionBuilder_Tests_BaseTest {

	protected $extensionSchemaBuilder;

	public function setUp() {
		//parent::setUp();
		$this->extension = $this->getMock('Tx_ExtensionBuilder_Domain_Model_Extension', array('getOverWriteSettings'));
		$this->extensionSchemaBuilder = $this->getMock($this->buildAccessibleProxy('Tx_ExtensionBuilder_Service_ExtensionSchemaBuilder'), array('dummy'));
		$this->extensionSchemaBuilder->injectConfigurationManager(new Tx_ExtensionBuilder_Configuration_ConfigurationManager());
		$objectSchemaBuilder = $this->getMock($this->buildAccessibleProxy('Tx_ExtensionBuilder_Service_ObjectSchemaBuilder'), array('dummy'));
		$objectSchemaBuilder->injectConfigurationManager(new Tx_ExtensionBuilder_Configuration_ConfigurationManager());
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

		$extension = new Tx_ExtensionBuilder_Domain_Model_Extension();
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
		$persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_Person');
		$persons[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_ExtensionBuilder_Domain_Model_Person');
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

		$extension = new Tx_ExtensionBuilder_Domain_Model_Extension();
		$extension->setName('My ext name');
		$extension->setState(Tx_ExtensionBuilder_Domain_Model_Extension::STATE_BETA);
		$extension->setExtensionKey($this->extensionKey);
		$extension->setDescription('Some description');
		$extension->setExtensionDir('');

		$blog = new Tx_ExtensionBuilder_Domain_Model_DomainObject();
		$blog->setName('Blog');
		$blog->setDescription('A blog object');
		$blog->setEntity(TRUE);
		$blog->setAggregateRoot(FALSE);
		$property = new Tx_ExtensionBuilder_Domain_Model_DomainObject_StringProperty('name');
		$blog->addProperty($property);
		$property = new Tx_ExtensionBuilder_Domain_Model_DomainObject_StringProperty('description');
		$blog->addProperty($property);

		$extension->addDomainObject($blog);

		$post = new Tx_ExtensionBuilder_Domain_Model_DomainObject();
		$post->setName('Post');
		$post->setDescription('A blog post');
		$post->setEntity(TRUE);
		$post->setAggregateRoot(FALSE);
		$extension->addDomainObject($post);

		$comment = new Tx_ExtensionBuilder_Domain_Model_DomainObject();
		$comment->setName('Comment');
		$comment->setDescription('');
		$comment->setEntity(TRUE);
		$comment->setAggregateRoot(FALSE);
		$extension->addDomainObject($comment);

		$relation = new Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ZeroToManyRelation('posts');
		$relation->setForeignModel($post);
		$relation->setExcludeField(1);
		$blog->addProperty($relation);

		$relation = new Tx_ExtensionBuilder_Domain_Model_DomainObject_Relation_ZeroToManyRelation('comments');
		$relation->setForeignModel($comment);
		$relation->setExcludeField(1);
		$post->addProperty($relation);
		$actualExtension = $this->extensionSchemaBuilder->build($input);
		$this->assertEquals($extension->getDomainObjects(), $actualExtension->getDomainObjects(), 'The extensions differ');
	}
}

?>
