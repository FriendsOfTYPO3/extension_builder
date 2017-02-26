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
 * Indentation ViewHelper
 *
 */
class IndentViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    protected $escapeOutput = FALSE;

    /**
     *
     * @param int $indentation
     * @param string $type
     * @return bool true or false
     */
    public function render($indentation)
    {
        $outputToIndent = $this->renderChildren();
        $lineArray = explode(chr(10), $outputToIndent);
        $indentString = '';
        for ($i = 0; $i < $indentation; $i++) {
            $indentString .= '    ';
        }
        return implode(chr(10) . $indentString, $lineArray);
    }
}
