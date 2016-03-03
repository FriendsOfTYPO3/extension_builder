<?php
namespace EBT\ExtensionBuilder\Parser\Visitor;

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
 *
 * a generic visitor to replace node properties in statements
 * (Usage see: Tests/Function/ModifyObjects renameMethodParameterAndUpdateMethodBody)
 *
 */
class ReplaceVisitor extends \PhpParser\NodeVisitorAbstract
{
    /**
     * @var array
     */
    protected $nodeTypes = array();
    /**
     * @var string
     */
    protected $nodeProperty = '';
    /**
     * @var array
     */
    protected $replacements = array();

    /**
     * @param \PhpParser\Node $node
     * @return \PhpParser\Node|void
     */
    public function leaveNode(\PhpParser\Node $node)
    {
        $nodeProperty = $this->nodeProperty;
        $nodeTypeMatch = false;
        if (!empty($this->nodeTypes)) {
            if (in_array($node->getType(), $this->nodeTypes)) {
                $nodeTypeMatch = true;
            }
        } else {
            // no nodeType so apply conditions to all node types
            $nodeTypeMatch = true;
        }
        if ($nodeTypeMatch) {
            foreach ($this->replacements as $oldValue => $newValue) {
                if (property_exists($node, $nodeProperty)) {
                    $nodePropertyValue = $node->$nodeProperty;
                    if ($nodePropertyValue == $oldValue) {
                        $node->$nodeProperty = $newValue;
                    }
                }
            }
            return $node;
        }
    }

    /**
     * @param array $replacements $oldValue => $newValue
     * @return \EBT\ExtensionBuilder\Parser\Visitor\ReplaceVisitor
     */
    public function setReplacements(array $replacements)
    {
        $this->replacements = $replacements;
        return $this;
    }

    /**
     * The property of a node that should be changed (defaults to 'name')
     * @param $nodeProperty
     * @return \EBT\ExtensionBuilder\Parser\Visitor\ReplaceVisitor
     */
    public function setNodeProperty($nodeProperty)
    {
        $this->nodeProperty = $nodeProperty;
        return $this;
    }

    /**
     * @param array $nodeTypes
     * @return \EBT\ExtensionBuilder\Parser\Visitor\ReplaceVisitor
     */
    public function setNodeTypes($nodeTypes)
    {
        $this->nodeTypes = $nodeTypes;
        return $this;
    }
}
