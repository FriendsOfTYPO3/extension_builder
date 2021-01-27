<?php
declare(strict_types=1);

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
    public function parseAndPrintSimplePropertyClass(): void
    {
        $fileName = 'SimpleProperty.php';
        $this->parseAndPrint($fileName);
    }

    /**
     * @param string $fileName
     * @param string|null $subFolder
     */
    protected function parseAndPrint($fileName, $subFolder = ''): void
    {
        $classFilePath = $this->fixturesPath . $subFolder . $fileName;
        self::assertFileExists($classFilePath, 'File not found: ' . $subFolder . $fileName);

        $fileHandler = fopen($classFilePath, 'rb');
        $code = fread($fileHandler, filesize($classFilePath));
        fclose($fileHandler);

        $fileObject = $this->parserService->parseCode($code);
        self::assertEquals(
            GeneralUtility::trimExplode(PHP_EOL, $code),
            GeneralUtility::trimExplode(PHP_EOL, $this->printerService->renderFileObject($fileObject)),
            'File content is not equal'
        );
    }
}
