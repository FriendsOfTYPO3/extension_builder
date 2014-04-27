<?php
namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Nico de Haen <mail@ndh-websolutions.de>, ndh websolutions
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

class Comment {

	/**
	 * the raw comment content
	 *
	 * @var string
	 */
	protected $text = '';

	/**
	 * @var int
	 */
	protected $line = -1;

	/**
	 * @param string $text Comment text (including comment delimiters like /*)
	 * @param int    $line Line number the comment started on
	 */
	public function __construct($text, $line = -1) {
		$this->text = $text;
		$this->line = $line;
	}

	/**
	 * @param string $text
	 * @return void
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}

}
