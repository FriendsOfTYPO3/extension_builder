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

namespace EBT\ExtensionBuilder\Parser\Visitor;

use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * a generic visitor to replace node properties in statements
 * (Usage see: Tests/Function/ModifyObjects renameMethodParameterAndUpdateMethodBody)
 */
class ReplaceVisitor extends NodeVisitorAbstract
{
    protected ?array $nodeTypes = [];
    protected string $nodeProperty = '';
    protected array $replacements = [];

    /**
     * @param Node $node
     * @return Node
     */
    public function leaveNode(Node $node)
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
                    $nodePropertyValue = NodeConverter::getPropertyValueFromNode($node, $nodeProperty);
                    //$nodePropertyValue = $node->$nodeProperty;
                    if ($nodePropertyValue == $oldValue) {
                        $node->$nodeProperty = $newValue;
                    }
                }
            }
            return $node;
        }
        return $node;
    }

    /**
     * @param array $replacements $oldValue => $newValue
     * @return ReplaceVisitor
     */
    public function setReplacements(array $replacements): self
    {
        $this->replacements = $replacements;
        return $this;
    }

    /**
     * The property of a node that should be changed (defaults to 'name')
     * @param string $nodeProperty
     * @return ReplaceVisitor
     */
    public function setNodeProperty(string $nodeProperty): self
    {
        $this->nodeProperty = $nodeProperty;
        return $this;
    }

    /**
     * @param array|null $nodeTypes
     * @return ReplaceVisitor
     */
    public function setNodeTypes(?array $nodeTypes): self
    {
        $this->nodeTypes = $nodeTypes;
        return $this;
    }
}
