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
    protected ParserService $parserService;
    protected Printer $printerService;
    protected string $tmpDir = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturesPath = Environment::getPublicPath() . '/typo3conf/ext/extension_builder/Tests/Fixtures/ClassParser/';

        vfsStream::setup('tmpDir');

        $this->tmpDir = vfsStream::url('tmpDir') . '/';
        $nodeFactory = new NodeFactory();
        $this->printerService = new Printer($nodeFactory);
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
                1 => '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TOOOL\Projects\Domain\Model\Calculation> $tests',
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

    /**
     * @test
     */
    public function parsedFileObjectCarriesOriginalStmtsAndTokens(): void
    {
        $classFileObject = $this->parserService->parseFile($this->fixturesPath . 'SimpleProperty.php');
        self::assertNotNull($classFileObject->getOrigStmts(), 'origStmts must be set after parseFile()');
        self::assertNotEmpty($classFileObject->getOrigStmts());
        self::assertNotNull($classFileObject->getOrigTokens(), 'origTokens must be set after parseFile()');
        self::assertNotEmpty($classFileObject->getOrigTokens());
    }

    /**
     * @test
     */
    public function newFileObjectHasNoOrigStmtsOrTokens(): void
    {
        $fileObject = new File();
        self::assertNull($fileObject->getOrigStmts());
        self::assertNull($fileObject->getOrigTokens());
    }

    /**
     * @test
     *
     * Regression test for https://github.com/FriendsOfTYPO3/extension_builder/issues/628
     * Verifies that nested method call arguments retain proper indentation after a roundtrip.
     */
    public function renderPreservesIndentationOfNestedMethodCalls(): void
    {
        $fileName = 'ClassWithNestedMethodCalls.php';
        $originalPath = $this->fixturesPath . $fileName;
        $classFileObject = $this->parserService->parseFile($originalPath);
        $rendered = $this->printerService->renderFileObject($classFileObject);

        // The key assertion: nested args must be indented more than the outer call,
        // not all left-aligned at the same indentation (which was the bug).
        self::assertStringContainsString(
            '        $query->matching(' . "\n" . '            $query->logicalOr(',
            $rendered,
            'Arg of matching() must be indented relative to the call'
        );
        self::assertStringContainsString(
            '            $query->logicalOr(' . "\n" . '                [',
            $rendered,
            'Array arg of logicalOr() must be indented relative to the call'
        );
        self::assertStringContainsString(
            '                    $query->like(',
            $rendered,
            'Array items must be indented inside the array'
        );
    }

    /**
     * @test
     *
     * Regression test for https://github.com/FriendsOfTYPO3/extension_builder/issues/628
     * Verifies a single multiline arg (array literal) is properly indented.
     */
    public function renderPreservesIndentationOfMultilineArrayArg(): void
    {
        $fileName = 'ClassWithNestedMethodCalls.php';
        $originalPath = $this->fixturesPath . $fileName;
        $classFileObject = $this->parserService->parseFile($originalPath);
        $rendered = $this->printerService->renderFileObject($classFileObject);

        // findAllActive has a single multiline array arg — must be indented
        self::assertStringContainsString(
            '        return $this->findBy(' . "\n" . '            [',
            $rendered,
            'Single multiline array arg must be indented relative to the call'
        );
    }

    /**
     * @test
     *
     * Regression test for https://github.com/FriendsOfTYPO3/extension_builder/issues/130
     * Verifies that a newline is preserved after a case label, so statements appear on
     * their own line instead of being concatenated onto the same line as the colon.
     */
    public function renderPreservesNewlineAfterCaseLabel(): void
    {
        $fileName = 'ClassWithSwitchStatement.php';
        $originalPath = $this->fixturesPath . $fileName;
        $classFileObject = $this->parserService->parseFile($originalPath);
        $rendered = $this->printerService->renderFileObject($classFileObject);

        self::assertStringNotContainsString(
            "case 'foo':    ",
            $rendered,
            'case label must not be followed by a statement on the same line'
        );
        self::assertStringNotContainsString(
            "case 'bar':    ",
            $rendered,
            'case label must not be followed by a statement on the same line'
        );
        self::assertStringContainsString(
            "case 'foo':" . "\n",
            $rendered,
            'case label must be followed by a newline'
        );
        self::assertStringContainsString(
            "case 'bar':" . "\n",
            $rendered,
            'case label must be followed by a newline'
        );
    }

    private function parseAndWrite(string $fileName, string $subFolder = ''): File
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
    private function compareClasses(File $classFileObject, string $pathToGeneratedFile): ReflectionClass
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

    private function parseFile(string $relativeFilePath): File
    {
        return $this->parserService->parseFile($this->fixturesPath . $relativeFilePath);
    }

    private function compareGeneratedCodeWithOriginal(string $originalFile, string $pathToGeneratedFile): void
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
