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
 * Renders a comma separated list of a specific property and a list of objects
 *
 * = Examples =
 *
 * <code title="Example">
 * <k:listObjectsByPropertyCSV objects="{persons}" property="name" />
 * </code>
 *
 * Output:
 * Anthony,Billy,Chris
 *
 */
class ListObjectsByPropertyCSVViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Renders a comma separated list of a specific property and a list of objects
     * @param array $objects
     * @param string $property
     * @return string comma separated list of values
     */
    public function render($objects, $property)
    {
        $values = array();
        foreach ($objects as $object) {
            if (method_exists($object, 'get' . ucfirst($property))) {
                eval('$values[] = $object->get' . ucfirst($property) . '();');
            }
        }
        return join(',', $values);
    }
}
