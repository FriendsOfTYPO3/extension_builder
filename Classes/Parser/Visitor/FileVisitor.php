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

use EBT\ExtensionBuilder\Domain\Model\ClassObject\ClassObject;
use EBT\ExtensionBuilder\Domain\Model\Container;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Domain\Model\NamespaceObject;
use EBT\ExtensionBuilder\Parser\ClassFactoryInterface;
use EBT\ExtensionBuilder\Parser\Utility\NodeConverter;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeAbstract;
use PhpParser\NodeVisitorAbstract;

/**
 * provides methods to import a class object and methods and properties
 */
class FileVisitor extends NodeVisitorAbstract implements FileVisitorInterface
{
    protected array $properties = [];
    protected ?ClassObject $currentClassObject = null;
    protected ?NamespaceObject $currentNamespace = null;
    protected Container $currentContainer;
    protected ?File $fileObject = null;
    protected ?ClassFactoryInterface $classFactory = null;
    protected bool $onFirstLevel = true;
    /**
     * currently not used, might be useful for filtering etc.
     * it keeps a reference to the current "first level" node
     */
    protected array $contextStack = [];
    protected ?Node $lastNode = null;

    public function getFileObject(): File
    {
        return $this->fileObject;
    }

    public function enterNode(Node $node): void
    {
        $this->contextStack[] = $node;
        if ($node instanceof Namespace_) {
            $this->currentNamespace = $this->classFactory->buildNamespaceObject($node);
            $this->currentContainer = $this->currentNamespace;
            return;
        }

        if ($node instanceof Class_) {
            $this->currentClassObject = $this->classFactory->buildClassObject($node);
            $this->currentContainer = $this->currentClassObject;
        }
    }

    public function leaveNode(Node $node): void
    {
        array_pop($this->contextStack);
        if (count($this->contextStack) === 0 || $this->isContainerNode(end($this->contextStack))) {
            // we are on the first level
            if ($node instanceof Class_) {
                if (count($this->contextStack) > 0) {
                    if (end($this->contextStack)->getType() === 'Stmt_Namespace') {
                        $currentNamespaceName = NodeConverter::getValueFromNode(end($this->contextStack));
                        $this->currentClassObject->setNamespaceName($currentNamespaceName);
                        $this->currentNamespace->addClass($this->currentClassObject);
                    }
                } else {
                    $this->fileObject->addClass($this->currentClassObject);
                    $this->currentClassObject = null;
                    $this->currentContainer = $this->fileObject;
                }
                return;
            }

            if ($node instanceof Namespace_) {
                if (null !== $this->currentNamespace) {
                    $this->fileObject->addNamespace($this->currentNamespace);
                    $this->currentNamespace = null;
                    $this->currentContainer = $this->fileObject;
                }
                return;
            }

            if ($node instanceof TraitUse) {
                if ($this->currentClassObject) {
                    $this->currentClassObject->addUseTraitStatement($node);
                }
                return;
            }

            if ($node instanceof Use_) {
                $this->currentContainer->addAliasDeclaration(
                    NodeConverter::convertUseAliasStatementNodeToArray($node)
                );
                return;
            }

            if ($node instanceof ClassConst) {
                $constants = NodeConverter::convertClassConstantNodeToArray($node);
                foreach ($constants as $constant) {
                    $this->currentContainer->setConstant($constant['name'], $constant['value']);
                }
                return;
            }

            if ($node instanceof ClassMethod) {
                $this->onFirstLevel = true;
                $method = $this->classFactory->buildClassMethodObject($node);
                $this->currentClassObject->addMethod($method);
                return;
            }

            if ($node instanceof Property) {
                $property = $this->classFactory->buildPropertyObject($node);
                $this->currentClassObject->addProperty($property);
                return;
            }

            if ($node instanceof Function_) {
                $this->onFirstLevel = true;
                $function = $this->classFactory->buildFunctionObject($node);
                $this->currentContainer->addFunction($function);
                return;
            }

            if (!$node instanceof Name && !$node instanceof Declare_) {
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

    public function beforeTraverse(array $nodes): void
    {
        $this->fileObject = new File();
        $this->currentContainer = $this->fileObject;
    }

    public function setClassFactory(ClassFactoryInterface $classFactory): void
    {
        $this->classFactory = $classFactory;
    }

    protected function isContainerNode(NodeAbstract $node): bool
    {
        return $node instanceof Namespace_ || $node instanceof Class_;
    }

    protected function addLastNode(): void
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
