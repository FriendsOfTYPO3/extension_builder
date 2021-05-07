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

namespace EBT\ExtensionBuilder\Tests\Unit;

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Parser\NodeFactory;
use EBT\ExtensionBuilder\Service\ParserService;
use EBT\ExtensionBuilder\Service\Printer;
use EBT\ExtensionBuilder\Tests\BaseUnitTest;
use org\bovigo\vfs\vfsStream;
use ReflectionClass;
use ReflectionException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PrinterTest extends BaseUnitTest
{
    /**
     * @var ParserService
     */
    protected $parserService;
    /**
     * @var Printer
     */
    protected $printerService;
    /**
     * @var string
     */
    protected $tmpDir = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturesPath = Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Tests/Fixtures/ClassParser/';

        vfsStream::setup('tmpDir');

        $this->tmpDir = vfsStream::url('tmpDir') . '/';
        $this->printerService = $this->getAccessibleMock(Printer::class, ['dummy']);

        $nodeFactory = new NodeFactory();
        $this->printerService->_set('nodeFactory', $nodeFactory);
        $this->parserService = new ParserService();
    }

    /**
     * @test
     */
    public function printSimplePropertyClass(): void
    {
        self::assertTrue(
            is_writable($this->tmpDir),
            'Directory not writable: ' . $this->tmpDir . '. Can\'t compare rendered files'
        );
        $fileName = 'SimpleProperty.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printClassWithMultipleProperties(): void
    {
        $fileName = 'ClassWithMultipleProperties.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleClassMethodWithManyParameter(): void
    {
        $fileName = 'ClassMethodWithManyParameter.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleClassMethodWithMissingParameterTag(): void
    {
        $fileName = 'ClassMethodWithMissingParameterTag.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $reflectedClass = $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        // No way to detect the typeHint with Reflection...
    }

    /**
     * @test
     */
    public function printClassWithIncludeStatement(): void
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
    public function printClassWithDefaultValuesInProperties(): void
    {
        $fileName = 'BasicClassWithDefaultValuesInProperties.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printClassWithPreStatements(): void
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
     */
    public function printClassWithPostStatements(): void
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
     */
    public function printClassWithPreAndPostStatements(): void
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
    public function printSimpleNamespacedClass(): void
    {
        $fileName = 'SimpleNamespace.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleNamespacedClassExtendingOtherClass(): void
    {
        $fileName = 'SimpleNamespaceExtendingOtherClass.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        //$this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printSimpleNamespaceWithUseStatement(): void
    {
        $fileName = 'SimpleNamespaceWithUseStatement.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printMultipleNamespacedClass(): void
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
    public function printMultipleBracedNamespacedClass(): void
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
    public function printMultiLineArray(): void
    {
        $fileName = 'ClassWithArrayProperty.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $this->compareClasses($classFileObject, $this->tmpDir . $fileName);
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printsClassMethodWithMissingParameterTag(): void
    {
        $fileName = 'ClassMethodWithMissingParameterTag.php';
        $classFileObject = $this->parseAndWrite($fileName);
        $tags = $classFileObject->getFirstClass()->getMethod('testMethod')->getTagValues('param');
        self::assertCount(3, $tags);
        self::assertSame(
            $tags,
            ['$string', 'array $arr', '\\EBT\\ExtensionBuilder\\Parser\\Utility\\NodeConverter $n']
        );
    }

    /**
     * @test
     */
    public function printsNamespacedClassMethodWitNamespacedParameter(): void
    {
        $fileName = 'ClassMethodWithManyParameter.php';
        $classFileObject = $this->parseAndWrite($fileName, 'Namespaces/');
        $testMethod = $classFileObject->getFirstClass()->getMethod('testMethod');
        $tags = $testMethod->getTagValues('param');
        self::assertCount(2, $tags);
        self::assertSame(
            $tags,
            [
                0 => '\EBT\ExtensionBuilder\Domain\Model\DomainObject $domainObject',
                1 => '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TOOOL\Projects\Domain\Model\Calculation> $tests'
            ]
        );
        self::assertSame(
            '\EBT\ExtensionBuilder\Domain\Model\DomainObject',
            $testMethod->getParameterByPosition(0)->getTypeHint()
        );
        $this->compareGeneratedCodeWithOriginal('Namespaces/' . $fileName, $this->tmpDir . $fileName);
    }

    /**
     * @test
     */
    public function printsClassMethodWithMultilineParameter(): void
    {
        $fileName = 'ClassMethodWithMultilineParameter.php';
        $classFileObject = $this->parseAndWrite($fileName);
        self::assertSame(
            $classFileObject->getFirstClass()->getMethod('testMethod')->getParameterNames(),
            [
                0 => 'number',
                1 => 'stringParam',
                2 => 'arr',
                3 => 'n',
                4 => 'booleanParam',
                5 => 'float',
            ]
        );
        $this->compareGeneratedCodeWithOriginal($fileName, $this->tmpDir . $fileName);
    }

    protected function parseAndWrite(string $fileName, string $subFolder = ''): File
    {
        $classFilePath = $this->fixturesPath . $subFolder . $fileName;
        self::assertFileExists($classFilePath);

        $classFileObject = $this->parserService->parseFile($classFilePath);
        $newClassFilePath = $this->tmpDir . $fileName;
        file_put_contents($newClassFilePath, $this->printerService->renderFileObject($classFileObject, true));
        return $classFileObject;
    }

    /**
     * includes the generated file and compares the reflection class
     * with the class object
     *
     * @param File $classFileObject
     * @param string $pathToGeneratedFile
     *
     * @return ReflectionClass
     * @throws ReflectionException
     */
    protected function compareClasses(File $classFileObject, string $pathToGeneratedFile)
    {
        self::assertFileExists($pathToGeneratedFile, $pathToGeneratedFile . 'not exists');
        $classObject = $classFileObject->getFirstClass();
        self::assertInstanceOf(ClassObject::class, $classObject);
        $className = $classObject->getQualifiedName();
        if (!class_exists($className)) {
            require_once($pathToGeneratedFile);
        }
        self::assertTrue(
            class_exists($className),
            'Class "' . $className . '" does not exist! Tried ' . $pathToGeneratedFile
        );
        $reflectedClass = new ReflectionClass($className);
        self::assertSameSize(
            $reflectedClass->getMethods(),
            $classObject->getMethods(),
            'Method count does not match'
        );
        self::assertSameSize($reflectedClass->getProperties(), $classObject->getProperties());
        self::assertSameSize($reflectedClass->getConstants(), $classObject->getConstants());
        if (strlen($classObject->getNamespaceName()) > 0) {
            self::assertEquals($reflectedClass->getNamespaceName(), $classObject->getNamespaceName());
        }
        return $reflectedClass;
    }

    protected function parseFile(string $relativeFilePath): File
    {
        return $this->parserService->parseFile($this->fixturesPath . $relativeFilePath);
    }

    protected function compareGeneratedCodeWithOriginal(string $originalFile, string $pathToGeneratedFile): void
    {
        $originalLines = GeneralUtility::trimExplode(LF, file_get_contents($this->fixturesPath . $originalFile), true);
        $generatedLines = GeneralUtility::trimExplode(LF, file_get_contents($pathToGeneratedFile), true);
        self::assertEquals(
            $originalLines,
            $generatedLines,
            'File ' . $originalFile . ' was not equal to original file.'
        );
    }
}
