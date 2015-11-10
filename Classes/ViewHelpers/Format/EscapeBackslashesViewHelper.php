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
class EscapeBackslashesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param string $value
     * @return string
     */
    public function render($value = null)
    {
        if ($value === null) {
            $value = $this->renderChildren();
        }

        return str_replace('\\', '\\\\', $value);
    }
}
