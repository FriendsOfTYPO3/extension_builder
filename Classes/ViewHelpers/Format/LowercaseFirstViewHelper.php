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
 * Wrapper for PHPs ucfirst function.
 * @see http://www.php.net/manual/en/ucfirst
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:uppercaseFirst>{textWithMixedCase}</k:uppercaseFirst>
 * </code>
 *
 * Output:
 * TextWithMixedCase
 *
 */
class LowercaseFirstViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Lowercase first character
     *
     * @return string The altered string.
     */
    public function render()
    {
        $content = $this->renderChildren();
        return \TYPO3\CMS\Core\Utility\GeneralUtility::lcfirst($content);
    }
}
