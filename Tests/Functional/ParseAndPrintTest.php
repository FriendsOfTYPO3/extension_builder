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

use EBT\ExtensionBuilder\Tests\BaseFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Some tests to compare the parsed and the generated source code
 * The are only equal if the source follows the same coding conventions
 * as the printer
 *
 * Class ParseAndPrintTest
 */
class ParseAndPrintTest extends BaseFunctionalTest
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
        self::assertEquals(
            GeneralUtility::trimExplode(PHP_EOL, $this->printerService->renderFileObject($fileObject, true)),
            GeneralUtility::trimExplode(PHP_EOL, $code),
            'Not equal'
        );
    }
}
