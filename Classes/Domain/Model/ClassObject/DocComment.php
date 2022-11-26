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

namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;

use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DocComment extends Comment
{
    protected string $description = '';
    /**
     * @var string[]
     */
    protected array $descriptionLines = [];
    protected array $tags = [];

    /**
     * @param string $text Comment text (including comment delimiters like /*)
     */
    public function __construct(string $text)
    {
        $this->text = $text;
        $this->initialize($text);
    }

    /**
     * Parses the given doc comment and saves the result (description and tags) in
     * the parser's object. They can be retrieved by the getTags() getTagValues()
     * and getDescription() methods.
     *
     * @param string $docComment
     */
    public function initialize(string $docComment): void
    {
        $lines = explode(chr(10), $docComment);
        foreach ($lines as $line) {
            $line = preg_replace('/(\\s*\\*\\/\\s*)?$/', '', $line);
            $line = trim($line);
            if (strlen($line) > 0 && strpos($line, '* @') !== false) {
                $this->parseTag(substr($line, strpos($line, '@')));
            } elseif (count($this->tags) === 0) {
                $this->description .= preg_replace('/\\s*\\/?[\\\\*]*\\s?(.*)$/', '$1', $line) . chr(10);
            }
        }
        $this->descriptionLines = GeneralUtility::trimExplode(chr(10), $this->description, true);
        $this->description = trim($this->description);
    }

    public function hasDescription(): bool
    {
        return !empty($this->description);
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
        // TODO: enable automated splitting into lines after certain number of characters?
        $this->descriptionLines = [];
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @param string $tagName
     * @param mixed $tagValue
     * @param bool $override
     */
    public function setTag(string $tagName, $tagValue = null, bool $override = false): void
    {
        if (!$override && isset($this->tags[$tagName])) {
            if (!is_array($this->tags[$tagName])) {
                // build an array with the existing value as first element
                $this->tags[$tagName] = [$this->tags[$tagName]];
            }
            $this->tags[$tagName][] = $tagValue;
        } else {
            $this->tags[$tagName] = $tagValue;
        }
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Returns the values of the specified tag. The doc comment must be parsed with
     * parseDocComment() before tags are available.
     *
     * @param string $tagName The tag name to retrieve the values for
     * @return array The tag's values
     * @throws InvalidArgumentException
     */
    public function getTagValues(string $tagName): array
    {
        if (!$this->isTaggedWith($tagName)) {
            throw new InvalidArgumentException('Tag "' . $tagName . '" does not exist.', 1337645712);
        }
        return $this->tags[$tagName];
    }

    /**
     * unsets a tag
     *
     * @param string $tagName
     */
    public function removeTag($tagName): void
    {
        //TODO: multiple tags with same tag name must be possible (param etc.)
        unset($this->tags[$tagName]);
    }

    /**
     * Checks if a tag with the given name exists
     *
     * @param string $tagName The tag name to check for
     * @return bool true the tag exists, otherwise false
     */
    public function isTaggedWith($tagName): bool
    {
        return isset($this->tags[$tagName]);
    }

    /**
     * Parses a line of a doc comment for a tag and its value. The result is stored
     * in the internal tags array.
     *
     * @param string $line A line of a doc comment which starts with an @-sign
     */
    protected function parseTag(string $line): void
    {
        $tagAndValue = [];
        if (preg_match('/@([A-Za-z0-9\\\-]+)(\(.*\))? ?(.*)/', $line, $tagAndValue) === 0) {
            $tagAndValue = preg_split('/\\s/', $line, 2);
        } else {
            array_shift($tagAndValue);
        }
        $tag = trim($tagAndValue[0] . $tagAndValue[1], '@');
        if (count($tagAndValue) > 1) {
            $this->tags[$tag][] = trim($tagAndValue[2], ' "');
        } else {
            $this->tags[$tag] = [];
        }
    }

    public function setValue(string $value): void
    {
        $this->initialize($value);
    }

    public function getValue(): string
    {
        return $this->toString(true);
    }

    /**
     * Returns a string representation of the ignorable.
     *
     * @param bool $singleLineCommentAllowed
     * @return string String representation
     */
    public function toString(bool $singleLineCommentAllowed = false): string
    {
        $docCommentLines = [];

        if (is_array($this->tags)) {
            if (isset($this->tags['return'])) {
                $returnTagValue = $this->tags['return'];
                // always keep the return tag as last tag
                unset($this->tags['return']);
                $this->tags['return'] = $returnTagValue;
            }
            foreach ($this->tags as $tagName => $tags) {
                if (is_array($tags) && !empty($tags)) {
                    foreach ($tags as $tagValue) {
                        $docCommentLines[] = '@' . $tagName . ' ' . $tagValue;
                    }
                } elseif (is_array($tags) && empty($tags)) {
                    $docCommentLines[] = '@' . $tagName;
                } else {
                    $docCommentLines[] = '@' . $tagName . ' ' . $tags;
                }
            }
        }

        if (!empty($this->description)) {
            array_unshift($docCommentLines, PHP_EOL);
            if (!empty($this->descriptionLines)) {
                $docCommentLines = array_merge($this->descriptionLines, $docCommentLines);
            } else {
                array_unshift($docCommentLines, $this->description);
            }
        }

        if ($singleLineCommentAllowed && count($docCommentLines) === 1) {
            return '/** ' . $docCommentLines[0] . ' */';
        }

        $docCommentLines = preg_replace('/\\s+$/', '', $docCommentLines);
        $docCommentLines = preg_replace('/^/', ' * ', $docCommentLines);
        return '/**' . PHP_EOL . implode(PHP_EOL, $docCommentLines) . PHP_EOL . ' */';
    }
}
