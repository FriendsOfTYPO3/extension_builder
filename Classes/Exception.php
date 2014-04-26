<?php
namespace EBT\ExtensionBuilder;
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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Exception extends \TYPO3\CMS\Extbase\Exception {
	/**
	 * @var string|NULL
	 */
	protected $subKey = NULL;

	/**
	 * @param string $message
	 * @param integer $code
	 * @param \Exception $previous
	 */
	public function __construct($message, $code, \Exception $previous = null) {

		$this->findTranslationSubKeyByExceptionClassName();

		// Build the locallang label index
		$translationKey = 'error.';
		if ($this->subKey !== NULL) {
			$translationKey .= $this->subKey . '.';
		}
		$translationKey .= $code;

		// Get the translated message
		$translated = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($translationKey, 'ExtbaseBuilder');
		if (!empty($translated)) {
			$message = $translated;
		}

		parent::__construct($message, $code);
	}

	/**
	 * Find the translation subkey based on the class name of the exception
	 */
	protected function findTranslationSubKeyByExceptionClassName() {
		preg_match("/_([^_]*)Exception$/", get_class($this), $subKey);

		if (!empty($subKey[1])) {
			$this->subKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($subKey[1]);
		}
	}
}
