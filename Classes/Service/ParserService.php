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

namespace EBT\ExtensionBuilder\Service;

use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Parser\ClassFactoryInterface;
use EBT\ExtensionBuilder\Parser\Traverser;
use EBT\ExtensionBuilder\Parser\TraverserInterface;
use EBT\ExtensionBuilder\Parser\Visitor\FileVisitor;
use EBT\ExtensionBuilder\Parser\Visitor\FileVisitorInterface;
use EBT\ExtensionBuilder\Parser\Visitor\ReplaceVisitor;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use TYPO3\CMS\Core\Localization\Exception\FileNotFoundException;
use TYPO3\CMS\Core\SingletonInterface;

class ParserService implements SingletonInterface
{
    protected ?FileVisitor $fileVisitor = null;
    protected ?TraverserInterface $traverser = null;
    protected ?ClassFactoryInterface $classFactory = null;
    protected ?FileVisitorInterface $classFileVisitor = null;
    protected Parser $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->createForHostVersion();
    }

    public function parseCode(string $code): File
    {
        $origStmts = $this->parser->parse($code);
        $origTokens = $this->parser->getTokens();

        // set defaults
        if ($this->traverser === null) {
            $this->traverser = new Traverser();
        }
        if ($this->fileVisitor === null) {
            $this->fileVisitor = new FileVisitor();
        }
        if ($this->classFactory === null) {
            $this->classFactory = new ClassFactory();
        }
        $this->fileVisitor->setClassFactory($this->classFactory);
        $this->traverser->resetVisitors();
        $this->traverser->appendVisitor(new CloningVisitor());
        $this->traverser->appendVisitor($this->fileVisitor);
        $this->traverser->traverse($origStmts);

        $fileObject = $this->fileVisitor->getFileObject();
        $fileObject->setOrigStmts($origStmts);
        $fileObject->setOrigTokens($origTokens);
        return $fileObject;
    }

    public function parseFile(string $fileName): File
    {
        if (!file_exists($fileName)) {
            throw new FileNotFoundException('File "' . $fileName . '" not found!');
        }
        $fileHandler = fopen($fileName, 'rb');
        $code = fread($fileHandler, filesize($fileName));
        fclose($fileHandler);

        $fileObject = $this->parseCode($code);
        $fileObject->setFilePathAndName($fileName);
        return $fileObject;
    }

    public function setFileVisitor(FileVisitorInterface $visitor): void
    {
        $this->classFileVisitor = $visitor;
    }

    public function setTraverser(TraverserInterface $traverser): void
    {
        $this->traverser = $traverser;
    }

    public function setClassFactory(ClassFactoryInterface $classFactory): void
    {
        $this->classFactory = $classFactory;
    }

    public function replaceNodeProperty(
        array $stmts,
        array $replacements,
        ?array $nodeTypes = [],
        string $nodeProperty = 'name'
    ): array {
        if ($this->traverser === null) {
            $this->traverser = new Traverser();
        }
        $this->traverser->resetVisitors();

        $visitor = new ReplaceVisitor();
        $visitor->setNodeTypes($nodeTypes)
            ->setNodeProperty($nodeProperty)
            ->setReplacements($replacements);
        $this->traverser->addVisitor(new CloningVisitor());
        $this->traverser->appendVisitor($visitor);

        $stmts = $this->traverser->traverse($stmts);
        $this->traverser->resetVisitors();
        return $stmts;
    }

    public function initReduceCallbacks(): void {}
}
