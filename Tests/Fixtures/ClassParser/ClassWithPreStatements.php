<?php
declare(strict_types=1);

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

// just some stuff for testing

define('TX_PHPPARSER_TEST_FOO', 'BAR');

if (!isset($foo) && !isset($bar)) {
    $foo = 23;
    $bar = 42;
}

if ($bar > $foo && $foo == 23) {
    define('TX_PHPPARSER_TEST_BAR', 'FOO');
}

abstract class Tx_PhpParser_Tests_ClassWithPreStatement
{
}
