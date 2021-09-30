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

use PhpParser\Parser;
use EBT\ExtensionBuilder\Domain\Model\File;
use EBT\ExtensionBuilder\Parser\ClassFactory;
use EBT\ExtensionBuilder\Parser\ClassFactoryInterface;
use EBT\ExtensionBuilder\Parser\Traverser;
use EBT\ExtensionBuilder\Parser\TraverserInterface;
use EBT\ExtensionBuilder\Parser\Visitor\FileVisitor;
use EBT\ExtensionBuilder\Parser\Visitor\FileVisitorInterface;
use EBT\ExtensionBuilder\Parser\Visitor\ReplaceVisitor;
use PhpParser\NodeVisitor\CloningVisitor;
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
        $this->parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }

    public function parseCode(string $code): File
    {
        $stmts = $this->parser->parse($code);

        // set defaults
        if (null === $this->traverser) {
            $this->traverser = new Traverser();
        }
        if (null === $this->fileVisitor) {
            $this->fileVisitor = new FileVisitor();
        }
        if (null === $this->classFactory) {
            $this->classFactory = new ClassFactory();
        }
        $this->fileVisitor->setClassFactory($this->classFactory);
        $this->traverser->appendVisitor($this->fileVisitor);
        $this->traverser->traverse($stmts);
        return $this->fileVisitor->getFileObject();
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
        if (null === $this->traverser) {
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

    public function initReduceCallbacks(): void
    {
    }
}
