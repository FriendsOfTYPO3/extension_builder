<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Sebastian Michaelsen <sebastian.gebhard@gmail.com>
*  All rights reserved
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
 * This class provides static methods to provide information about compatiblity
 * 
 * @package     TYPO3
 * @subpackage  extbase_kickstarter
 * @author      Sebastian Michaelsen <sebastian.gebhard@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_ExtbaseKickstarter_Utility_Compatibility {

	public static function getFluidVersionInteger() {
		return t3lib_div::int_from_ver(t3lib_extMgm::getExtensionVersion('fluid'));
	}

	/**
	 * Test if the installed fluid version is newer, older oder equals a specific version
	 * Usage:
	 * Tx_ExtbaseKickstarter_Utility_Compatiblity::compareFluidVersion('1.3.0', '<');
	 * Returns true if installed version is older than 1.3.0
	 *
	 * @param	string	Version to compare the installed fluid version with in a 3 dot format.
	 * @param 	string	Operator for the comparison. <, <=, =, >= or >
	 * @return	bool	Result of the comparison
	 */
	public static function compareFluidVersion($versionToCompare, $operator) {
		$fluidVersionInteger = self::getFluidVersionInteger();
		$versionToCompareInteger = t3lib_div::int_from_ver($versionToCompare);
		
		switch($operator) {

			case '>=':
				if($fluidVersionInteger == $versionToCompareInteger) return true;
				// fall through
			case '>':
				if($fluidVersionInteger > $versionToCompareInteger) return true;
				break;
				
			case '<=':
				if($fluidVersionInteger == $versionToCompareInteger) return true;
				// fall through
			case '<':
				if($fluidVersionInteger < $versionToCompareInteger) return true;
				break;
			
			case '=':
				if($fluidVersionInteger == $versionToCompare) return true;
				break;
		}
		return false;
	}
}

?>