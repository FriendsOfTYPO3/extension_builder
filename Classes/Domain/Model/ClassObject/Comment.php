<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;

class Comment
{
    /**
     * the raw comment content
     */
    protected string $text = '';
    protected int $line = -1;

    /**
     * @param string $text Comment text (including comment delimiters like /*)
     * @param int $line Line number the comment started on
     */
    public function __construct($text, $line = -1)
    {
        $this->text = $text;
        $this->line = $line;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
