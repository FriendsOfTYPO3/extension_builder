<?php
namespace EBT\ExtensionBuilder\Service;

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

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Parser\NodeFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * provides methods to render the sourcecode for statements
 */
class Printer extends Standard
{
    /**
     * @var \EBT\ExtensionBuilder\Parser\NodeFactory
     */
    protected $nodeFactory = null;
    /**
     * @var bool
     */
    protected $canUseSemicolonNamespaces = true;
    /**
     * @var string
     */
    protected $indentToken = '    ';

    /**
     * @param \EBT\ExtensionBuilder\Parser\NodeFactory $nodeFactory
     */
    public function injectNodeFactory(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * @param array $stmts
     * @return string
     */
    public function render($stmts)
    {
        if (!is_array($stmts)) {
            $stmts = [$stmts];
        }
        return $this->prettyPrint($stmts);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\File
     * @param bool $prependPHPTag
     * @return string
     */
    public function renderFileObject(File $fileObject, $prependPHPTag = false)
    {
        $stmts = $this->nodeFactory->getFileStatements($fileObject);
        $resultingCode = $this->render($stmts);
        if ($prependPHPTag) {
            return '<?php' . LF . $resultingCode . LF;
        } else {
            return $resultingCode . LF;
        }
    }

    /**
     * add a new line before each comment
     *
     * @param array $comments
     * @return string
     */
    protected function pComments(array $comments) : string {
        return $this->nl . parent::pComments($comments);
    }

    /**
     * print an associative array
     * with support for multi line notation
     *
     * @param \PhpParser\Node\Expr\Array_
     */
    public function pExpr_Array(\PhpParser\Node\Expr\Array_ $node)
    {
        $multiLine = false;
        $startLine = $node->getAttribute('startLine');
        $endLine = $node->getAttribute('endLine');
        if ($startLine != $endLine) {
            $multiLine = true;
        }
        $printedNodes = '';
        foreach ($node->items as $itemNode) {
            $glueToken = ', ';
            if ($itemNode->getAttribute('startLine') != $startLine) {
                $glueToken = ',' . LF;
                $startLine = $itemNode->getAttribute('startLine');
            }
            if (!empty($printedNodes)) {
                $printedNodes .= $glueToken . $this->p($itemNode);
            } else {
                $printedNodes .= $this->p($itemNode);
            }
        }
        if ($multiLine) {
            $multiLinedItems = $this->indentToken . preg_replace(
                    '~\\n(?!$|' . $this->noIndentToken . ')~',
                    LF . $this->indentToken,
                    $printedNodes
                );
            return '[' . LF . $multiLinedItems . LF . ']';
        } else {
            return '[' . $this->pCommaSeparated($node->items) . ']';
        }
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pCommaSeparated(array $nodes) : string {
        $multiline = false;
        if (!empty($nodes)) {
            $startLine = reset($nodes)->getAttribute('startLine');
            $endLine = end($nodes)->getAttribute('endLine');
            if ($startLine != $endLine) {
                $multiline = true;
            }
        }
        if ($multiline) {
            return $this->nl . $this->pImplode($nodes, ', ' . $this->nl) . $this->nl;
        } else {
            return $this->pImplode($nodes, ', ');
        }
    }

}
