<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
 *  All rights reserved
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

/**
 * @author Nico de Haen
 */
class Tx_PhpParser_Test_ClassMethodWithMultilineParameter
{
    /**
     * This is the description
     *
     * @param int $number
     * @param string $stringParam
     * @param array $arr
     * @param bool $booleanParam
     * @param float $float
     * @param \EBT\ExtensionBuilder\Parser\Utility\NodeConverter $n
     * @return string
     */
    private static function testMethod(
        $number, $stringParam, array $arr, $booleanParam = false,
        $float = 0.2, \EBT\ExtensionBuilder\Parser\Utility\NodeConverter $n)
    {
        self::sendForDownload(
            $arr,
            'Foo-' . PHP_EOL . '-' . date('Y-m-d-H-i') . $stringParam,
            'Bar.docx'
        );
        if ($number > 3 && $booleanParam) {
            return 'bar';
        } else {
            return 'foo';
        }
    }
}
