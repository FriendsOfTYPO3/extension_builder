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
 * View helper for preg_replace
 *
 * = Examples =
 * <k:pregReplace match="/this/" replace="that" subject="this" />
 * {k:pregReplace(match:'/this/', replace:'that', subject:'this')}
 *
 */
class PregReplaceViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Execute the preg_replace
     *
     * @param mixed $match
     * @param mixed $replace
     * @param mixed $subject
     *
     * @return mixed
     */
    public function render($match, $replace, $subject)
    {
        return preg_replace($match, $replace, $subject);
    }
}
