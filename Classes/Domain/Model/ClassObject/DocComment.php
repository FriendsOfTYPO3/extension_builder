<?php
namespace EBT\ExtensionBuilder\Domain\Model\ClassObject;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class DocComment extends Comment
{
    /**
     * @var string
     */
    protected $description = '';
    /**
     * @var string[]
     */
    protected $descriptionLines = array();
    /**
     * @var array
     */
    protected $tags = array();

    /**
     * @param string $text Comment text (including comment delimiters like /*)
     */
    public function __construct($text)
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
     * @return void
     */
    public function initialize($docComment)
    {
        $lines = explode(chr(10), $docComment);
        foreach ($lines as $line) {
            $line = preg_replace('/(\\s*\\*\\/\\s*)?$/', '', $line);
            $line = trim($line);
            if (strlen($line) > 0 && strpos($line, '* @') !== false) {
                $this->parseTag(substr($line, strpos($line, '@')));
            } else {
                if (count($this->tags) === 0) {
                    $this->description .= preg_replace('/\\s*\\/?[\\\\*]*\\s?(.*)$/', '$1', $line) . chr(10);
                }
            }
        }
        $this->descriptionLines = GeneralUtility::trimExplode(chr(10), $this->description, true);
        $this->description = trim($this->description);
    }

    /**
     * @return bool
     */
    public function hasDescription()
    {
        return !empty($this->description);
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
        // TODO: enable automated splitting into lines after certain number of characters?
        $this->descriptionLines = array();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array $tags
     * @return void
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param string $tagName
     * @param mixed $tagValue
     * @param bool $override
     * @return void
     */
    public function setTag($tagName, $tagValue = null, $override = false)
    {
        if (!$override && isset($this->tags[$tagName])) {
            if (!is_array($this->tags[$tagName])) {
                // build an array with the existing value as first element
                $this->tags[$tagName] = array($this->tags[$tagName]);
            }
            $this->tags[$tagName][] = $tagValue;
        } else {
            $this->tags[$tagName] = $tagValue;
        }
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Returns the values of the specified tag. The doc comment must be parsed with
     * parseDocComment() before tags are available.
     *
     * @param string $tagName The tag name to retrieve the values for
     * @throws \InvalidArgumentException
     * @return array The tag's values
     */
    public function getTagValues($tagName)
    {
        if (!$this->isTaggedWith($tagName)) {
            throw new \InvalidArgumentException('Tag "' . $tagName . '" does not exist.', 1337645712);
        }
        return $this->tags[$tagName];
    }

    /**
     * unsets a tag
     *
     * @param string $tagName
     * @return void
     */
    public function removeTag($tagName)
    {
        //TODO: multiple tags with same tagname must be possible (param etc.)
        unset($this->tags[$tagName]);
    }

    /**
     * Checks if a tag with the given name exists
     *
     * @param string $tagName The tag name to check for
     * @return bool true the tag exists, otherwise false
     */
    public function isTaggedWith($tagName)
    {
        return (isset($this->tags[$tagName]));
    }

    /**
     * Parses a line of a doc comment for a tag and its value. The result is stored
     * in the internal tags array.
     *
     * @param string $line A line of a doc comment which starts with an @-sign
     * @return void
     */
    protected function parseTag($line)
    {
        $tagAndValue = array();
        if (preg_match('/@([A-Za-z0-9\\\-]+)(\(.*\))? ?(.*)/', $line, $tagAndValue) === 0) {
            $tagAndValue = preg_split('/\\s/', $line, 2);
        } else {
            array_shift($tagAndValue);
        }
        $tag = trim($tagAndValue[0] . $tagAndValue[1], '@');
        if (count($tagAndValue) > 1) {
            $this->tags[$tag][] = trim($tagAndValue[2], ' "');
        } else {
            $this->tags[$tag] = array();
        }
    }

    /**
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->initialize($value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->toString(true);
    }

    /**
     * Returns a string representation of the ignorable.
     *
     * @param bool $singleLineCommentAllowed
     * @return string String representation
     */
    public function toString($singleLineCommentAllowed = false)
    {
        $docCommentLines = array();

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
        } else {
            $docCommentLines = preg_replace('/\\s+$/', '', $docCommentLines);
            $docCommentLines = preg_replace('/^/', ' * ', $docCommentLines);
            return '/**' . PHP_EOL . implode(PHP_EOL, $docCommentLines) . PHP_EOL . ' */';
        }
    }
}
