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

use PhpParser\Node\Stmt;

/**
 * provides methods to render the sourcecode for statements
 */
class Printer extends \PhpParser\PrettyPrinter\Standard
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
    public function injectNodeFactory(\EBT\ExtensionBuilder\Parser\NodeFactory $nodeFactory)
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
            $stmts = array($stmts);
        }
        return $this->prettyPrint($stmts);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     * @return string
     * @return string
     */
    public function renderClassObject(\EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject)
    {
        $stmts = $this->nodeFactory->buildClassNode($classObject);
        return $this->render($stmts);
    }

    /**
     * @param \EBT\ExtensionBuilder\Domain\Model\File
     * @param bool $prependPHPTag
     * @return string
     */
    public function renderFileObject(\EBT\ExtensionBuilder\Domain\Model\File $fileObject, $prependPHPTag = false)
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
     * @param array $stmts
     * @param string $needle
     *
     * @return bool|int
     */
    public function statementsContainString(array $stmts, $needle)
    {
        $haystack = $this->render($stmts);
        return strpos($haystack, $needle);
    }

    // override printerService functions according to TYPO3 CGL

    /**
     * Pretty prints an array of nodes (statements) and indents them optionally.
     *
     * @param \PHPParser\Node[] $nodes Array of nodes
     * @param bool $indent Whether to indent the printed nodes
     *
     * @return string Pretty printed statements
     */
    protected function pStmts(array $nodes, $indent = true)
    {
        $pNodes = array();
        foreach ($nodes as $node) {
            $pNodes[] = $this->pComments($node->getAttribute('comments', array())) .
                $this->p($node) .
                ($node instanceof \PhpParser\Node\Expr ? ';' : '');
        }

        if ($indent) {
            $result = $this->indentToken . preg_replace(
                    '~\\n(?!$|' . $this->noIndentToken . ')~',
                    LF . $this->indentToken,
                    implode(LF, $pNodes)
                );
            // remove spaces in empty lines
            return \preg_replace('/\\n\\s\\n/', LF . LF, $result);
        } else {
            return implode(LF, $pNodes);
        }
    }

    public function pStmt_Interface(Stmt\Interface_ $node)
    {
        return 'interface ' . $node->name .
        (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '') .
        LF . '{' . LF . $this->pStmts($node->stmts) . LF . '}';
    }

    public function pStmt_Class(Stmt\Class_ $node)
    {
        return $this->pModifiers($node->type) .
        'class ' . $node->name .
        (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '') .
        (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '') .
        LF . '{' . LF . $this->pStmts($node->stmts) . '}';
    }

    public function pStmt_ClassConst(Stmt\ClassConst $node)
    {
        return 'const ' . $this->pCommaSeparated($node->consts) . ';' . LF;
    }

    public function pStmt_Property(Stmt\Property $node)
    {
        return $this->pModifiers($node->type) . $this->pCommaSeparated($node->props) . ';' . LF;
    }

    public function pStmt_ClassMethod(Stmt\ClassMethod $node)
    {
        $firstToken = '';
        $lastToken = '';
        if (count($node->params) > 0) {
            if ($node->getAttribute('startLine') != reset($node->params)->getAttribute('startLine')) {
                $firstToken = LF . $this->indentToken;
            }
            // if the last parameters endline is 2 lines above the first statements
            // startLine, the closing bracket is in a new line (except if there is a comment)
            if ($this->getFirstLineOfMethodBody($node->stmts) - end($node->params)->getAttribute('endLine') > 1) {
                //$lastToken = LF;
            }
        }
        return $this->pModifiers($node->type) .
        'function ' . ($node->byRef ? '&' : '') . $node->name .
        '(' . $firstToken . $this->pParameterNodes($node->params) . $lastToken . ')' .
        (null !== $node->stmts
            ? LF . '{' . LF . $this->pStmts($node->stmts) . LF . '}' . LF
            : ';');
    }

    /**
     *
     * @param array $stmts
     * @return int
     */
    protected function getFirstLineOfMethodBody(array $stmts)
    {
        if (count($stmts) < 1) {
            return 0;
        } else {
            $firstNode = reset($stmts);
            $lineNumber = $firstNode->getAttribute('startLine');
            if (is_array($firstNode->getAttribute('comments'))) {
                $lineNumber = reset($firstNode->getAttribute('comments'))->getLine();
            }
            return $lineNumber;
        }
    }

    // Function calls and similar constructs

    public function pExpr_FuncCall(\PhpParser\Node\Expr\FuncCall $node)
    {
        $firstToken = '';
        $lastToken = '';
        if (count($node->args) > 0) {
            if ($node->getAttribute('startLine') != reset($node->args)->getAttribute('startLine')) {
                $firstToken = LF . $this->indentToken;
            }
            if ($node->getAttribute('startLine') != end($node->args)->getAttribute('startLine')) {
                $lastToken = LF;
            }
        }
        return $this->p($node->name) . '(' . $firstToken . $this->pParameterNodes($node->args) . $lastToken . ')';
    }

    public function zzz_pExpr_MethodCall(\PhpParser\Node\Expr\MethodCall $node)
    {
        $firstToken = '';
        $lastToken = '';
        if (count($node->args) > 0) {
            if ($node->getAttribute('startLine') != reset($node->args)->getAttribute('startLine')) {
                $firstToken = LF . $this->indentToken;
            }
            if ($node->getAttribute('startLine') != end($node->args)->getAttribute('startLine')) {
                $lastToken = LF;
            }
        }
        return $this->pExpr_Variable($node->var) . '->' . $this->pObjectProperty($node->name) .
        '(' . $firstToken . $this->pParameterNodes($node->args) . $lastToken . ')';
    }

    public function pExpr_StaticCall(\PhpParser\Node\Expr\StaticCall $node)
    {
        $firstToken = '';
        $lastToken = '';
        if (count($node->args) > 0) {
            if ($node->getAttribute('startLine') != reset($node->args)->getAttribute('startLine')) {
                $firstToken = LF . $this->indentToken;
            }
            if ($node->getAttribute('startLine') != end($node->args)->getAttribute('startLine')) {
                $lastToken = LF;
            }
        }
        return $this->p($node->class) . '::' .
        ($node->name instanceof \PhpParser\Node\Expr
            ? ($node->name instanceof \PhpParser\Node\Expr\Variable
            || $node->name instanceof \PhpParser\Node\Expr\ArrayDimFetch
                ? $this->p($node->name)
                : '{' . $this->p($node->name) . '}')
            : $node->name) .
        '(' . $firstToken . $this->pParameterNodes($node->args) . $lastToken . ')';
    }

    /**
     * Pretty prints an array of nodes and implodes the printed values with commas.
     *
     * @param \PhpParser\Node[] $nodes Array of Nodes to be printed
     *
     * @return string Comma separated pretty printed nodes
     */
    protected function pParameterNodes(array $nodes)
    {
        $startLine = '';

        $multiLine = false;
        if (isset($nodes[0]) && $nodes[0]->hasAttribute('startLine')) {
            $startLine = reset($nodes)->getAttribute('startLine');
            $endLine = end($nodes)->getAttribute('endLine');
            if ($startLine != $endLine) {
                $multiLine = true;
            }
        }
        if (!$multiLine) {
            return parent::pCommaSeparated($nodes);
        }
        $printedNodes = '';
        foreach ($nodes as $node) {
            $glueToken = ', ';
            if ($node->getAttribute('startLine') != $startLine) {
                $glueToken = ',' . LF;
                $startLine = $node->getAttribute('startLine');
            }
            if (!empty($printedNodes)) {
                $printedNodes .= $glueToken . $this->p($node);
            } else {
                $printedNodes .= $this->p($node);
            }
        }
        return preg_replace(
            '~\\n(?!$|' . $this->noIndentToken . ')~',
            LF . $this->indentToken,
            $printedNodes
        );
    }

    public function pStmtFunction(Stmt\Function_ $node)
    {
        return 'function ' . ($node->byRef ? '&' : '') . $node->name .
        '(' . $this->pParameterNodes($node->params) . ')' .
        ' {' . LF . $this->pStmts($node->stmts) . LF . '}' . LF;
    }

    /**
     * @param \PhpParser\Comment[] $comments
     * @return string
     */
    protected function pComments(array $comments)
    {
        $result = '';

        foreach ($comments as $comment) {
            $content = preg_replace('/(\\*|\\/|\\s)/', '', $comment->getText());
            if (empty($content)) {
                // don't output empty comments
                continue;
            }
            $result .= $comment->getReformattedText() . LF;
            if (!$comment instanceof \PhpParser\Comment\Doc &&
                count(explode(LF, $comment->getReformattedText())) > 1
            ) {
                // one blank line after comments except for doc comments and single line comments
                $result .= LF;
            }
        }
        // remove whitespaces at end of lines
        $result = preg_replace('/ +\\n/', LF, $result);
        return $result;
    }

    /**
     * print an associative array
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
            return 'array(' . LF . $multiLinedItems . LF . ')';
        } else {
            return parent::pExpr_Array($node);
        }
    }

    public function pStmt_Namespace(Stmt\Namespace_ $node)
    {
        if ($this->canUseSemicolonNamespaces) {
            return 'namespace ' . $this->p($node->name) . ';' . LF . $this->pStmts($node->stmts, false);
        } else {
            return 'namespace' . (null !== $node->name ? ' ' . $this->p($node->name) : '') .
            ' {' . LF . $this->pStmts($node->stmts) . LF . '}';
        }
    }

    public function pExpr_Include(\PhpParser\Node\Expr\Include_ $node)
    {
        static $map = array(
            \PhpParser\Node\Expr\Include_::TYPE_INCLUDE => 'include',
            \PhpParser\Node\Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once',
            \PhpParser\Node\Expr\Include_::TYPE_REQUIRE => 'require',
            \PhpParser\Node\Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once',
        );
        return $map[$node->type] . '(' . $this->p($node->expr) . ')';
    }

    // Control flow

    public function pStmt_If(Stmt\If_ $node)
    {
        return 'if (' . $this->p($node->cond) . ') {' . LF
        . $this->pStmts($node->stmts) . "\n" . '}'
        . $this->pImplode($node->elseifs)
        . (null !== $node->else ? $this->p($node->else) : '');
    }

    public function pStmt_ElseIf(Stmt\ElseIf_ $node)
    {
        return ' elseif (' . $this->p($node->cond) . ') {' . LF
        . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Else(Stmt\Else_ $node)
    {
        return ' else {' . LF . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_For(Stmt\For_ $node)
    {
        return 'for ('
        . $this->pCommaSeparated($node->init) . ';' . (!empty($node->cond) ? ' ' : '')
        . $this->pCommaSeparated($node->cond) . ';' . (!empty($node->loop) ? ' ' : '')
        . $this->pCommaSeparated($node->loop)
        . ') {' . LF . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Foreach(Stmt\Foreach_ $node)
    {
        return 'foreach (' . $this->p($node->expr) . ' as '
        . (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : '')
        . ($node->byRef ? '&' : '') . $this->p($node->valueVar) . ') {' . LF
        . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_While(Stmt\While_ $node)
    {
        return 'while (' . $this->p($node->cond) . ') {' . LF
        . $this->pStmts($node->stmts) . "\n" . '}';
    }

    public function pStmt_Do(Stmt\Do_ $node)
    {
        return 'do {' . $this->pStmts($node->stmts) . "\n"
        . '} while (' . $this->p($node->cond) . ');';
    }

    public function pStmt_Switch(Stmt\Switch_ $node)
    {
        return 'switch (' . $this->p($node->cond) . ') {' . LF
        . $this->pStmts($node->cases) . "\n" . '}';
    }
}
