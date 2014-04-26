<?php
namespace EBT\ExtensionBuilder\Tests\Unit;
/***************************************************************
 *  Copyright notice
 *
 * (c) 2010 Nico de Haen
 * All rights reserved
 *
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
use EBT\ExtensionBuilder\Utility\Inflector;


/**
 *
 * @author ndh
 *
 */
class ClassBuilderTest extends \EBT\ExtensionBuilder\Tests\BaseTest {
	/**
	 * @var string
	 */
	protected $modelName = 'Model1';

	/**
	 * @var string
	 */
	protected $modelClassTemplatePath = '';

	protected function setUp() {
		parent::setUp();
		$this->generateInitialModelClassFile($this->modelName);
	}

	protected function tearDown() {
		$this->removeInitialModelClassFile($this->modelName);
	}

	/**
	 * @test
	 */
	public function classBuilderGeneratesSetterMethodForSimpleProperty() {
		$domainObject = $this->buildDomainObject($this->modelName, true, true);

		$property0 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
		$domainObject->addProperty($property0);

		$modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, FALSE)->getFirstClass();

		$this->assertTrue(is_object($modelClassObject), 'No model class object');
		$this->assertTrue($modelClassObject->methodExists('setName'), 'No method: setName');

		$setNameMethod = $modelClassObject->getMethod('setName');
		$parameters = $setNameMethod->getParameters();
		$this->assertEquals(count($parameters), 1);
		$firstParameter = array_shift($parameters);
		$this->assertEquals($firstParameter->getName(), 'name');
	}


	/**
	 * @test
	 */

	public function classBuilderGeneratesGetterMethodForSimpleProperty() {

		$domainObject = $this->buildDomainObject($this->modelName, true, true);
		$property0 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
		$property0->setRequired(TRUE);
		$domainObject->addProperty($property0);

		$modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, FALSE)->getFirstClass();
		$this->assertTrue($modelClassObject->methodExists('getName'), 'No method: getName');

	}

	/**
	 * @test
	 */
	public function classBuilderGeneratesIsMethodForBooleanProperty() {

		$domainObject = $this->buildDomainObject($this->modelName, true, true);

		$property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty('blue');
		$property->setRequired(TRUE);
		$domainObject->addProperty($property);

		$modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, FALSE)->getFirstClass();
		$this->assertTrue($modelClassObject->methodExists('isBlue'), 'No method: isBlue');

	}

	/**
	 * @test
	 */
	public function classBuilderGeneratesMethodsForRelationProperty() {
		$modelName2 = 'Model2';
		$propertyName = 'relNames';

		$domainObject1 = $this->buildDomainObject($this->modelName, true, true);
		$relatedDomainObject = $this->buildDomainObject($modelName2);

		$relationProperty = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ManyToManyRelation($propertyName);
		$relationProperty->setForeignModel($relatedDomainObject);
		$domainObject1->addProperty($relationProperty);

		$modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject1, $this->modelClassTemplatePath, FALSE)->getFirstClass();

		$this->assertTrue($modelClassObject->methodExists('add' . ucfirst(Inflector::singularize($propertyName))), 'Add method was not generated');
		$this->assertTrue($modelClassObject->methodExists('remove' . ucfirst(Inflector::singularize($propertyName))), 'Remove method was not generated');
		$this->assertTrue($modelClassObject->methodExists('set' . ucfirst($propertyName)), 'Setter was not generated');
		$this->assertTrue($modelClassObject->methodExists('set' . ucfirst($propertyName)), 'Setter was not generated');

		$addMethod = $modelClassObject->getMethod('add' . ucfirst(Inflector::singularize($propertyName)));
		$this->assertTrue($addMethod->isTaggedWith('param'), 'No param tag set for setter method');
		$paramTagValues = $addMethod->getTagValues('param');
		$this->assertTrue((strpos($paramTagValues, $relatedDomainObject->getFullQualifiedClassName()) === 0), 'Wrong param tag:' . $paramTagValues);

		$parameters = $addMethod->getParameters();
		$this->assertTrue((count($parameters) == 1), 'Wrong parameter count in add method');
		$parameter = current($parameters);
		$this->assertTrue(($parameter->getName() == Inflector::singularize($propertyName)), 'Wrong parameter name in add method');
		$this->assertTrue(($parameter->getTypeHint() == $relatedDomainObject->getFullQualifiedClassName()), 'Wrong type hint for add method parameter:' . $parameter->getTypeHint());

	}

	public function propertyDefaultTypesProviderTypes() {
		return array(
			'boolean' => array('boolean', FALSE),
			'Date' => array('date', NULL),
			'DateTime' => array('dateTime', NULL),
			'file' => array('file', NULL),
			'float' => array('float', 0.0),
			'image' => array('image', NULL),
			'integer' => array('integer', 0),
			'nativeDate' => array('nativeDate', NULL),
			'nativeDateTime' => array('nativeDateTime', NULL),
			'password' => array('password', ''),
			'richText' => array('richText', ''),
			'select' => array('select', 0),
			'string' => array('string', ''),
			'text' => array('text', ''),
			'time' => array('time', 0),
			'timeSec' => array('timeSec', 0),
		);
	}

	/**
	 * @test
	 * @dataProvider propertyDefaultTypesProviderTypes
	 */
	public function classBuilderGeneratesPropertyDefault($propertyName, $propertyDefaultValue) {
		$domainObject = $this->buildDomainObject($this->modelName, TRUE, TRUE);
		$propertyClassName = '\\EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\' . ucfirst($propertyName) . 'Property';
		$property = new $propertyClassName($propertyName);
		$domainObject->addProperty($property);

		/** @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $modelClassObject */
		$modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, FALSE)->getFirstClass();

		$propertyObject = $modelClassObject->getProperty($propertyName);
		$this->assertSame($propertyDefaultValue, $propertyObject->getDefault());
	}

}
