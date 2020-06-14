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

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use PhpParser\Lexer;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

class ClassParserTest extends BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Service\ParserService
     */
    protected $parserService;
    /**
     * set to true to see an overview of the parsed class objects in the backend
     *
     * @var bool
     */
    protected $debugMode = false;

    protected function setUp()
    {
        parent::setUp();
        $this->parserService = new ParserService();
    }

    /**
     * Parse a basic class from a file
     * @test
     */
    public function parseBasicClass(): void
    {
        $file = $this->fixturesPath . 'ClassParser/BasicClass.php';
        $this->parseClass($file, 'Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicClass');
    }

    /**
     * Parse a basic class from a file
     * @test
     */
    public function parseBasicNameSpacedClass(): void
    {
        $file = $this->fixturesPath . 'ClassParser/BasicNameSpacedClass.php';
        $this->parseClass($file, '\\Foo\\Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicNameSpacedClass');
    }

    /**
     * Parse a complex class from a file
     * @test
     */
    public function parseComplexClass(): void
    {
        $file = $this->fixturesPath . 'ClassParser/ComplexClass.php';
        $classObject = $this->parseClass($file, 'Tx_ExtensionBuilder_Tests_Examples_ClassParser_ComplexClass');
        $getters = $classObject->getGetters();
        self::assertCount(1, $getters);

        $firstGetter = array_pop($getters);
        self::assertEquals('getName', $firstGetter->getName());

        $params2 = $classObject->getMethod('methodWithVariousParameter')->getParameters();
        self::assertCount(
            4,
            $params2,
            'Wrong parameter count in parsed "methodWithVariousParameter"'
        );
        self::assertEquals(
            'param4',
            $params2[3]->getName(),
            'Last parameter name was not correctly parsed'
        );
    }

    /**
     * Parse a with interfaces
     *
     */
    public function parseClassWithInterfaces(): void
    {
        $file = $this->fixturesPath . 'ClassParser/ClassWithInterfaces.php';
        $classObject = $this->parseClass($file, 'Tx_ExtensionBuilder_Tests_Examples_ClassParser_ClassWithInterfaces');
        self::assertEquals(
            [
                'PHPUnit_Framework_IncompleteTest',
                'PHPUnit_Framework_MockObject_Stub',
                'PHPUnit_Framework_SelfDescribing'
            ],
            $classObject->getInterfaceNames()
        );
    }

    /**
     * Parse a with interfaces
     *
     */
    public function parseClassWithAliasDeclarations(): void
    {
        $file = $this->fixturesPath . 'ClassParser/ClassWithAlias.php';
        $classObject = $this->parseClass($file, 'Tx_ExtensionBuilder_Tests_Examples_ClassParser_ClassWithAlias');
        self::assertEquals(
            [
                'TYPO3\\CMS\\Core\\Utility\\GeneralUtility',
                'TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager as Config'
            ],
            $classObject->getAliasDeclarations()
        );
    }

    /**
     * Parse a complex class from a file
     *
     */
    public function parseAnotherComplexClass(): void
    {
        $file = $this->fixturesPath . 'ClassParser/AnotherComplexClass.php';
        $this->parseClass($file, 'Tx_ExtensionBuilder_Tests_Examples_ClassParser_AnotherComplexClass');
    }

    /**
     * Parse a big class from a file
     *
     */
    public function parseGeneralUtitliy(): void
    {
        $this->parseClass(
            Environment::getPublicPath() . '/typo3/sysext/core/Classes/Utility/GeneralUtility.php',
            '\\TYPO3\\CMS\\Core\\Utility\\GeneralUtility'
        );
    }

    /**
     * Parse a file and compare the resulting
     * ClassObject with the reflection class object
     *
     * @param $file
     * @param $className
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \ReflectionException
     */
    protected function parseClass($file, $className): ClassObject
    {
        $classObject = $this->parserService->parseFile($file)->getFirstClass();
        self::assertInstanceOf(ClassObject::class, $classObject);
        require_once($file);

        $classReflectionService = new ReflectionService();
        $classSchema = $classReflectionService->getClassSchema($className);
        $this->parserFindsAllConstants($classObject, new \ReflectionClass($className));
        $this->parserFindsAllMethods($classObject, $classSchema);
        $this->parserFindsAllProperties($classObject, $classSchema);
        return $classObject;
    }

    /**
     * compares the number of methods found by parsing with those
     * retrieved from the reflection class
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
     * @param \TYPO3\CMS\Extbase\Reflection\ClassSchema $classReflection
     * @return void
     */
    public function parserFindsAllConstants($classObject, $classReflection): void
    {
        $reflectionConstantCount = count($classReflection->getConstants());
        $classObjectConstantCount = count($classObject->getConstants());
        self::assertEquals(
            $reflectionConstantCount,
            $classObjectConstantCount,
            'Not all constants were found: ' . $classObject->getName() . serialize($classReflection->getConstants())
        );
    }

    /**
     * compares the number of methods found by parsing
     * with those retrieved from the reflection class
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
     * @param \TYPO3\CMS\Extbase\Reflection\ClassSchema $classReflection
     * @return void
     */
    public function parserFindsAllMethods($classObject, $classReflection): void
    {
        $reflectionMethodCount = count($classReflection->getMethods());
        $classObjectMethodCount = count($classObject->getMethods());
        self::assertEquals(
            $classObjectMethodCount,
            $reflectionMethodCount,
            'Not all methods were found!: ' . $reflectionMethodCount
        );
    }

    /**
     * compares the number of properties found by parsing
     * with those retrieved from the reflection class
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject
     * @param \TYPO3\CMS\Extbase\Reflection\ClassSchema $classReflection
     * @return void
     */
    public function parserFindsAllProperties($classObject, $classReflection): void
    {
        $reflectionPropertyCount = count($classReflection->getProperties());
        $classObjectPropertyCount = count($classObject->getProperties());
        self::assertEquals(
            $classObjectPropertyCount,
            $reflectionPropertyCount,
            'Not all properties were found!'
        );
    }
}
