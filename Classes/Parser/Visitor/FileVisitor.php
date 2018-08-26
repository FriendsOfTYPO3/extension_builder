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

use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * provides methods to import a class object and methods and properties
 *
 */
class FileVisitor extends NodeVisitorAbstract implements FileVisitorInterface
{
    /**
     * @var array
     */
    protected $properties = [];
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject
     */
    protected $currentClassObject = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\NamespaceObject
     */
    protected $currentNamespace = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\Container
     */
    protected $currentContainer = null;
    /**
     * @var \EBT\ExtensionBuilder\Domain\Model\File
     */
    protected $fileObject = null;
    /**
     * @var \EBT\ExtensionBuilder\Parser\ClassFactoryInterface
     */
    protected $classFactory = null;
    /**
     * @var bool
     */
    protected $onFirstLevel = true;
    /**
     * currently not used, might be useful for filtering etc.
     * it keeps a reference to the current "first level" node
     *
     * @var array
     */
    protected $contextStack = [];
    /**
     * @var \PhpParser\Node
     */
    protected $lastNode = null;

    public function getFileObject()
    {
        return $this->fileObject;
    }

    /**
     *
     *
     * @param \PhpParser\Node $node
     */
    public function enterNode(Node $node)
    {
        $this->contextStack[] = $node;
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentNamespace = $this->classFactory->buildNamespaceObject($node);
            $this->currentContainer = $this->currentNamespace;
        } elseif ($node instanceof Node\Stmt\Class_) {
            $this->currentClassObject = $this->classFactory->buildClassObject($node);
            $this->currentContainer = $this->currentClassObject;
        }
    }

    /**
     * @param \PhpParser\Node $node
     */
    public function leaveNode(Node $node)
    {
        array_pop($this->contextStack);
        if ($this->isContainerNode(end($this->contextStack)) || count($this->contextStack) === 0) {
            // we are on the first level
            if ($node instanceof Node\Stmt\Class_) {
                if (count($this->contextStack) > 0) {
                    if (end($this->contextStack)->getType() == 'Stmt_Namespace') {
                        $currentNamespaceName = NodeConverter::getValueFromNode(end($this->contextStack));
                        $this->currentClassObject->setNamespaceName($currentNamespaceName);
                        $this->currentNamespace->addClass($this->currentClassObject);
                    }
                } else {
                    $this->fileObject->addClass($this->currentClassObject);
                    $this->currentClassObject = null;
                    $this->currentContainer = $this->fileObject;
                }
            } elseif ($node instanceof Node\Stmt\Namespace_) {
                if (null !== $this->currentNamespace) {
                    $this->fileObject->addNamespace($this->currentNamespace);
                    $this->currentNamespace = null;
                    $this->currentContainer = $this->fileObject;
                }
            } elseif ($node instanceof Node\Stmt\TraitUse) {
                if ($this->currentClassObject) {
                    $this->currentClassObject->addUseTraitStatement($node);
                }
            } elseif ($node instanceof Node\Stmt\Use_) {
                $this->currentContainer->addAliasDeclaration(
                    NodeConverter::convertUseAliasStatementNodeToArray($node)
                );
            } elseif ($node instanceof Node\Stmt\ClassConst) {
                $constants = NodeConverter::convertClassConstantNodeToArray($node);
                foreach ($constants as $constant) {
                    $this->currentContainer->setConstant($constant['name'], $constant['value']);
                }
            } elseif ($node instanceof Node\Stmt\ClassMethod) {
                $this->onFirstLevel = true;
                $method = $this->classFactory->buildClassMethodObject($node);
                $this->currentClassObject->addMethod($method);
            } elseif ($node instanceof Node\Stmt\Property) {
                $property = $this->classFactory->buildPropertyObject($node);
                $this->currentClassObject->addProperty($property);
            } elseif ($node instanceof Node\Stmt\Function_) {
                $this->onFirstLevel = true;
                $function = $this->classFactory->buildFunctionObject($node);
                $this->currentContainer->addFunction($function);
            } elseif (!$node instanceof Node\Name) {
                // any other nodes (except the name node of the current container node)
                // go into statements container
                    if ($this->currentContainer->getFirstClass() === false) {
                    $this->currentContainer->addPreClassStatements($node);
                } else {
                    $this->currentContainer->addPostClassStatements($node);
                }
            }
        }
    }

    /**
     * @param array $nodes
     */
    public function beforeTraverse(array $nodes)
    {
        $this->fileObject = new File;
        $this->currentContainer = $this->fileObject;
    }

    /**
     * @param \EBT\ExtensionBuilder\Parser\ClassFactoryInterface $classFactory
     */
    public function setClassFactory($classFactory)
    {
        $this->classFactory = $classFactory;
    }

    protected function isContainerNode($node)
    {
        return ($node instanceof Node\Stmt\Namespace_ || $node instanceof Node\Stmt\Class_);
    }

    protected function addLastNode()
    {
        if ($this->lastNode === null) {
            return;
        }
        if ($this->currentContainer->getFirstClass() === false) {
            $this->currentContainer->addPreClassStatements($this->lastNode);
        } else {
            $this->currentContainer->addPostClassStatements($this->lastNode);
        }
        $this->lastNode = null;
    }
}
