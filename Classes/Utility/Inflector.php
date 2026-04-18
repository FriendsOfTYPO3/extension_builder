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

use Doctrine\Inflector\Inflector as DoctrineInflector;
use Doctrine\Inflector\InflectorFactory;

/**
 * Inflector utilities for the Extension Builder. This is a basic conversion from PHP
 * class and field names to a human readable form.
 */
class Inflector
{
    private static ?DoctrineInflector $instance = null;

    private static function getInstance(): DoctrineInflector
    {
        if (self::$instance === null) {
            self::$instance = InflectorFactory::create()->build();
        }
        return self::$instance;
    }

    /**
     * @param string $word The word to pluralize
     * @return string The pluralized word
     */
    public static function pluralize(string $word): string
    {
        return self::getInstance()->pluralize($word);
    }

    /**
     * @param string $word The word to singularize
     * @return string The singularized word
     */
    public static function singularize(string $word): string
    {
        return self::getInstance()->singularize($word);
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
        if (!str_contains($string, $delimiter)) {
            $delimiter = '_';
        }
        $string = str_replace($delimiter, ' ', $string);
        return ucwords($string);
    }
}
