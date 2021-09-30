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

use EBT\ExtensionBuilder\Parser\NodeFactory;
use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * replaces all occurances of new "className" and static class calls like "className::"
 */
class ReplaceClassNamesVisitor extends NodeVisitorAbstract
{
    protected string $nodeType = '';
    protected string $nodeProperty = '';
    protected string $oldClassPrefix = '';
    protected string $newClassPrefix = '';

    /**
     * @param Node $node
     * @return Node
     */
    public function leaveNode(Node $node)
    {
        if (null !== $node->__get('class')) {
            $oldClassName = NodeConverter::getValueFromNode($node->__get('class'));
            if (strpos($oldClassName, $this->oldClassPrefix) !== false) {
                $newClassName = str_replace($this->oldClassPrefix, $this->newClassPrefix, $oldClassName);
                $node->setClass(NodeFactory::buildNodeFromName($newClassName));
                return $node;
            }
        }
        return $node;
    }

    public function beforeTraverse(array $nodes): void
    {
    }

    public function enterNode(Node $node): void
    {
    }

    public function afterTraverse(array $nodes): void
    {
    }

    public function setNewClassPrefix($newClassPrefix): void
    {
        $this->newClassPrefix = $newClassPrefix;
    }

    public function setOldClassPrefix($oldClassPrefix): void
    {
        $this->oldClassPrefix = $oldClassPrefix;
    }
}
