<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
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
 * JSON view helper
 * enables access to json_encode and json_decode in fluid templates
 */

class Tx_ExtensionBuilder_ViewHelpers_JsonViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 *
	 * @param string $encodeType (optional) default encode possible: decode, encode
	 * @param boolean $arraysAsPhpNotation (optional) should arrays be notated as php arrays?
	 * @return mixed the encoded string or decoded data
	 */
	public function render($encodeType = 'encode', $arraysAsPhpNotation = TRUE) {
		$content = $this->renderChildren();
		if ($encodeType == 'decode') {
			return json_decode($content);
		}
		else {
			$content = json_encode($content);
			if ($arraysAsPhpNotation) {
				$content = Tx_ExtensionBuilder_Utility_Tools::convertJSONArrayToPHPArray($content);
			}
			return $content;
		}
	}


}

?>