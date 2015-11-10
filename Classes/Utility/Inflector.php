<?php
namespace EBT\ExtensionBuilder\Utility;

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

require(__DIR__ . '/../../Resources/Private/PHP/Sho_Inflect.php');

/**
 * Inflector utilities for the Extension Builder. This is a basic conversion from PHP
 * class and field names to a human readable form.
 *
 */
class Inflector
{
    /**
     * @param string $word The word to pluralize
     * @return string The pluralized word
     * @author Christopher Hlubek
     */
    // TODO: These methods are static now, this breaks other places.
    public static function pluralize($word)
    {
        return \Sho_Inflect::pluralize($word);
    }

    /**
     * @param string $word The word to singularize
     * @return string The singularized word
     * @author Sebastian Kurfürst <sbastian@typo3.org>
     */
    public static function singularize($word)
    {
        return \Sho_Inflect::singularize($word);
    }

    /**
     * Convert a model class name like "BlogAuthor" or a field name like
     * "blog_author" to a humanized version "Blog Author" for better readability.
     *
     * @param string $string The camel cased or lower underscore value
     * @return string The humanized value
     */
    public static function humanize($string)
    {
        $string = strtolower(preg_replace('/(?<=\w)([A-Z])/', '_\\1', $string));
        $delimiter = '\\';
        if (strpos($delimiter, $string) === false) {
            $delimiter = '_';
        }
        $string = str_replace($delimiter, ' ', $string);
        $string = ucwords($string);
        return $string;
    }
}
