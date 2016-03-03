<?php
namespace EBT\ExtensionBuilder\ViewHelpers;

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
 * View helper to check if one string contains another string
 *
 * = Examples =
 * <k:matchString match="this" in="this and that" />
 * {k:matchString(match:'this', in:'this and that')}
 *
 */
class MatchStringViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param string $match
     * @param string $in
     * @param bool $caseSensitive
     *
     * @return bool
     */
    public function render($match, $in, $caseSensitive = false)
    {
        $matchAsRegularExpression = '/' . $match . '/';
        if (!$caseSensitive) {
            $matchAsRegularExpression .= 'i';
        }
        return (preg_match($matchAsRegularExpression, $in) === 0) ? false : true;
    }
}
