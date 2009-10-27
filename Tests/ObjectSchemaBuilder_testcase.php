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

require_once('BaseTestCase.php');
class Tx_ExtbaseKickstarter_ObjectSchemaBuilder_testcase extends Tx_ExtbaseKickstarter_BaseTestCase { //extends Tx_Extbase_Base_testcase {

	protected $objectSchemaBuilder;

	public function setUp() {
		$this->objectSchemaBuilder = $this->getMock($this->buildAccessibleProxy('Tx_ExtbaseKickstarter_ObjectSchemaBuilder'), array('dummy'));
	}
	/**
	 * @test
	 */
	public function conversionExtractsExtensionProperties() {

		$description = 'My cool fancy description';
		$name = 'ExtName';
		$extensionKey = 'EXTKEY';
		$state = 'beta';

		$input = array(
			'properties' => array(
				'description' => $description,
				'extensionKey' => $extensionKey,
				'name' => $name,
				'state' => $state
				)
		    );

		$extension = new Tx_ExtbaseKickstarter_Domain_Model_Extension();
		$extension->setDescription($description);
		$extension->setName($name);
		$extension->setExtensionKey($extensionKey);
		$extension->setState(2);

		$actual = $this->objectSchemaBuilder->build($input);
		$this->assertEquals($actual, $extension, 'Extension properties were not extracted.');
	}

	/**
	 * @test
	 */
	public function conversionExtractsPersons() {
		$this->markTestIncomplete('Persons not supported');

	}


	/**
	 * @test
	 */
	public function conversionExtractsSingleDomainObjectMetadata() {
		$name = 'MyDomainObject';
		$description = 'My long domain object description';

		$input = array(
			'name' => $name,
			'objectsettings' => array(
				'description' => $description,
				'aggregateRoot' => TRUE,
				'type' => 'Entity'
			),
			'propertyGroup' => array(
				'properties' => array(
					0 => array(
						'propertyName' => 'name',
						'propertyType' => 'String',
						'propertyIsRequired' => 'true'
					),
					1 => array(
						'propertyName' => 'type',
						'propertyType' => 'Integer'
					)
				)
			    ),
			'relationGroup' => array()
		    );
		$expected = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject();
		$expected->setName($name);
		$expected->setDescription($description);
		$expected->setEntity(TRUE);
		$expected->setAggregateRoot(TRUE);

		$property0 = new Tx_ExtbaseKickstarter_Domain_Model_Property_StringProperty();
		$property0->setName('name');
		$property0->setRequired(TRUE);
		$property1 = new Tx_ExtbaseKickstarter_Domain_Model_Property_IntegerProperty();
		$property1->setName('type');
		$expected->addProperty($property0);
		$expected->addProperty($property1);

		$actual = $this->objectSchemaBuilder->_call('buildDomainObject', $input);
		$this->assertEquals($actual, $expected, 'Domain Object not built correctly.');
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
					'value' =>  array(
						'name' => 'Blog',
						'objectsettings' => array(
							'description' => 'A blog object',
							'aggregateRoot' => TRUE,
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
									'relationType' => 'zeroToOne'
								)
							)
						)
					)
				),
				1 => array(
					// config
					// name
					'value' =>  array(
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
									'relationType' => 'zeroToMany'
								)
							)
						)
					)
				),
				2 => array(
					// config
					// name
					'value' =>  array(
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
				'extensionKey' => 'my_extension_key',
				'name' => 'My ext name',
				'state' => 'beta',

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

		$extension = new Tx_ExtbaseKickstarter_Domain_Model_Extension();
		$extension->setName('My ext name');
		$extension->setState(Tx_ExtbaseKickstarter_Domain_Model_Extension::STATE_BETA);
		$extension->setExtensionKey('my_extension_key');
		$extension->setDescription('Some description');

		$blog = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject();
		$blog->setName('Blog');
		$blog->setDescription('A blog object');
		$blog->setEntity(TRUE);
		$blog->setAggregateRoot(TRUE);
		$property = new Tx_ExtbaseKickstarter_Domain_Model_Property_StringProperty();
		$property->setName('name');
		$blog->addProperty($property);
		$property = new Tx_ExtbaseKickstarter_Domain_Model_Property_StringProperty();
		$property->setName('description');
		$blog->addProperty($property);

		$extension->addDomainObject($blog);


		$post = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject();
		$post->setName('Post');
		$post->setDescription('A blog post');
		$post->setEntity(TRUE);
		$post->setAggregateRoot(FALSE);
		$extension->addDomainObject($post);

		$comment = new Tx_ExtbaseKickstarter_Domain_Model_DomainObject();
		$comment->setName('Comment');
		$comment->setDescription('');
		$comment->setEntity(TRUE);
		$comment->setAggregateRoot(FALSE);
		$extension->addDomainObject($comment);

		$relation = new Tx_ExtbaseKickstarter_Domain_Model_Property_Relation_ZeroToOneRelation();
		$relation->setName('posts');
		$relation->setForeignClass($post);
		$blog->addProperty($relation);

		$relation = new Tx_ExtbaseKickstarter_Domain_Model_Property_Relation_ZeroToManyRelation();
		$relation->setName('comments');
		$relation->setForeignClass($comment);
		$post->addProperty($relation);

		$actualExtension = $this->objectSchemaBuilder->build($input);
		$this->assertEquals($extension, $actualExtension, 'The extensions differ');

	}
}