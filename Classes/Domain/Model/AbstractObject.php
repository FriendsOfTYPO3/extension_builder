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

namespace EBT\ExtensionBuilder\Domain\Model;

use EBT\ExtensionBuilder\Exception\FileNotFoundException;
use EBT\ExtensionBuilder\Exception\SyntaxError;
use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use InvalidArgumentException;
use PhpParser\Error;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract object representing a class, method or property in the context of
 * software development
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
    private array $mapModifierNames = [
        'public' => Class_::MODIFIER_PUBLIC,
        'protected' => Class_::MODIFIER_PROTECTED,
        'private' => Class_::MODIFIER_PRIVATE,
        'static' => Class_::MODIFIER_STATIC,
        'abstract' => Class_::MODIFIER_ABSTRACT,
        'final' => Class_::MODIFIER_FINAL
    ];
    /**
     * @var string|Identifier
     */
    protected $name = '';

    protected string $namespaceName = '';

    /**
     * modifiers (privat, static abstract etc. not to mix up with "isModified" )
     */
    protected ?int $modifiers = null;

    protected string $docComment;

    /**
     * @var string[]
     */
    protected $comments = [];

    protected string $description = '';

    /**
     * @var string[]
     */
    protected array $descriptionLines = [];

    /**
     * @var string[]|array[]
     */
    protected array $tags = [];

    /**
     * this flag is set to true if a modification of an object was detected
     */
    protected bool $isModified = false;

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Getter for name
     *
     * @return string|Identifier
     */
    public function getName()
    {
        return $this->name;
    }

    public function getQualifiedName(): string
    {
        return $this->getNamespaceName() . '\\' . $this->getName();
    }

    /**
     * Checks if the doc comment of this method is tagged with
     * the specified tag
     *
     * @param string $tagName : Tag name to check for
     * @return bool true if such a tag has been defined, otherwise false
     */
    public function isTaggedWith(string $tagName): bool
    {
        return isset($this->tags[$tagName]);
    }

    /**
     * Returns an array of tags and their values
     *
     * @return array Tags and values
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function hasTags(): bool
    {
        return count($this->getTags()) > 0;
    }

    /**
     * sets the array of tags
     * @param string[] tags
     *
     * @return $this;
     */
    public function setTags(array $tags): self
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
    public function setTag(string $tagName, $tagValue = '', bool $override = true): self
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
     */
    public function removeTag(string $tagName): void
    {
        //TODO: multiple tags with same tag name must be possible (param etc.)
        unset($this->tags[$tagName]);
    }

    /**
     * Returns the values of the specified tag
     * @param string $tagName
     * @return array|string Values of the given tag
     */
    public function getTagValues(string $tagName)
    {
        if (!$this->isTaggedWith($tagName)) {
            throw new InvalidArgumentException('Tag "' . $tagName . '" does not exist.', 1337645712);
        }
        return $this->tags[$tagName];
    }

    /**
     * Returns the value of the specified tag
     *
     * @param string $tagName
     * @return string Value of the given tag
     */
    public function getTagValue(string $tagName): string
    {
        $tagValues = $this->getTagValues($tagName);
        if (is_array($tagValues) && count($tagValues) > 1) {
            throw new InvalidArgumentException('Tag "' . $tagName . '" has multiple values.');
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
    public function getAnnotations(): array
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get description lines as array
     * used by fluid in templates
     *
     * @return string[] Property description
     */
    public function getDescriptionLines(): array
    {
        return GeneralUtility::trimExplode(PHP_EOL, trim($this->getDescription()));
    }

    /**
     * set description lines as array
     * this enables more control for line length and line breaks
     * @param string[] $descriptionLines
     */
    public function setDescriptionLines(array $descriptionLines): void
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
    public function setDescription(string $description): self
    {
        $this->description = $description;
        $this->descriptionLines = explode(PHP_EOL, wordwrap($description, 80, PHP_EOL));
        return $this;
    }

    public function hasDescription(): bool
    {
        return !empty($this->description);
    }

    /**
     * Setter for modifiers (will set all modifiers at once,
     * since modifiers are claculated bitwise)
     *
     * @param int $modifiers modifiers
     * @return $this (for fluid interface)
     */
    public function setModifiers(int $modifiers): self
    {
        $this->modifiers = $modifiers;
        return $this;
    }

    /**
     * adds a modifier
     *
     * @param string $modifierName
     *
     * @return $this (for fluid interface)
     * @throws FileNotFoundException
     * @throws SyntaxError
     */
    public function addModifier(string $modifierName): self
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
     * @return $this (for fluid interface)
     * @throws FileNotFoundException
     * @throws SyntaxError
     */
    public function setModifier(string $modifierName): self
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

    public function removeModifier(string $modifierName): self
    {
        $this->modifiers ^= $this->mapModifierNames[$modifierName];
        return $this;
    }

    public function removeAllModifiers(): self
    {
        $this->modifiers = 0;
        return $this;
    }

    public function getModifiers(): ?int
    {
        return $this->modifiers;
    }

    public function getModifierNames(): array
    {
        $modifiers = $this->getModifiers();
        return NodeConverter::modifierToNames($modifiers);
    }

    /**
     * validate if the modifier can be added to the current modifiers or not
     *
     * @param int $modifier
     * @throws FileNotFoundException
     * @throws SyntaxError
     */
    protected function validateModifier(int $modifier): void
    {
        if (($modifier === Class_::MODIFIER_FINAL && $this->isAbstract())
            || ($modifier === Class_::MODIFIER_ABSTRACT && $this->isFinal())
        ) {
            throw new SyntaxError('Abstract and Final can\'t be applied both to same object');
        }

        if (($modifier === Class_::MODIFIER_STATIC && $this->isAbstract())
            || ($modifier === Class_::MODIFIER_ABSTRACT && $this->isStatic())
        ) {
            throw new FileNotFoundException('Abstract and Static can\'t be applied both to same object');
        }

        try {
            Class_::verifyModifier($this->modifiers, $modifier);
        } catch (Error $e) {
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
     */
    public function setDocComment(string $docComment): void
    {
        $lines = explode(chr(10), $docComment);
        foreach ($lines as $index => $line) {
            // extract description & tags from comment text
            $line = preg_replace('/(\\s*\\*\\/\\s*)?$/', '', $line);
            $line = trim($line);
            if ($line === '*/') {
                break;
            }
            if ($line !== '' && strpos($line, '* @') !== false) {
                $this->parseTag(substr($line, strpos($line, '@')));
            } else {
                $plainLine = preg_replace('/\\s*\\/?[\\\\*]*\\s?(.*)$/', '$1', $line);
                // add line to description if:
                // no tag yet (tags should be placed below text)
                // and line is not empty
                // or line is empty and not first or last line
                if (count($this->tags) === 0
                    && (!empty($plainLine) || (!empty($this->description) && $index !== count($lines) - 1))
                ) {
                    $this->description .= $plainLine . PHP_EOL;
                }
            }
        }
        $this->description = trim($this->description);
        $this->descriptionLines = explode(PHP_EOL, $this->description);
    }

    /**
     * Parses a line of a doc comment for a tag and its value.
     * The result is stored in the internal tags array.
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

    /**
     * Getter for docComment
     *
     * render a docComment string, based on description and tags
     *
     * @return string
     */
    public function getDocComment(): string
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
                } elseif (is_array($tags)) {
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
        $docCommentLines = array_reduce($docCommentLines, static function (array $acc, string $item) {
            $c = count($acc);
            if ($c > 1 && empty($item) && empty($acc[$c-1])) {
                // skip second empty line
            } else {
                $acc[] = $item;
            }
            return $acc;
        }, []);
        array_walk($docCommentLines, static function (&$line): void {
            $line = empty($line) ? ' *' : ' * ' . $line;
        });
        return '/**' . PHP_EOL . implode(PHP_EOL, $docCommentLines) . PHP_EOL . ' */';
    }

    /**
     * is there a docComment
     *
     * @return bool
     */
    public function hasDocComment(): bool
    {
        return !empty($this->docComment);
    }

    public function addComment(string $commentText): void
    {
        // parsed comments have no line at the end
        // generated comments have
        $lastChar = substr($commentText, -1);
        if ($lastChar !== PHP_EOL) {
            $commentText .= PHP_EOL;
        }
        $this->comments[] = $commentText;
    }

    /**
     * @return string[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    public function setIsModified(bool $isModified): void
    {
        $this->isModified = $isModified;
    }

    public function getIsModified(): bool
    {
        return $this->isModified;
    }

    public function setNamespaceName(string $namespaceName): self
    {
        $this->namespaceName = $namespaceName;
        return $this;
    }

    public function getNamespaceName(): string
    {
        return $this->namespaceName;
    }

    public function isNamespaced(): bool
    {
        return !empty($this->namespaceName);
    }

    public function isPublic(): bool
    {
        return ($this->modifiers & Class_::MODIFIER_PUBLIC) !== 0;
    }

    public function isProtected(): bool
    {
        return ($this->modifiers & Class_::MODIFIER_PROTECTED) !== 0;
    }

    public function isPrivate(): bool
    {
        return ($this->modifiers & Class_::MODIFIER_PRIVATE) !== 0;
    }

    public function isStatic(): bool
    {
        return ($this->modifiers & Class_::MODIFIER_STATIC) !== 0;
    }

    public function isAbstract(): bool
    {
        return ($this->modifiers & Class_::MODIFIER_ABSTRACT) !== 0;
    }

    public function isFinal(): bool
    {
        return ($this->modifiers & Class_::MODIFIER_FINAL) !== 0;
    }
}
