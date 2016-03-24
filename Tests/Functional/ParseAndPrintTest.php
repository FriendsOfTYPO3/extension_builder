<?php
namespace EBT\ExtensionBuilder\Tests\Functional;

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

/**
 * Some tests to compare the parsed and the generated source code
 * The are only equal if the source follows the same coding conventions
 * as the printer
 *
 * Class ParseAndPrintTest
 */
class ParseAndPrintTest extends \EBT\ExtensionBuilder\Tests\BaseFunctionalTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->fixturesPath .= 'ClassParser/';
    }

    /**
     * @test
     */
    public function parseAndPrintSimplePropertyClass()
    {
        $fileName = 'SimpleProperty.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @test
     */
    public function parseAndPrintSimpleClassMethodWithManyParameter()
    {
        $fileName = 'ClassMethodWithManyParameter.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @test
     */
    public function parseAndPrintClassWithIncludeStatement()
    {
        $fileName = 'ClassWithIncludeStatement.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @test
     */
    public function parseAndPrintClassWithUseTraitStatement()
    {
        $fileName = 'ClassWithUseTraitStatement.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @test
     */
    public function parseAndPrintSimpleNamespacedClass()
    {
        $fileName = 'SimpleNamespace.php';
        $this->parseAndPrint($fileName, 'Namespaces/');
    }

    /**
     * @test
     */
    public function parseAndPrintSimpleNamespacedClassExtendingOtherClass()
    {
        $fileName = 'SimpleNamespaceExtendingOtherClass.php';
        $this->parseAndPrint($fileName, 'Namespaces/');
    }

    /**
     * @test
     */
    public function parseAndPrintSimpleNamespaceWithUseStatement()
    {
        $fileName = 'SimpleNamespaceWithUseStatement.php';
        $this->parseAndPrint($fileName, 'Namespaces/');
    }

    /**
     * @test
     */
    public function parseAndPrintMultiLineArray()
    {
        $fileName = 'ClassWithArrayProperty.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @test
     */
    public function parseAndPrintsNamespacedClassMethodWitNamespacedParameter()
    {
        $fileName = 'ClassMethodWithManyParameter.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @test
     */
    public function parseAndPrintsClassMethodWithMultilineParameter()
    {
        $fileName = 'ClassMethodWithMultilineParameter.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @test
     */
    public function parseAndPrintsClassMethodWithSwitchStatement()
    {
        $fileName = 'ClassMethodWithSwitchStatement.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @param $fileName
     * @param string $subFolder
     * @return \EBT\ExtensionBuilder\Domain\Model\File
     */
    protected function parseAndPrint($fileName, $subFolder = '')
    {
        $classFilePath = $this->fixturesPath . $subFolder . $fileName;
        self::assertTrue(file_exists($classFilePath), 'File not found: ' . $subFolder . $fileName);
        $fileHandler = fopen($classFilePath, 'r');
        $code = fread($fileHandler, filesize($classFilePath));
        $fileObject = $this->parserService->parseCode($code);
        $printedCode = $this->printerService->renderFileObject($fileObject, true);

        self::assertEquals(
            explode(PHP_EOL, $code),
            explode(PHP_EOL, $printedCode)
        );
    }
}
