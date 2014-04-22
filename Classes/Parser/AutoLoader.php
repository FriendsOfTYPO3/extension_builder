<?php
namespace EBT\ExtensionBuilder\Parser;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Class AutoLoader for PHP_Parser
 */
class AutoLoader {
	/**
	 * @var mixed
	 */
	static public $autoloadRegistry = NULL;

	/**
    * Registers \PHPParser_Autoloader as an SPL autoloader.
    */
    static public function register(){
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(__CLASS__, 'autoload'), TRUE, TRUE);
    }

    /**
    * Handles autoloading of classes.
    *
    * @param string $class A class name.
    */
    static public function autoload($class){
		if (0 === strpos($class, '\\EBT\\ExtensionBuilder\\Parser')) {
			$file = static::getCurrentDirectoryName() . '/'  . strtr(str_replace('EBT\\ExtensionBuilder\\Parser', '', $class), '\\', '/') . '.php';
			if (is_file($file)) {
				require $file;
			}
		} elseif (0 === strpos($class, 'PHPParser_')) {
			$file =  str_replace('Classes/Parser', '', static::getCurrentDirectoryName()) . 'Resources/Private/PHP/PHP-Parser/lib/'  . strtr($class, '_', '/') . '.php';
			if (is_file($file)) {
				require $file;
			} else {
				die('File not found: ' . $file);
			}
		}
    }

	static protected function getCurrentDirectoryName() {
		return str_replace('\\', '/', dirname(__FILE__));
	}
}

