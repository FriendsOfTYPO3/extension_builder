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

use org\bovigo\vfs\vfsStream;

class PrinterTest extends \EBT\ExtensionBuilder\Tests\BaseUnitTest
{
    /**
     * @var \EBT\ExtensionBuilder\Service\Parser
     */
    protected $parserService = null;
    /**
     * @var \EBT\ExtensionBuilder\Service\Printer
     */
    protected $printerService = null;
    /**
     * @var string
     */
    protected $tmpDir = '';

    protected function setUp()
    {
        parent::setUp();
        $this->fixturesPath = PATH_typo3conf . 'ext/extension_builder/Tests/Fixtures/ClassParser/';
        vfsStream::setup('tmpDir');
        $this->tmpDir = vfsStream::url('tmpDir') . '/';
        $this->printerService = $this->getAccessibleMock(\EBT\ExtensionBuilder\Service\Printer::class, array('dummy'));
        $nodeFactory = new \EBT\ExtensionBuilder\Parser\NodeFactory();
        $this->printerService->_set('nodeFactory', $nodeFactory);
        $this->parserService = new \EBT\ExtensionBuilder\Service\Parser(new \PhpParser\Lexer());
    }

    /**
     * @test
     */
    public function printSimplePropertyClass()
    {
        self::assertTrue(is_writable($this->tmpDir), 'Directory not writable: ' . $this->tmpDir . '. Can\'t compare rendered files');
        $fileName = 'SimpleProperty.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printClassWithMultipleProperties()
    {
        $fileName = 'ClassWithMultipleProperties.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleClassMethodWithManyParameter()
    {
        $fileName = 'ClassMethodWithManyParameter.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleClassMethodWithMissingParameterTag()
    {
        $fileName = 'ClassMethodWithMissingParameterTag.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $reflectedClass = $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        // No way to detect the typeHint with Reflection...
    }

    /**
     * @test
     */
    public function printClassWithIncludeStatement()
    {
        $fileName = 'ClassWithIncludeStatement.php';
        self::assertTrue(copy($this->fixturesPath . 'DummyIncludeFile1.php', $this->tmpDir . 'DummyIncludeFile1.php'));
        self::assertTrue(copy($this->fixturesPath . 'DummyIncludeFile2.php', $this->tmpDir . 'DummyIncludeFile2.php'));
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printClassWithDefaultValuesInProperties()
    {
        $fileName = 'BasicClassWithDefaultValuesInProperties.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printClassWithPreStatements()
    {
        $fileName = 'ClassWithPreStatements.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        self::assertEquals(TX_PHPPARSER_TEST_FOO, 'BAR');
        self::assertEquals('FOO', TX_PHPPARSER_TEST_BAR);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     *
     */
    public function printClassWithPostStatements()
    {
        $fileName = 'ClassWithPostStatements.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        self::assertEquals(TX_PHPPARSER_TEST_FOO_POST, 'BAR');
        self::assertEquals('FOO', TX_PHPPARSER_TEST_BAR_POST);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     *
     */
    public function printClassWithPreAndPostStatements()
    {
        $fileName = 'ClassWithPreAndPostStatements.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        self::assertEquals(TX_PHPPARSER_TEST_FOO_PRE2, 'BAR');
        self::assertEquals('FOO', TX_PHPPARSER_TEST_BAR_POST2);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleNamespacedClass()
    {
        $fileName = 'SimpleNamespace.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleNamespacedClassExtendingOtherClass()
    {
        $fileName = 'SimpleNamespaceExtendingOtherClass.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        //$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleNamespaceWithUseStatement()
    {
        $fileName = 'SimpleNamespaceWithUseStatement.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printMultipleNamespacedClass()
    {
        $fileName = 'MultipleNamespaces.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        self::assertTrue(class_exists('Parser\Test\Model\MultipleNamespaces'));
        self::assertTrue(class_exists('Parser\Test\Model2\MultipleNamespaces'));
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printMultipleBracedNamespacedClass()
    {
        $fileName = 'MultipleBracedNamespaces.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        self::assertTrue(class_exists('Parser\Test\Model\MultipleBracedNamespaces'));
        self::assertTrue(class_exists('Parser\Test\Model2\MultipleBracedNamespaces'));
    }

    /**
     * @test
     */
    public function printMultiLineArray()
    {
        $fileName = 'ClassWithArrayProperty.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printsClassMethodWithMissingParameterTag()
    {
        $fileName = 'ClassMethodWithMissingParameterTag.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $tags = $classFileObject->getFirstClass()->getMethod('testMethod')->getTagValues('param');
        self::assertEquals(count($tags), 3);
        self::assertSame($tags, array('$string', 'array $arr', '\\EBT\\ExtensionBuilder\\Parser\\Utility\\NodeConverter $n'));
    }

    /**
     * @test
     */
    public function printsNamespacedClassMethodWitNamespacedParameter()
    {
        $fileName = 'ClassMethodWithManyParameter.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $testMethod = $classFileObject->getFirstClass()->getMethod('testMethod');
        $tags = $testMethod->getTagValues('param');
        self::assertEquals(count($tags), 2);
        self::assertSame(
            $tags,
            array(
                0 => '\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject',
                1 => '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TOOOL\Projects\Domain\Model\Calculation> $tests'
            )
        );
        self::assertSame(
            $testMethod->getParameterByPosition(0)->getTypeHint(),
            '\EBT\ExtensionBuilder\Domain\Model\DomainObject'
        );
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printsClassMethodWithMultilineParameter()
    {
        $fileName = 'ClassMethodWithMultilineParameter.php';
        $classFileObject = $this->parseAndWrite($fileName);
        self::assertSame(
            $classFileObject->getFirstClass()->getMethod('testMethod')->getParameterNames(),
            array(
                0 => 'number',
                1 => 'stringParam',
                2 => 'arr',
                3 => 'booleanParam',
                4 => 'float',
                5 => 'n',
            )
        );
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @param $fileName
     * @param string $subFolder
     * @return \EBT\ExtensionBuilder\Domain\Model\File
     */
    protected function parseAndWrite($fileName, $subFolder = '')
    {
        $classFilePath = $this->fixturesPath . $subFolder . $fileName;
        self::assertTrue(file_exists($classFilePath));

        $fileHandler = fopen($classFilePath, 'r');
        $classFileObject = $this->parserService->parseFile($classFilePath);
        $newClassFilePath = $this->tmpDir . $fileName;
        file_put_contents($newClassFilePath, $this->printerService->renderFileObject($classFileObject, true));
        return $classFileObject;
    }

    /**
     * includes the generated file and compares the reflection class
     * with the class object
     *
     * @param \EBT\ExtensionBuilder\Domain\Model\File $classFileObject
     * @param string $pathToGeneratedFile
     * @return \ReflectionClass
     */
    protected function compareClasses($classFileObject, $pathToGeneratedFile)
    {
        self::assertTrue(file_exists($pathToGeneratedFile), $pathToGeneratedFile . 'not exists');
        $classObject = $classFileObject->getFirstClass();
        self::assertTrue($classObject instanceof \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject);
        $className = $classObject->getQualifiedName();
        if (!class_exists($className)) {
            require_once($pathToGeneratedFile);
        }
        self::assertTrue(class_exists($className), 'Class "' . $className . '" does not exist! Tried ' . $pathToGeneratedFile);
        $reflectedClass = new \ReflectionClass($className);
        self::assertEquals(count($reflectedClass->getMethods()), count($classObject->getMethods()), 'Method count does not match');
        self::assertEquals(count($reflectedClass->getProperties()), count($classObject->getProperties()));
        self::assertEquals(count($reflectedClass->getConstants()), count($classObject->getConstants()));
        if (strlen($classObject->getNamespaceName()) > 0) {
            self::assertEquals($reflectedClass->getNamespaceName(), $classObject->getNamespaceName());
        }
        return $reflectedClass;
    }

    protected function parseFile($relativeFilePath)
    {
        return $this->parserService->parseFile($this->fixturesPath . $relativeFilePath);
    }

    /**
     * @param string $originalFile
     * @param string $pathToGeneratedFile
     */
    protected function compareGeneratedCodeWithOriginal($originalFile, $pathToGeneratedFile)
    {
        $originalLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, file_get_contents($this->fixturesPath . $originalFile), true);
        $generatedLines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, file_get_contents($pathToGeneratedFile), true);
        self::assertEquals(
            $originalLines,
            $generatedLines,
            'File ' . $originalFile . ' was not equal to original file.'
        );
    }
}
