<?php
namespace EBT\ExtensionBuilder\ViewHelpers\Format;

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

/**
 * Removes all linebreaks
 *
 */
class RemoveMultipleNewlinesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Uppercase first character
     *
     * @return string The altered string.
     * @author Christopher Hlubek <hlubek@networkteam.com>
     */
    public function render()
    {
        $content = trim($this->renderChildren());

        // Collapse whitespace lines
        $content = preg_replace('/^\\s+$/m', '', $content);
        $content = preg_replace('/\\n\\n+/', LF, $content);

        return $content;
    }
}
