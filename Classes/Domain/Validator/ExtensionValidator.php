<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rens Admiraal
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
 * Schema for a whole extension
 *
 * @package ExtbaseKickstarter
 * @version $ID:$
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_ExtbaseKickstarter_Domain_Validator_ExtensionValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	const	ERROR_EXTKEY_LENGTH			= 0,
		ERROR_EXTKEY_ILLEGAL_CHARACTERS	= 1,
		ERROR_EXTKEY_ILLEGAL_PREFIX		= 2,
		ERROR_EXTKEY_ILLEGAL_FIRST_CHARACTER	= 3;

	/**
	 * Validate the given extension
	 *
	 * @param Tx_ExtbaseKickstarter_Domain_Model_Extension $extension
	 * @author Rens Admiraal
	 * @return boolean
	 */
	public function isValid($extension) {
		try {
			self::validateExtensionKey($extension->getExtensionKey());
		} catch (Tx_Extbase_Exception $e) {
			throw($e);
		}

		return true;
	}

	/**
	 * @author Rens Admiraal
	 * @param string $key
	 * @return boolean
	 * @throws Tx_ExtbaseKickstarter_Domain_Exception_ExtensionException
	 */
	private static function validateExtensionKey($key) {
		/**
		 * Character test
		 * Allowed characters are: a-z (lowercase), 0-9 and '_' (underscore)
		 */
		if (!preg_match("/^[a-z0-9_]*$/", $key)) {
			throw new Tx_ExtbaseKickstarter_Domain_Exception_ExtensionException('Illegal characters in extension key', self::ERROR_EXTKEY_ILLEGAL_CHARACTERS);
		}

		/**
		 * Start character
		 * Extension keys cannot start or end with 0-9 and '_' (underscore)
		 */
		if (preg_match("/^[0-9_]/", $key)) {
			throw new Tx_ExtbaseKickstarter_Domain_Exception_ExtensionException('Illegal first character of extension key', self::ERROR_EXTKEY_ILLEGAL_FIRST_CHARACTER);
		}

		/**
		 * Extension key length
		 * An extension key must have minimum 3, maximum 30 characters (not counting underscores)
		 */
		$keyLengthTest = str_replace('_', '', $key);
		if (strlen($keyLengthTest) < 3 || strlen($keyLengthTest) > 30) {
			throw new Tx_ExtbaseKickstarter_Domain_Exception_ExtensionException('Invalid extension key length', self::ERROR_EXTKEY_LENGTH);
		}

		/**
		 * Reserved prefixes
		 * The key must not being with one of the following prefixes: tx,u,user_,pages,tt_,sys_,ts_language_,csh_
		 */
		if (preg_match("/^(tx_|u_|user_|pages_|tt_|sys_|ts_language_|csh_)/", $key)) {
			throw new Tx_ExtbaseKickstarter_Domain_Exception_ExtensionException('Illegal extension key prefix', self::ERROR_EXTKEY_ILLEGAL_PREFIX);
		}

		return true;
	}
}