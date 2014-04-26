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

/**
 * provides methods to generate classes from PHP code
 *
 * @author Nico de Haen
 */

if (!class_exists('PHPParser_Parser')) {
	\EBT\ExtensionBuilder\Parser\AutoLoader::register();
}


/**
 *
 */
class Parser extends \PHPParser_Parser implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var \EBT\ExtensionBuilder\\Parser\Visitor\FileVisitorInterface
	 */
	protected $fileVisitor = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\\Parser\TraverserInterface
	 */
	protected $traverser = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\\Parser\ClassFactoryInterface
	 */
	protected $classFactory = NULL;

	/**
	 * @var \EBT\ExtensionBuilder\Parser\Visitor\FileVisitorInterface
	 */
	protected $classFileVisitor = NULL;

	/**
	 * @param string $code
	 * @return \EBT\ExtensionBuilder\Domain\Model\File
	 */
	public function parseCode($code) {
		$stmts = $this->parseRawStatements($code);
			// set defaults
		if (NULL === $this->traverser) {
			$this->traverser = new \EBT\ExtensionBuilder\Parser\Traverser;
		}
		if (NULL === $this->fileVisitor) {
			$this->fileVisitor = new \EBT\ExtensionBuilder\Parser\Visitor\FileVisitor;
		}
		if (NULL === $this->classFactory) {
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
	public function parseFile($fileName) {
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
	public function parseRawStatements($code) {
		return parent::parse($code);
	}

	/**
	 * @param \EBT\ExtensionBuilder\\Parser\Visitor\FileVisitorInterface $visitor
	 */
	public function setFileVisitor(\EBT\ExtensionBuilder\Parser\Visitor\FileVisitorInterface $visitor) {
		$this->classFileVisitor = $visitor;
	}

	/**
	 * @param \EBT\ExtensionBuilder\\Parser\TraverserInterface
	 * @return void
	 */
	public function setTraverser(\EBT\ExtensionBuilder\Parser\TraverserInterface $traverser) {
		$this->traverser = $traverser;
	}

	/**
	 * @param \EBT\ExtensionBuilder\\Parser\ClassFactoryInterface $classFactory
	 */
	public function setClassFactory(\EBT\ExtensionBuilder\Parser\ClassFactoryInterface $classFactory) {
		$this->classFactory = $classFactory;
	}

	/**
	 * @param array $stmts
	 * @param array $replacements
	 * @param string $nodeType
	 * @param string $nodeProperty
	 * @return array
	 */
	public function replaceNodeProperty($stmts, $replacements, $nodeType = NULL, $nodeProperty = 'name') {
		if (NULL === $this->traverser) {
			$this->traverser = new \EBT\ExtensionBuilder\Parser\Traverser;
		}
		$this->traverser->resetVisitors();
		$visitor = new \EBT\ExtensionBuilder\Parser\Visitor\ReplaceVisitor;
		$visitor->setNodeType($nodeType)
			->setNodeProperty($nodeProperty)
			->setReplacements($replacements);
		$this->traverser->appendVisitor($visitor);
		$stmts = $this->traverser->traverse($stmts);
		$this->traverser->resetVisitors();
		return $stmts;
	}

}
