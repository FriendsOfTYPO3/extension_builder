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
 * method view helper
 */

class Tx_ExtensionBuilder_ViewHelpers_MethodViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 *
	 * @param object $methodObject
	 * @param string $renderElement
	 * @return
	 */
	public function render($methodObject, $renderElement) {
		$content = '';
		//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog(serialize($methodObject), $renderElement);
		switch ($renderElement) {
			case 'parameter'		:
				$content = $this->renderMethodParameter($methodObject);

		}
		return $content;
	}

	/**
	 * This methods renders the parameters of a method, including typeHints and default values.
	 *
	 * @param $methodObject
	 * @return string parameters
	 */
	private function renderMethodParameter($methodObject) {
		$parameters = array();
		if (is_array($methodObject->getParameters())) {
			foreach ($methodObject->getParameters() as $parameter) {
				$parameterName = $parameter->getName();
				$typeHint = $parameter->getTypeHint();
				if ($parameter->isOptional()) {
					$defaultValue = $parameter->getDefaultValue();
					// optional parameters have a default value
					if (!empty($typeHint)) {
						// typeHints of optional parameter have the format "typeHint or defaultValue"
						$typeHintParts = explode(' ', $typeHint);
						$typeHint = $typeHintParts[0];
					}

					// the default value has to be json_encoded to render its string representation
					if (is_array($defaultValue)) {
						if (!empty($defaultValue)) {
							$defaultValue = json_encode($defaultValue);
							// now we render php notation from JSON notation
							$defaultValue = Tx_ExtensionBuilder_Utility_Tools::convertJSONArrayToPHPArray($defaultValue);

							//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('default Value: '. $defaultValue, 'parameter debug');
						}
						else $defaultValue = 'array()';
					} elseif ($defaultValue === NULL) {
						$defaultValue = 'NULL';
					} else {
						$defaultValue = json_encode($defaultValue);
					}
					$parameterName .= ' = ' . $defaultValue;
				}

				$parameterName = '$' . $parameterName;

				if ($parameter->isPassedByReference()) {
					$parameterName = '&' . $parameterName;
				}
				if (!empty($typeHint)) {
					$parameterName = $typeHint . ' ' . $parameterName;
				}
				$parameters[] = $parameterName;
				//\TYPO3\CMS\Core\Utility\GeneralUtility::devlog($methodSchemaObject->getName().':'.$parameter->getName(), 'parameter debug');
			}
		}
		return implode(', ', $parameters);
	}


}

?>