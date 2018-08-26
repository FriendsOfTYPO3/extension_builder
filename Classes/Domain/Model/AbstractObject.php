<?php
namespace EBT\ExtensionBuilder\Domain\Model;

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

use EBT\ExtensionBuilder\Exception\FileNotFoundException;
use EBT\ExtensionBuilder\Exception\SyntaxError;
use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use PhpParser\Node\Stmt\Class_;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * abstract object representing a class, method or property in the context of
 * software development
 *
 */
abstract class AbstractObject
{
    /**
     *  const MODIFIER_PUBLIC    =  1;
     *  const MODIFIER_PROTECTED =  2;
     *  const MODIFIER_PRIVATE   =  4;
     *  const MODIFIER_STATIC    =  8;
     *  const MODIFIER_ABSTRACT  = 16;
     *  const MODIFIER_FINAL     = 32;
     *
     * @var int[]
     */
    private $mapModifierNames = [
        'public' => Class_::MODIFIER_PUBLIC,
        'protected' => Class_::MODIFIER_PROTECTED,
        'private' => Class_::MODIFIER_PRIVATE,
        'static' => Class_::MODIFIER_STATIC,
        'abstract' => Class_::MODIFIER_ABSTRACT,
        'final' => Class_::MODIFIER_FINAL
    ];
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $namespaceName = '';

    /**
     * modifiers  (privat, static abstract etc. not to mix up with "isModified" )
     * @var int
     */
    protected $modifiers;

    /**
     * @var string
     */
    protected $docComment = null;

    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\Comment[]
     */
    protected $comments = [];

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string[]
     */
    protected $descriptionLines = [];

    /**
     * @var string[]
     */
    protected $tags = [];

    /**
     * this flag is set to true if a modification of an object was detected
     *
     * @var bool
     */
    protected $isModified = false;

    /**
     * Setter for name
     *
     * @param string $name name
     * @return $this;
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Getter for name
     *
     * @return string name
     */
    public function getName()
    {
        return $this->name;
    }

    public function getQualifiedName()
    {
        return $this->getNamespaceName() . '\\' . $this->getName();
    }

    /**
     * Checks if the doc comment of this method is tagged with
     * the specified tag
     *
     * @param  string $tagName : Tag name to check for
     * @return bool true if such a tag has been defined, otherwise false
     */
    public function isTaggedWith($tagName)
    {
        return (isset($this->tags[$tagName]));
    }

    /**
     * Returns an array of tags and their values
     *
     * @return array Tags and values
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return bool
     */
    public function hasTags()
    {
        return count($this->getTags()) > 0;
    }

    /**
     * sets the array of tags
     * @param string[] tags
     *
     * @return $this;
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * sets a tags
     *
     * @param string $tagName
     * @param mixed $tagValue (optional)
     * @param bool $override
     * @return $this
     */
    public function setTag($tagName, $tagValue = '', $override = true)
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
        return $this;
    }

    /**
     * unsets a tags
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
     * Returns the values of the specified tag
     * @param string $tagName
     * @return string Values of the given tag
     */
    public function getTagValues($tagName)
    {
        if (!$this->isTaggedWith($tagName)) {
            throw new \InvalidArgumentException('Tag "' . $tagName . '" does not exist.', 1337645712);
        }
        return $this->tags[$tagName];
    }

    /**
     * Returns the value of the specified tag
     *
     * @param string $tagName
     * @return string Value of the given tag
     */
    public function getTagValue($tagName)
    {
        $tagValues = $this->getTagValues($tagName);
        if (is_array($tagValues) && count($tagValues) > 1) {
            throw new \InvalidArgumentException('Tag "' . $tagName . '" has multiple values.');
        }
        if (is_string($tagValues)) {
            return $tagValues;
        }
        if (is_array($tagValues)) {
            return $tagValues[0];
        }
        return '';
    }

    /**
     * is called by fluid
     * converts each tags to a single line containing name and value(s)
     *
     * @return array
     */
    public function getAnnotations()
    {
        $annotations = [];
        $tags = $this->getTags();
        $tagNames = array_keys($tags);
        foreach ($tagNames as $tagName) {
            if (empty($tags[$tagName])) {
                $annotations[] = $tagName;
            } elseif (is_array($tags[$tagName])) {
                foreach ($tags[$tagName] as $tagValue) {
                    $annotations[] = $tagName . ' ' . $tagValue;
                }
            } else {
                $annotations[] = $tagName . ' ' . $tags[$tagName];
            }
        }
        return $annotations;
    }

    /**
     * Get property description to be used in comments
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get description lines as array
     * used by fluid in templates
     *
     * @return string[] Property description
     */
    public function getDescriptionLines()
    {
        return GeneralUtility::trimExplode(PHP_EOL, trim($this->getDescription()));
    }

    /**
     * set description lines as array
     * this enables more control for line length and line breaks
     * @param string[] $descriptionLines
     *
     * @return void
     */
    public function setDescriptionLines($descriptionLines)
    {
        $this->descriptionLines = $descriptionLines;
        $this->description = implode(' ', $descriptionLines);
    }

    /**
     * Set property description
     *
     * @param string $description Property description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $this->descriptionLines = explode(PHP_EOL, wordwrap($description, 80, PHP_EOL));
        return $this;
    }

    /**
     *
     *
     * @return bool true if the description isn't empty
     */
    public function hasDescription()
    {
        return !empty($this->description);
    }

    /**
     * Setter for modifiers (will set all modifiers at once,
     * since modifiers are claculated bitwise)
     *
     * @param int $modifiers modifiers
     * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject (for fluid interface)
     */
    public function setModifiers($modifiers)
    {
        $this->modifiers = $modifiers;
        return $this;
    }

    /**
     * adds a modifier
     *
     * @param string $modifierName
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject (for fluid interface)
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     */
    public function addModifier($modifierName)
    {
        $modifier = $this->mapModifierNames[$modifierName];
        if (!in_array($modifierName, $this->getModifierNames())) {
            $this->validateModifier($modifier);
            $this->modifiers |= $this->mapModifierNames[$modifierName];
        }
        return $this;
    }

    /**
     * Use this method to set an accessor modifier,
     * it will care for removing existing ones to avoid syntax errors
     *
     * @param string $modifierName
     *
     * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject (for fluid interface)
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     */
    public function setModifier($modifierName)
    {
        if (in_array($modifierName, $this->getModifierNames())) {
            return $this; // modifier is already present
        }
        $modifier = $this->mapModifierNames[$modifierName];
        if (in_array($modifier, NodeConverter::$accessorModifiers)) {
            foreach (NodeConverter::$accessorModifiers as $accessorModifier) {
                // unset all accessorModifier
                if ($this->modifiers & $accessorModifier) {
                    $this->modifiers ^= $accessorModifier;
                }
            }
        }
        $this->validateModifier($modifier);
        $this->modifiers |= $modifier;
        return $this;
    }

    /**
     * @param string $modifierName
     * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject (for fluid interface)
     */
    public function removeModifier($modifierName)
    {
        $this->modifiers ^= $this->mapModifierNames[$modifierName];
        return $this;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject (for fluid interface)
     */
    public function removeAllModifiers()
    {
        $this->modifiers = 0;
        return $this;
    }

    /**
     * Getter for modifiers
     *
     * @return int
     *
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * getModifierNames
     *
     * @return array
     */
    public function getModifierNames()
    {
        $modifiers = $this->getModifiers();
        return NodeConverter::modifierToNames($modifiers);
    }

    /**
     * validate if the modifier can be added to the current modifiers or not
     *
     * @param $modifier
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @throws \EBT\ExtensionBuilder\Exception\SyntaxError
     */
    protected function validateModifier($modifier)
    {
        if ($modifier == Class_::MODIFIER_FINAL && $this->isAbstract() ||
            $modifier == Class_::MODIFIER_ABSTRACT && $this->isFinal()
        ) {
            throw new SyntaxError('Abstract and Final can\'t be applied both to same object');
        } elseif ($modifier == Class_::MODIFIER_STATIC && $this->isAbstract() ||
            $modifier == Class_::MODIFIER_ABSTRACT && $this->isStatic()
        ) {
            throw new FileNotFoundException('Abstract and Static can\'t be applied both to same object');
        }
        try {
            Class_::verifyModifier($this->modifiers, $modifier);
        } catch (\PhpParser\Error $e) {
            throw new SyntaxError(
                'Only one access modifier can be applied to one object. Use setModifier to avoid this exception'
            );
        }
    }

    /**
     * Parses the given doc comment and saves description and
     * tags in the object properties. They can be retrieved by the
     * getTags() getTagValues() and getDescription() methods.
     *
     * Tags and description can be manipulated and the getter
     * will render the appropriately modified docComment
     *
     * @param string $docComment A doc comment
     * @return void
     */
    public function setDocComment($docComment)
    {
        $lines = explode(chr(10), $docComment);
        foreach ($lines as $line) {
            $line = preg_replace('/(\\s*\\*\\/\\s*)?$/', '', $line);
            $line = trim($line);
            if ($line === '*/') {
                break;
            }
            if (strlen($line) > 0 && strpos($line, '* @') !== false) {
                $this->parseTag(substr($line, strpos($line, '@')));
            } else {
                if (count($this->tags) === 0) {
                    $this->description .= preg_replace('/\\s*\\/?[\\\\*]*\\s?(.*)$/', '$1', $line) . PHP_EOL;
                }
            }
        }
        $this->descriptionLines = GeneralUtility::trimExplode(PHP_EOL, $this->description, true);
        $this->description = trim($this->description);
    }

    /**
     * Parses a line of a doc comment for a tag and its value.
     * The result is stored in the internal tags array.
     *
     * @param string $line A line of a doc comment which starts with an @-sign
     * @return void
     */
    protected function parseTag($line)
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

    /**
     * Getter for docComment
     *
     * render a docComment string, based on description and tags
     *
     * @return string
     */
    public function getDocComment()
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
            if (!empty($docCommentLines)) {
                array_unshift($docCommentLines, PHP_EOL);
            }
            if (!empty($this->descriptionLines)) {
                $docCommentLines = array_merge($this->descriptionLines, $docCommentLines);
            } else {
                array_unshift($docCommentLines, $this->description);
            }
        }
        $docCommentLines = preg_replace('/\\s+$/', '', $docCommentLines);
        $docCommentLines = preg_replace('/^/', ' * ', $docCommentLines);
        return '/**' . PHP_EOL . implode(PHP_EOL, $docCommentLines) . PHP_EOL . ' */';
    }

    /**
     * is there a docComment
     *
     * @return bool
     */
    public function hasDocComment()
    {
        return !empty($this->docComment);
    }

    /**
     * @param string $commentText
     * @return void
     */
    public function addComment($commentText)
    {
        $this->comments[] = $commentText;
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\Comment[] $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return \EBT\ExtensionBuilder\Domain\Model\ClassObject\Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Setter for isModified
     *
     * @param string $isModified isModified
     * @return void
     */
    public function setIsModified($isModified)
    {
        $this->isModified = $isModified;
    }

    /**
     * Getter for isModified
     *
     * @return string isModified
     */
    public function getIsModified()
    {
        return $this->isModified;
    }

    /**
     * @param string $namespaceName
     * @return \EBT\ExtensionBuilder\Domain\Model\AbstractObject
     */
    public function setNamespaceName($namespaceName)
    {
        $this->namespaceName = $namespaceName;
        return $this;
    }

    /**
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->namespaceName;
    }

    /**
     * @return bool
     */
    public function isNamespaced()
    {
        if (empty($this->namespaceName)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return (($this->modifiers & Class_::MODIFIER_PUBLIC) !== 0);
    }

    /**
     * @return bool
     */
    public function isProtected()
    {
        return (($this->modifiers & Class_::MODIFIER_PROTECTED) !== 0);
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return (($this->modifiers & Class_::MODIFIER_PRIVATE) !== 0);
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return (($this->modifiers & Class_::MODIFIER_STATIC) !== 0);
    }

    /**
     * @return bool
     */
    public function isAbstract()
    {
        return (($this->modifiers & Class_::MODIFIER_ABSTRACT) !== 0);
    }

    /**
     * @return bool
     */
    public function isFinal()
    {
        return (($this->modifiers & Class_::MODIFIER_FINAL) !== 0);
    }
}
