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

class Parser extends \PhpParser\Parser implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var \EBT\ExtensionBuilder\\Parser\Visitor\FileVisitorInterface
     */
    protected $fileVisitor = null;
    /**
     * @var \EBT\ExtensionBuilder\\Parser\TraverserInterface
     */
    protected $traverser = null;
    /**
     * @var \EBT\ExtensionBuilder\\Parser\ClassFactoryInterface
     */
    protected $classFactory = null;
    /**
     * @var \EBT\ExtensionBuilder\Parser\Visitor\FileVisitorInterface
     */
    protected $classFileVisitor = null;

    /**
     * @param string $code
     * @return \EBT\ExtensionBuilder\Domain\Model\File
     */
    public function parseCode($code)
    {
        $stmts = $this->parseRawStatements($code);
        // set defaults
        if (null === $this->traverser) {
            $this->traverser = new \EBT\ExtensionBuilder\Parser\Traverser(true);
        }
        if (null === $this->fileVisitor) {
            $this->fileVisitor = new \EBT\ExtensionBuilder\Parser\Visitor\FileVisitor;
        }
        if (null === $this->classFactory) {
            $this->classFactory = new \EBT\ExtensionBuilder\Parser\ClassFactory;
        }
        $this->fileVisitor->setClassFactory($this->classFactory);
        $this->traverser->appendVisitor($this->fileVisitor);
        $this->traverser->traverse(array($stmts));
        $fileObject = $this->fileVisitor->getFileObject();
        return $fileObject;
    }

    /**
     * @param string $fileName
     * @throws \EBT\ExtensionBuilder\Exception\FileNotFoundException
     * @return \EBT\ExtensionBuilder\Domain\Model\File
     */
    public function parseFile($fileName)
    {
        if (!file_exists($fileName)) {
            throw new \TYPO3\CMS\Core\Localization\Exception\FileNotFoundException('File "' . $fileName . '" not found!');
        }
        $fileHandler = fopen($fileName, 'r');
        $code = fread($fileHandler, filesize($fileName));
        $fileObject = $this->parseCode($code);
        $fileObject->setFilePathAndName($fileName);
        return $fileObject;
    }

    /**
     * @param string $code
     * @return array
     */
    public function parseRawStatements($code)
    {
        return parent::parse($code);
    }

    /**
     * @param \EBT\ExtensionBuilder\\Parser\Visitor\FileVisitorInterface $visitor
     */
    public function setFileVisitor(\EBT\ExtensionBuilder\Parser\Visitor\FileVisitorInterface $visitor)
    {
        $this->classFileVisitor = $visitor;
    }

    /**
     * @param \EBT\ExtensionBuilder\\Parser\TraverserInterface
     * @return void
     */
    public function setTraverser(\EBT\ExtensionBuilder\Parser\TraverserInterface $traverser)
    {
        $this->traverser = $traverser;
    }

    /**
     * @param \EBT\ExtensionBuilder\\Parser\ClassFactoryInterface $classFactory
     */
    public function setClassFactory(\EBT\ExtensionBuilder\Parser\ClassFactoryInterface $classFactory)
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @param array $stmts
     * @param array $replacements
     * @param array $nodeTypes
     * @param string $nodeProperty
     * @return array
     */
    public function replaceNodeProperty($stmts, $replacements, $nodeTypes = array(), $nodeProperty = 'name')
    {
        if (null === $this->traverser) {
            $this->traverser = new \EBT\ExtensionBuilder\Parser\Traverser;
        }
        $this->traverser->resetVisitors();
        $visitor = new \EBT\ExtensionBuilder\Parser\Visitor\ReplaceVisitor;
        $visitor->setNodeTypes($nodeTypes)
            ->setNodeProperty($nodeProperty)
            ->setReplacements($replacements);
        $this->traverser->addVisitor($visitor);
        $stmts = $this->traverser->traverse($stmts);
        $this->traverser->resetVisitors();
        return $stmts;
    }
}
