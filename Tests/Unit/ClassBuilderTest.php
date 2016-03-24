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

use EBT\ExtensionBuilder\Utility\Inflector;

class ClassBuilderTest extends \EBT\ExtensionBuilder\Tests\BaseUnitTest
{
    /**
     * @var string
     */
    protected $modelName = 'Model1';
    /**
     * @var \EBT\ExtensionBuilder\Service\ClassBuilder
     */
    protected $classBuilder = null;
    /**
     * @var string
     */
    protected $modelClassTemplatePath = '';

    protected function setUp()
    {
        parent::setUp();

        $this->classBuilder = $this->getAccessibleMock(\EBT\ExtensionBuilder\Service\ClassBuilder::class, array('dummy'));
        $parserService = new \EBT\ExtensionBuilder\Service\Parser(new \PhpParser\Lexer());
        $printerService = $this->getAccessibleMock(\EBT\ExtensionBuilder\Service\Printer::class, array('dummy'));
        $nodeFactory = new \EBT\ExtensionBuilder\Parser\NodeFactory();
        $printerService->_set('nodeFactory', $nodeFactory);
        $configurationManager = new \EBT\ExtensionBuilder\Configuration\ConfigurationManager();
        $this->classBuilder->_set('parserService', $parserService);
        $this->classBuilder->_set('printerService', $printerService);
        $this->classBuilder->_set('configurationManager', $configurationManager);
        $this->classBuilder->initialize($this->extension);
    }

    /**
     * @test
     */
    public function classBuilderGeneratesSetterMethodForSimpleProperty()
    {
        $domainObject = $this->buildDomainObject($this->modelName, true, true);

        $property0 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
        $domainObject->addProperty($property0);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, false)->getFirstClass();

        self::assertTrue(is_object($modelClassObject), 'No model class object');
        self::assertTrue($modelClassObject->methodExists('setName'), 'No method: setName');

        $setNameMethod = $modelClassObject->getMethod('setName');
        $parameters = $setNameMethod->getParameters();
        self::assertEquals(count($parameters), 1);
        $firstParameter = array_shift($parameters);
        self::assertEquals($firstParameter->getName(), 'name');
    }

    /**
     * @test
     */
    public function classBuilderGeneratesGetterMethodForSimpleProperty()
    {
        $domainObject = $this->buildDomainObject($this->modelName, true, true);
        $property0 = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\StringProperty('name');
        $property0->setRequired(true);
        $domainObject->addProperty($property0);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, false)->getFirstClass();
        self::assertTrue($modelClassObject->methodExists('getName'), 'No method: getName');
    }

    /**
     * @test
     */
    public function classBuilderGeneratesIsMethodForBooleanProperty()
    {
        $domainObject = $this->buildDomainObject($this->modelName, true, true);

        $property = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\BooleanProperty('blue');
        $property->setRequired(true);
        $domainObject->addProperty($property);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, false)->getFirstClass();
        self::assertTrue($modelClassObject->methodExists('isBlue'), 'No method: isBlue');
    }

    /**
     * @test
     */
    public function classBuilderGeneratesMethodsForRelationProperty()
    {
        $modelName2 = 'Model2';
        $propertyName = 'relNames';

        $domainObject1 = $this->buildDomainObject($this->modelName, true, true);
        $relatedDomainObject = $this->buildDomainObject($modelName2);

        $relationProperty = new \EBT\ExtensionBuilder\Domain\Model\DomainObject\Relation\ManyToManyRelation($propertyName);
        $relationProperty->setForeignModel($relatedDomainObject);
        $domainObject1->addProperty($relationProperty);

        $modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject1, $this->modelClassTemplatePath, false)->getFirstClass();

        self::assertTrue($modelClassObject->methodExists('add' . ucfirst(Inflector::singularize($propertyName))), 'Add method was not generated');
        self::assertTrue($modelClassObject->methodExists('remove' . ucfirst(Inflector::singularize($propertyName))), 'Remove method was not generated');
        self::assertTrue($modelClassObject->methodExists('set' . ucfirst($propertyName)), 'Setter was not generated');
        self::assertTrue($modelClassObject->methodExists('set' . ucfirst($propertyName)), 'Setter was not generated');

        $addMethod = $modelClassObject->getMethod('add' . ucfirst(Inflector::singularize($propertyName)));
        self::assertTrue($addMethod->isTaggedWith('param'), 'No param tag set for setter method');
        $paramTagValues = $addMethod->getTagValues('param');
        self::assertTrue((strpos($paramTagValues, $relatedDomainObject->getFullQualifiedClassName()) === 0), 'Wrong param tag:' . $paramTagValues);

        $parameters = $addMethod->getParameters();
        self::assertTrue((count($parameters) == 1), 'Wrong parameter count in add method');
        $parameter = current($parameters);
        self::assertTrue(($parameter->getName() == Inflector::singularize($propertyName)), 'Wrong parameter name in add method');
        self::assertTrue(($parameter->getTypeHint() == $relatedDomainObject->getFullQualifiedClassName()), 'Wrong type hint for add method parameter:' . $parameter->getTypeHint());
    }

    public function propertyDefaultTypesProviderTypes()
    {
        return array(
            'boolean' => array('boolean', false),
            'Date' => array('date', null),
            'DateTime' => array('dateTime', null),
            'file' => array('file', null),
            'float' => array('float', 0.0),
            'image' => array('image', null),
            'integer' => array('integer', 0),
            'nativeDate' => array('nativeDate', null),
            'nativeDateTime' => array('nativeDateTime', null),
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
    public function classBuilderGeneratesPropertyDefault($propertyName, $propertyDefaultValue)
    {
        $domainObject = $this->buildDomainObject($this->modelName, true, true);
        $propertyClassName = '\\EBT\\ExtensionBuilder\\Domain\\Model\\DomainObject\\' . ucfirst($propertyName) . 'Property';
        $property = new $propertyClassName($propertyName);
        $domainObject->addProperty($property);

        /** @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $modelClassObject */
        $modelClassObject = $this->classBuilder->generateModelClassFileObject($domainObject, $this->modelClassTemplatePath, false)->getFirstClass();

        $propertyObject = $modelClassObject->getProperty($propertyName);
        self::assertSame($propertyDefaultValue, $propertyObject->getDefault());
    }
}
