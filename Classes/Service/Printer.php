<?php
namespace EBT\ExtensionBuilder\Service;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Nico de Haen <mail@ndh-websolutions.de>
 *  All rights reserved
 *
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

if (!class_exists('PHPParser_PrettyPrinter_Default')) {
	\EBT\ExtensionBuilder\Parser\AutoLoader::register();
}

/**
 * provides methods to render the sourcecode for statements
 */
class Printer extends \PHPParser_PrettyPrinter_Default {
	/**
	 * @var \EBT\ExtensionBuilder\Parser\NodeFactory
	 */
	protected $nodeFactory = NULL;

	/**
	 * @var bool
	 */
	protected $canUseSemicolonNamespaces = TRUE;

	/**
	 * @var string
	 */
	protected $indentToken = TAB;

	/**
	 * @param \EBT\ExtensionBuilder\Parser\NodeFactory $nodeFactory
	 */
	public function injectNodeFactory(\EBT\ExtensionBuilder\Parser\NodeFactory $nodeFactory) {
		$this->nodeFactory = $nodeFactory;
	}

	/**
	 * @param array $stmts
	 * @return string
	 */
	public function render($stmts) {
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
	public function renderClassObject(\EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject $classObject) {
		$stmts = $this->nodeFactory->buildClassNode($classObject);
		return $this->render($stmts);
	}

	/**
	 * @param \EBT\ExtensionBuilder\Domain\Model\File
	 * @param bool $prependPHPTag
	 * @return string
	 */
	public function renderFileObject(\EBT\ExtensionBuilder\Domain\Model\File $fileObject, $prependPHPTag = FALSE) {
		$stmts = $this->nodeFactory->getFileStatements($fileObject);
		$resultingCode = $this->render($stmts);
		if ($prependPHPTag) {
			return '<?php' . LF . $resultingCode;
		} else {
			return $resultingCode;
		}
	}

	/**
	 * @param array $stmts
	 * @param string $needle
	 *
	 * @return bool|int
	 */
	public function statementsContainString(array $stmts, $needle) {
		$haystack = $this->render($stmts);
		return strpos($haystack, $needle);
	}

	// override printerService functions according to TYPO3 CGL

	/**
	 * Pretty prints an array of nodes (statements) and indents them optionally.
	 *
	 * @param \PHPParser_Node[] $nodes Array of nodes
	 * @param bool $indent Whether to indent the printed nodes
	 *
	 * @return string Pretty printed statements
	 */
	protected function pStmts(array $nodes, $indent = TRUE) {
		$pNodes = array();
		foreach ($nodes as $node) {
			$pNodes[] = $this->pComments($node->getAttribute('comments', array())) .
				$this->p($node) .
				($node instanceof \PHPParser_Node_Expr ? ';' : '');
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

	public function pStmt_Interface(\PHPParser_Node_Stmt_Interface $node) {
		return 'interface ' . $node->name .
			(!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '') .
			'{' . LF . $this->pStmts($node->stmts) . LF . '}';
	}

	public function pStmt_Class(\PHPParser_Node_Stmt_Class $node) {
		return $this->pModifiers($node->type) .
			'class ' . $node->name .
			(NULL !== $node->extends ? ' extends ' . $this->p($node->extends) : '') .
			(!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '') .
			' {' . LF . LF . $this->pStmts($node->stmts) . LF . '}';
	}

	public function pStmt_ClassConst(\PHPParser_Node_Stmt_ClassConst $node) {
		return 'const ' . $this->pCommaSeparated($node->consts) . ';' . LF;
	}

	public function pStmt_Property(\PHPParser_Node_Stmt_Property $node) {
		return $this->pModifiers($node->type) . $this->pCommaSeparated($node->props) . ';' . LF;
	}

	public function pStmt_ClassMethod(\PHPParser_Node_Stmt_ClassMethod $node) {
		$firstToken = '';
		$lastToken = '';
		if (count($node->params) > 0) {
			if ($node->getAttribute('startLine') != reset($node->params)->getAttribute('startLine')) {
				$firstToken = LF .  $this->indentToken;
			}
			// if the last parameters endline is 2 lines above the first statements
			// startLine, the closing bracket is in a new line (except if there is a comment)
			if ($this->getFirstLineOfMoethodBody($node->stmts) - end($node->params)->getAttribute('endLine') > 1) {
				$lastToken = LF;
			}
		}
		return $this->pModifiers($node->type) .
			'function ' . ($node->byRef ? '&' : '') . $node->name .
			'(' . $firstToken . $this->pParameterNodes($node->params) . $lastToken . ')' .
			(NULL !== $node->stmts
				? ' {' . LF . $this->pStmts($node->stmts) . LF . '}' . LF
				: ';');
	}

	/**
	 *
	 * @param array $stmts
	 * @return int
	 */
	protected function getFirstLineOfMoethodBody(array $stmts) {
		if (count($stmts) > 0) {
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

	public function pExpr_FuncCall(\PHPParser_Node_Expr_FuncCall $node) {
		$firstToken = '';
		$lastToken = '';
		if (count($node->args) > 0) {
			if ($node->getAttribute('startLine') != reset($node->args)->getAttribute('startLine')) {
				$firstToken = LF .  $this->indentToken;
			}
			if ($node->getAttribute('startLine') != end($node->args)->getAttribute('startLine')) {
				$lastToken = LF;
			}
		}
		return $this->p($node->name) . '(' . $firstToken . $this->pParameterNodes($node->args) . $lastToken. ')';
	}

	public function pExpr_MethodCall(\PHPParser_Node_Expr_MethodCall $node) {
		$firstToken = '';
		$lastToken = '';
		if (count($node->args) > 0) {
			if ($node->getAttribute('startLine') != reset($node->args)->getAttribute('startLine')) {
				$firstToken = LF .  $this->indentToken;
			}
			if ($node->getAttribute('startLine') != end($node->args)->getAttribute('startLine')) {
				$lastToken = LF;
			}
		}
		return $this->pVarOrNewExpr($node->var) . '->' . $this->pObjectProperty($node->name) .
			'(' . $firstToken . $this->pParameterNodes($node->args) . $lastToken . ')';
	}

	public function pExpr_StaticCall(\PHPParser_Node_Expr_StaticCall $node) {
		$firstToken = '';
		$lastToken = '';
		if (count($node->args) > 0) {
			if ($node->getAttribute('startLine') != reset($node->args)->getAttribute('startLine')) {
				$firstToken = LF .  $this->indentToken;
			}
			if ($node->getAttribute('startLine') != end($node->args)->getAttribute('startLine')) {
				$lastToken = LF;
			}
		}
		return $this->p($node->class) . '::' .
			($node->name instanceof \PHPParser_Node_Expr
			? ($node->name instanceof \PHPParser_Node_Expr_Variable
			|| $node->name instanceof \PHPParser_Node_Expr_ArrayDimFetch
			? $this->p($node->name)
			: '{' . $this->p($node->name) . '}')
			: $node->name) .
			'(' . $firstToken . $this->pParameterNodes($node->args) . $lastToken . ')';
	}

	/**
	* Pretty prints an array of nodes and implodes the printed values with commas.
	*
	* @param \PHPParser_Node[] $nodes Array of Nodes to be printed
	*
	* @return string Comma separated pretty printed nodes
	*/
	protected function pParameterNodes(array $nodes) {
		$startLine = '';

		$multiLine = FALSE;
		if (isset($nodes[0]) && $nodes[0]->hasAttribute('startLine')) {
			$startLine = reset($nodes)->getAttribute('startLine');
			$endLine = end($nodes)->getAttribute('endLine');
			if ($startLine != $endLine) {
				$multiLine = TRUE;
			}
		}
		if (!$multiLine) {
			return parent::pCommaSeparated($nodes);
		}
		$printedNodes = '';
		foreach ($nodes as $node) {
			$glueToken = ", ";
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

	public function pStmt_Function(\PHPParser_Node_Stmt_Function $node) {
		return 'function ' . ($node->byRef ? '&' : '') . $node->name .
			'(' . $this->pParameterNodes($node->params) . ')' .
			' {' . LF . $this->pStmts($node->stmts) . LF . '}' . LF;
	}

	/**
	 * @param \PHPParser_Comment[] $comments
	 * @return string
	 */
	protected function pComments(array $comments) {
		$result = '';

		foreach ($comments as $comment) {
			$content = preg_replace('/(\\*|\\/|\\s)/', '', $comment->getText());
			if (empty($content)) {
				// don't output empty comments
				continue;
			}
			$result .= $comment->getReformattedText() . LF;
			if (!$comment instanceof \PHPParser_Comment_Doc &&
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
	public function pExpr_Array(\PHPParser_Node_Expr_Array $node) {
		$multiLine = FALSE;
		$startLine = $node->getAttribute('startLine');
		$endLine = $node->getAttribute('endLine');
		if ($startLine != $endLine) {
			$multiLine = TRUE;
		}
		$printedNodes = '';
		foreach ($node->items as $itemNode) {
			$glueToken = ", ";
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
			return  'array(' . LF . $multiLinedItems . LF . ')';
		} else {
			return parent::pExpr_Array($node);
		}
	}

	public function pStmt_Namespace(\PHPParser_Node_Stmt_Namespace $node) {
		if ($this->canUseSemicolonNamespaces) {
			return 'namespace ' . $this->p($node->name) . ';' . LF . $this->pStmts($node->stmts, FALSE);
		} else {
		return 'namespace' . (NULL !== $node->name ? ' ' . $this->p($node->name) : '') .
			' {' . LF . $this->pStmts($node->stmts) . LF . '}';
		}
	}

	public function pExpr_Include(\PHPParser_Node_Expr_Include $node) {
		static $map = array(
			\PHPParser_Node_Expr_Include::TYPE_INCLUDE      => 'include',
			\PHPParser_Node_Expr_Include::TYPE_INCLUDE_ONCE => 'include_once',
			\PHPParser_Node_Expr_Include::TYPE_REQUIRE      => 'require',
			\PHPParser_Node_Expr_Include::TYPE_REQUIRE_ONCE => 'require_once',
		);
		return $map[$node->type] . '(' . $this->p($node->expr) . ')';
	}
}
