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
 * replaces all occurances of new "className" and static class calls like "className::"
 *
 */
class ReplaceClassNamesVisitor extends \PhpParser\NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected $nodeType = '';
    /**
     * @var string
     */
    protected $nodeProperty = '';
    /**
     * @var string
     */
    protected $oldClassPrefix = '';
    /**
     * @var string
     */
    protected $newClassPrefix = '';

    /**
     * @param \PhpParser\Node $node
     * @return \PhpParser\Node|void
     */
    public function leaveNode(\PhpParser\Node $node)
    {
        if (null !== $node->__get('class')) {
            $oldClassName = \EBT\ExtensionBuilder\Parser\Utility\NodeConverter::getValueFromNode($node->__get('class'));
            if (strpos($oldClassName, $this->oldClassPrefix) !== false) {
                $newClassName = str_replace($this->oldClassPrefix, $this->newClassPrefix, $oldClassName);
                $node->setClass(\EBT\ExtensionBuilder\Parser\NodeFactory::buildNodeFromName($newClassName));
                return $node;
            }
        }
    }

    public function beforeTraverse(array $nodes)
    {
    }

    public function enterNode(\PhpParser\Node $node)
    {
    }

    public function afterTraverse(array $nodes)
    {
    }

    public function setNewClassPrefix($newClassPrefix)
    {
        $this->newClassPrefix = $newClassPrefix;
    }

    public function setOldClassPrefix($oldClassPrefix)
    {
        $this->oldClassPrefix = $oldClassPrefix;
    }
}
