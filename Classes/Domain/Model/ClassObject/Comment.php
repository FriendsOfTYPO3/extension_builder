<?php
namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class Comment
{
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
     * @param int $line Line number the comment started on
     */
    public function __construct($text, $line = -1)
    {
        $this->text = $text;
        $this->line = $line;
    }

    /**
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
