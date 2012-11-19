<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * abstract object representing a class, method or property in the context of
 * software development
 *
 * @version $ID:$
 */
abstract class Tx_ExtensionBuilder_Domain_Model_Class_AbstractObject {

	/**
	 * 1	ReflectionMethod::IS_STATIC
	 * 2	ReflectionMethod::IS_ABSTRACT
	 * 4	ReflectionMethod::IS_FINAL
	 * 256	ReflectionMethod::IS_PUBLIC
	 * 512	ReflectionMethod::IS_PROTECTED
	 * 1024	ReflectionMethod::IS_PRIVATE
	 */
	private $mapModifierNames = array(
		'static' => ReflectionMethod::IS_STATIC,
		'abstract' => ReflectionMethod::IS_ABSTRACT,
		'final' => ReflectionMethod::IS_FINAL,
		'public' => ReflectionMethod::IS_PUBLIC,
		'protected' => ReflectionMethod::IS_PROTECTED,
		'private' => ReflectionMethod::IS_PRIVATE

	);

	/**
	 * name
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $nameSpace;

	/**
	 * modifiers  (privat, static abstract etc. not to mix up with "isModified" )
	 * @var array
	 */
	protected $modifiers = array();

	/**
	 * @var array An array of tag names and their values (multiple values are possible)
	 */
	protected $tags = array();

	/**
	 * docComment
	 * @var string
	 */
	protected $docComment;

	/**
	 * precedingBlock
	 * all lines that were found above the declaration of the current element
	 *
	 * @var string
	 */
	protected $precedingBlock;

	/**
	 * isModified (this flag is set to TRUE, if a modification of a class was detected)
	 * @var string
	 */
	protected $isModified;

	/**
	 * Description of property
	 * @var string
	 */
	protected $description;

	/**
	 * Setter for name
	 *
	 * @param string $name name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Getter for name
	 *
	 * @return string name
	 */
	public function getName() {
		return $this->name;
	}

	public function getQualifiedName() {
		return $this->getNameSpace() . '\\' . $this->getName();
	}

	/**
	 * Checks if the doc comment of this method is tagged with
	 * the specified tag
	 *
	 * @param  string $tag: Tag name to check for
	 * @return boolean TRUE if such a tag has been defined, otherwise FALSE
	 */
	public function isTaggedWith($tagName) {
		return in_array($tagName, array_keys($this->tags));
	}

	/**
	 * Returns an array of tags and their values
	 *
	 * @return array Tags and values
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 *
	 * @return
	 */
	public function getAnnotations() {
		$annotations = array();
		$tagNames = array_keys($this->tags);
		foreach ($tagNames as $tagName) {
			if (empty($this->tags[$tagName])) {
				$annotations[] = $tagName;
			} elseif (is_array($this->tags[$tagName])) {
				foreach ($this->tags[$tagName] as $tagValue) {
					$annotations[] = $tagName . ' ' . $tagValue;
				}
				//$tagValue = implode(' ',$tagValue);
			}
			else {
				$annotations[] = $tagName . ' ' . $this->tags[$tagName];
			}
		}
		return $annotations;
	}

	/**
	 * sets the array of tags
	 *
	 * @return array Tags and values
	 */
	public function setTags($tags) {
		$this->tags = $tags;
	}

	/**
	 * sets a tags
	 *
	 * @param string $tagName
	 * @param mixed $tagValue
	 * @return void
	 */
	public function setTag($tagName, $tagValue, $override = TRUE) {
		if (!$override && isset($this->tags[$tagName])) {
			if (!is_array($this->tags[$tagName])) {
				// build an array with the existing value as first element
				$this->tags[$tagName] = array($this->tags[$tagName]);
			}
			$this->tags[$tagName][] = $tagValue;
		}
		else {
			$this->tags[$tagName] = $tagValue;
		}
	}

	/**
	 * unsets a tags
	 *
	 * @param string $tagName
	 * @return void
	 */
	public function removeTag($tagName) {
		//TODO: multiple tags with same tagname must be possible (param etc.)
		unset($this->tags[$tagName]);
	}

	/**
	 * Get property description to be used in comments
	 *
	 * @return string Property description
	 */
	public function getDescription() {
		$test = str_replace('/', '', trim($this->description));
		if (empty($this->description) || empty($test)) {
			return $this->name;
		}
		return $this->description;
	}

	/**
	 * Get property description lines as array
	 *
	 * @return string Property description
	 */
	public function getDescriptionLines() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", trim($this->getDescription()));
	}

	/**
	 * Set property description
	 *
	 * @param string $description Property description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 *
	 *
	 * @return boolean TRUE if the description isn't empty
	 */
	public function hasDescription() {
		if (empty($this->description)) {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Returns the values of the specified tag
	 * @return array Values of the given tag
	 */
	public function getTagsValues($tagName) {
		return $this->tags[$tagName];
	}

	/**
	 * Setter for modifiers
	 *
	 * @param string $modifiers modifiers
	 * @return void
	 */
	public function setModifiers($modifiers) {
		if (is_array($modifiers)) {
			$this->modifiers = $modifiers;
		} else {
			$this->modifiers = explode(' ', $modifiers);
		}
	}

	/**
	 * adds a modifier
	 *
	 * @param string $modifiers
	 * @return void
	 */
	public function addModifier($modifier) {
		if (!is_numeric($modifier)) {
			$modifier = $this->mapModifierNames[$modifier];
		}
		if (!in_array($modifier, $this->modifiers)) {
			$this->modifiers[] = $modifier;
		}
	}


	/**
	 * Getter for modifiers
	 *
	 * @return int modifiers
	 */
	public function getModifiers() {
		return $this->modifiers;
	}

	public function getModifierNames() {
		$modifiers = $this->getModifiers();
		$modifierNames = array();
		if (is_array($modifiers)) {
			foreach ($modifiers as $modifier) {
				$modifierNames[] = array_shift(Reflection::getModifierNames($modifier));
			}
		}
		else {
			$modifierNames = Reflection::getModifierNames($modifiers);
		}
		return $modifierNames;
	}


	/**
	 * Setter for docComment
	 *
	 * @param string $docComment docComment
	 * @return void
	 */
	public function setDocComment($docComment) {
		$this->docComment = $docComment;
	}

	/**
	 * Getter for docComment
	 *
	 * @return string docComment
	 */
	public function getDocComment() {
		return $this->docComment;
	}

	/**
	 * is there a docComment
	 *
	 * @return boolean
	 */
	public function hasDocComment() {
		if (!empty($this->docComment)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Setter for precedingBlock
	 *
	 * @param string $precedingBlock precedingBlock
	 * @return void
	 */
	public function setPrecedingBlock($precedingBlock) {
		$this->precedingBlock = $precedingBlock;
	}

	/**
	 * Getter for precedingBlock
	 *
	 * @return string precedingBlock
	 */
	public function getPrecedingBlock() {
		$cleanPrecedingBlock = str_replace($this->docComment, '', $this->precedingBlock);
		$cleanPrecedingBlock = str_replace('<?php', '', $cleanPrecedingBlock);
		if (strlen(trim($cleanPrecedingBlock)) == 0) {
			return NULL;
		} else {
			return $cleanPrecedingBlock;
		}
	}

	/**
	 * Setter for isModified
	 *
	 * @param string $isModified isModified
	 * @return void
	 */
	public function setIsModified($isModified) {
		$this->isModified = $isModified;
	}

	/**
	 * Getter for isModified
	 *
	 * @return string isModified
	 */
	public function getIsModified() {
		return $this->isModified;
	}

	/**
	 * @param string $nameSpace
	 */
	public function setNameSpace($nameSpace) {
		$this->nameSpace = $nameSpace;
	}

	/**
	 * @return string
	 */
	public function getNameSpace() {
		return $this->nameSpace;
	}

}

?>