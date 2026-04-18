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

namespace EBT\ExtensionBuilder\Tests\Unit\Utility;

use EBT\ExtensionBuilder\Utility\Inflector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InflectorTest extends TestCase
{
    public static function pluralizeDataProvider(): array
    {
        return [
            'regular noun' => ['comment', 'comments'],
            'already plural' => ['comments', 'comments'],
            'noun ending in -s' => ['status', 'statuses'],
            'irregular: child' => ['child', 'children'],
            'irregular: person' => ['person', 'people'],
            'compound word' => ['blogPost', 'blogPosts'],
            'CamelCase class name' => ['BlogAuthor', 'BlogAuthors'],
        ];
    }

    public static function singularizeDataProvider(): array
    {
        return [
            'regular plural' => ['comments', 'comment'],
            'already singular' => ['comment', 'comment'],
            'plural ending in -es' => ['statuses', 'status'],
            'irregular: children' => ['children', 'child'],
            'irregular: people' => ['people', 'person'],
            'compound word' => ['blogPosts', 'blogPost'],
            'CamelCase class name' => ['BlogAuthors', 'BlogAuthor'],
        ];
    }

    public static function humanizeDataProvider(): array
    {
        return [
            'camelCase' => ['blogAuthor', 'Blog Author'],
            'CamelCase class name' => ['BlogAuthor', 'Blog Author'],
            'underscore separated' => ['blog_author', 'Blog Author'],
            'single word' => ['author', 'Author'],
            'single CamelCase word' => ['Author', 'Author'],
            'multiple words camelCase' => ['myBlogPost', 'My Blog Post'],
        ];
    }

    #[Test]
    #[DataProvider('pluralizeDataProvider')]
    public function pluralize(string $word, string $expected): void
    {
        self::assertSame($expected, Inflector::pluralize($word));
    }

    #[Test]
    #[DataProvider('singularizeDataProvider')]
    public function singularize(string $word, string $expected): void
    {
        self::assertSame($expected, Inflector::singularize($word));
    }

    #[Test]
    #[DataProvider('humanizeDataProvider')]
    public function humanize(string $string, string $expected): void
    {
        self::assertSame($expected, Inflector::humanize($string));
    }
}
