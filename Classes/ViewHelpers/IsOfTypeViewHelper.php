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
class IsOfTypeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Checks if $object is of type $type and returns true or false respectively
     * @param mixed $object
     * @param string $type
     * @return bool true or false
     */
    public function render($object, $type)
    {
        return is_a($object, 'EBT\\ExtensionBuilder\\Domain\\Model\\' . $type);
    }
}
