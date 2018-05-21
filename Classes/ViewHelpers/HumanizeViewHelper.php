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

use EBT\ExtensionBuilder\Utility\Inflector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Makes a word in CamelCase or lower_underscore human readable
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:inflect.humanize>foo_bar</k:inflect.humanize>
 * </code>
 *
 * Output:
 * Foo Bar
 *
 */
class HumanizeViewHelper extends AbstractViewHelper
{
    /**
     * @var \EBT\ExtensionBuilder\Utility\Inflector
     */
    protected $inflector = null;

    public function __construct()
    {
        $this->inflector = GeneralUtility::makeInstance(Inflector::class);
    }

    /**
    * Arguments Initialization
    */
    public function initializeArguments() {
       $this->registerArgument('string', 'string', 'The string to make human readable', TRUE);
    }

    /**
     * Make a word human readable
     *
     * @return string The human readable string
     */
    public function render()
    {
        $string = $this->arguments['string'];
        if ($string === null) {
            $string = $this->renderChildren();
        }
        return $this->inflector->humanize($string);
    }
}
