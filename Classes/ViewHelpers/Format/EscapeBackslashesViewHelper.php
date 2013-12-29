<?php
namespace EBT\ExtensionBuilder\ViewHelpers\Format;

/***************************************************************
 *  Copyright notice
 *
 *  This script belongs to the TYPO3 package "Extension Builder".
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
 * View helper which escapes backslashes in a string.
 * Useful to format class names to be used within quotes.
 *
 * = Examples =
 *
 * <k:format.escapeBackslashes>{anyString}</k:format.escapeBackslashes>
 * {anyString -> k:format.escapeBackslashes()}
 *
 * TYPO3\CMS\Core\Log\Logger
 * Result:
 * TYPO3\\CMS\\Core\\Log\\Logger
 *
 */
class EscapeBackslashesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @param string $value
	 * @return string
	 */
	public function render($value = NULL) {
		if ($value === NULL) {
			$value = $this->renderChildren();
		}

		return str_replace('\\', '\\\\', $value);
	}

}
