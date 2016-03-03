<?php
namespace FOO;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
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
 * Class Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicNameSpacedClass
 */
class Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicNameSpacedClass
{

    protected $names;

    const TEST = "test";

    const TEST2 = 'test';

    /**
     *
     * @return array $names
     */
    public function getNames()
    {
        return $this->names;
    }

    public function getNames0() { return $this->names; }

    public function getNames1()
    {
    }

    public function getNames2()
    {
    }

    public function getNames3()
    {
        return $this->names;
    }

    /**
     *
     * @param array $names
     * @return void
     */
    public function setNames(array $names)
    {
        $this->names = $names;
    }
}
