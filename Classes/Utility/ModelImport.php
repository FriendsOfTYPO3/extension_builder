<?php
namespace EBT\ExtensionBuilder\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nico de Haen
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
 * provides methods to import a model
 * currently this class is not used anymore
 * Later it should be the parent class for importing from various model formats
 */
class ModelImport implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var string
	 */
	const EXTENSION_BUILDER_JSON = 'default';

	public function getConfiguration($data, $dataFormat) {
		switch ($dataFormat) {
			case self::EXTENSION_BUILDER_JSON	:
				return $this->getConfigurationFromExtensionBuilderJSON($data);
		}
	}

	protected function getConfigurationFromExtensionBuilderJSON($extensionConfigurationJSON) {
		return $extensionConfigurationJSON;
	}

}