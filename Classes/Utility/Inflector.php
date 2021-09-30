<?php

declare(strict_types=1);

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

namespace EBT\ExtensionBuilder\Utility;

use Sho_Inflect;

require(__DIR__ . '/../../Resources/Private/PHP/Sho_Inflect.php');

/**
 * Inflector utilities for the Extension Builder. This is a basic conversion from PHP
 * class and field names to a human readable form.
 */
class Inflector
{
    /**
     * @param string $word The word to pluralize
     * @return string The pluralized word
     */
    // TODO: These methods are static now, this breaks other places.
    public static function pluralize(string $word): string
    {
        return Sho_Inflect::pluralize($word);
    }

    /**
     * @param string $word The word to singularize
     * @return string The singularized word
     */
    public static function singularize(string $word): string
    {
        return Sho_Inflect::singularize($word);
    }

    /**
     * Convert a model class name like "BlogAuthor" or a field name like
     * "blog_author" to a humanized version "Blog Author" for better readability.
     *
     * @param string $string The camel cased or lower underscore value
     * @return string The humanized value
     */
    public static function humanize(string $string): string
    {
        $string = strtolower(preg_replace('/(?<=\w)([A-Z])/', '_\\1', $string));
        $delimiter = '\\';
        if (strpos($string, $delimiter) === false) {
            $delimiter = '_';
        }
        $string = str_replace($delimiter, ' ', $string);
        return ucwords($string);
    }
}
