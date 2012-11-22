<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nico de Haen
 *  All rights reserved
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
 * provides methods to import a class object
 *
 * @version $ID:$
 */
class Tx_ExtensionBuilder_Utility_ClassParser implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 *
	 * @var Tx_ExtensionBuilder_Domain_Model_Class_Class
	 */
	protected $classObject;

	/**
	 *
	 * @var Tx_ExtensionBuilder_Reflection_ClassReflection
	 */
	protected $classReflection;

	/**
	 * the current line number
	 * @var int
	 */
	protected $lineCount;

	/**
	 * might be set to TRUE from "outside"
	 * @var boolean
	 */
	public $debugMode = FALSE;

	/**
	 * The default indent for lines in method bodies
	 * @var string
	 */
	public $indentToken = "\t\t";

	/**
	 * The regular expression to detect a method in a line
	 * @var string regular expression
	 */
	public $methodRegex = "/^
		\s*															# Some possible whitespace
		(
			((?P<visibility>public|protected|private)\s+)			# Visibility declaration
			|
			((?P<static>static)\s+)									# Static declaration
		){0,2}														# Visiblity and Static can both occur, in any order
		\s*															# Some possible whitespace
		function													# Literal string 'function'
		\s+															# One or multiple whitespaces
		(?P<methodName>\w*)											# The method name
		\s*\(														# Some possible whitespace followed by a (
	/x";

	/**
	 * The regular expression to detect a property (or multiple) in a line
	 * TODO: the regex fails in at least 2 cases:
	 *		 1. if a value contains a string with a semicolon AND an escaped quote; "test;\""
	 *		 2. if an array contains a string with a semicolon in it: array(foo => 'bar;');
	 * @var string regular expression
	 */
	public $propertyRegex = '/\s*\\$(?<name>\w*)\s*(\=(?<value>\s*([^;\"\']|\"[^\"]*\"|\'[^\']*\'|[^;]*)))?;/';

	/**
	 * The regular expression to detect a property with a multiline default value (for example an array)
	 * @var string regular expression
	 */
	public $multiLinePropertyRegex = '/\s*\\$(?<name>\w*)\s*(\=(?<value>\s*(.*)))?/';

	/**
	 * The regular expression to detect a constant in a line
	 * @var string regular expression
	 */
	public $constantRegex = '/\s*const\s+(\w*)\s*\=\s*\'*\"*([^;"\']*)\'*\"*;/';

	public $nameSpaceRegex = '/^namespace(.*);/';

	public $aliasRegex = '/^use(.*);/';

	public $declareRegex = '/^declare(.*);/';

	/**
	 * Reference to the current line in the parser
	 * @var String
	 */
	protected $currentLine;

	/**
	 * remember the last line that matched either a property, a constant or a method end
	 * this is needed to get all comments between two methods or properties
	 * (not only the doc comment)
	 * @var int
	 */
	protected $lastMatchedLineNumber;

	/**
	 * for profiling
	 * @var float
	 */
	protected $starttime;

	/**
	 * Array with various data about the currently parsed method
	 * @var array
	 */
	protected $currentMethod = Array(
		'reflection' => NULL, // the Tx_ExtensionBuilder_Reflection_MethodReflection returned from ClassReflection
		'methodObject' => NULL, // the new created Tx_ExtensionBuilder_Domain_Model_Class_Method
		'endline' => 0,
		'methodBody' => ''
	);

	/**
	 * If the current parser position is in a method body we
	 * can skip parsing except for method endings
	 * @var bool
	 */
	protected $inMethodBody = FALSE;

	/**
	 * @var bool true if we are in a multiline comment (since alsmost everything is
	 * allowed in multi line comments we skip parsing except for comment endings)
	 */
	protected $inMultiLineComment = FALSE;

	/**
	 * @var bool true if we are in a multiline property
	 */
	protected $inMultiLineProperty = FALSE;

	/**
	 * Array with matches from regex extended by a key 'startline'
	 * of the current mulitline property
	 * a multiline property is a property that has a multiline
	 * devault value (like an array for example)
	 * The additional lines are added to the "value" when the end(;) is parsed
	 * @var array
	 */
	protected $multiLinePropertyMatches = array();

	/**
	 * @var array
	 */
	protected $lines = array();

	/**
	 * @param string $className
	 * @return void
	 */
	protected function initClassObject($className) {

		$this->classObject = new Tx_ExtensionBuilder_Domain_Model_Class_Class($className);

		$this->classReflection = new Tx_ExtensionBuilder_Reflection_ClassReflection($className);

		$propertiesToMap = array('FileName', 'Modifiers', 'Tags', 'DocComment');

		// map class variables from ClassReflection to classObject
		foreach ($propertiesToMap as $propertyToMap) {
			// these are all "value objects" so there is no need to parse them
			$getterMethod = 'get' . $propertyToMap;
			$setterMethod = 'set' . $propertyToMap;

			$this->classObject->$setterMethod($this->classReflection->$getterMethod());
		}

		if(is_object($this->classReflection->getParentClass())) {
			$this->classObject->setParentClass($this->classReflection->getParentClass()->getName());
		}

		$interfaceNames = $this->classReflection->getInterfaceNames();
		if (count($interfaceNames) > 0) {
			if ($this->classReflection->getParentClass() && count($this->classReflection->getParentClass()->getInterfaceNames()) > 0) {
				$interfaceNames = array_diff($interfaceNames, $this->classReflection->getParentClass()->getInterfaceNames());
			}
			$this->classObject->setInterfaceNames($interfaceNames);
		}
		if($this->classReflection->getNamespaceName()) {
			$this->classObject->setNameSpace($this->classReflection->getNamespaceName());
		}
		// reset class properties
		$this->lines = array();
		$this->lastMatchedLineNumber = -1;
	}

	/**
	 * builds a classSchema from a className, you have to require_once before importing the class
	 * @param string $className
	 * @return Tx_ExtensionBuilder_Domain_Model_Class_Class
	 */
	public function parse($className) {

		$this->starttime = microtime(TRUE);

		if (!class_exists($className)) {
			throw new Exception('Class not exists: ' . $className);
		}

		$this->initClassObject($className);

		$file = $this->classReflection->getFileName();

		$fileHandler = fopen($file, 'r');

		if (!$fileHandler) {
			throw new Exception('Could not open file: ' . $file);
		}

		$this->lineCount = 1;

		while (!feof($fileHandler)) {

			$this->currentLine = fgets($fileHandler);
			$trimmedLine = trim($this->currentLine);

			// save all comment found before the class start line
			if ($this->lineCount == $this->classReflection->getStartLine()) {
				$this->onClassDefinitionFound();
				$this->lastMatchedLineNumber = $this->lineCount;
			}

			if (!empty($trimmedLine) && !$this->inMethodBody) {

				// if not in a comment we look for methods, properties or constants
				if (!$this->isSingleLineComment() && !$this->isMultiLineComment() && !empty($trimmedLine)) {

					// process methods
					if (preg_match_all($this->methodRegex, $trimmedLine, $methodMatches)) {

						$this->onMethodFound($methodMatches);

					} else {

						// a semicolon was found (but not in single or double quotes!)
						if ($this->inMultiLineProperty && preg_match('/(;)(?=(?:[^"\']|["|\'][^"\']*")*$)/', $trimmedLine)) {
							// the end line of a multi line property
							$this->onMultiLinePropertyEnd();
						}

						// process constants
						if (preg_match_all($this->constantRegex, $trimmedLine, $constantMatches)) {
							$this->addConstant($constantMatches);
							$this->lastMatchedLineNumber = $this->lineCount;
						}

						// process properties
						if (preg_match_all($this->propertyRegex, $trimmedLine, $propertyMatches)) {
							$this->addProperty($propertyMatches);
							$this->lastMatchedLineNumber = $this->lineCount;
						} elseif (preg_match_all($this->multiLinePropertyRegex, $trimmedLine, $propertyMatches)) {
							// a multiline property is a property that has a multiline devault value (like an array for example)
							$this->inMultiLineProperty = TRUE;
							$this->multiLinePropertyMatches = $propertyMatches;
							$this->multiLinePropertyMatches['startLine'] = $this->lineCount;
							$this->lastMatchedLineNumber = $this->lineCount;
						}

						if (preg_match_all($this->aliasRegex, $trimmedLine, $aliasMatches)) {
							t3lib_div::devlog('Alias Matches','extension_builder',0,$aliasMatches);
							if(!empty($aliasMatches[1])) {
								//$this->classObject->addAliasDeclaration(trim($aliasMatches[1][0]));
							}
						}
					}
				} // end of not in comment

			} // end of not empty and not in method body

			if ($this->inMethodBody && $this->lineCount == $this->currentMethod['reflection']->getEndline()) {
				// endline of a method
				$this->onMethodEnd($trimmedLine);
			}

			// if no matches of the various regex are found, the line might be added
			// later (onMethodEnd or on Multiline property end)
			$this->lines[$this->lineCount] = $this->currentLine;
			$this->lineCount++;

		} // end while feof

		if ($this->lineCount > $this->classReflection->getEndLine()) {
			$appendedBlock = $this->concatLinesFromArray($this->lines, $this->classReflection->getEndLine());
			$appendedBlock = str_replace('?>', '', $appendedBlock);
			$this->classObject->setAppendedBlock($appendedBlock);
		}

		// debug output
		if ($this->debugMode) {
			$this->debugInfo();
		}

		// some checks again the reflection class
		if (count($this->classObject->getMethods()) != count($this->classReflection->getNotInheritedMethods())) {
			throw new Exception('Class ' . $className . ' could not be parsed properly. Method count does not equal reflection method count');
		}

		if (count($this->classObject->getProperties()) != count($this->classReflection->getNotInheritedProperties())) {
			throw new Exception('Class ' . $className . ' could not be parsed properly. Property count does not equal reflection property count');
		}
		\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Class Info','extension_builder',0,$this->classObject->getInfo());
		return $this->classObject;
	}

	/**
	 * If the class definitions begins, all previous parsed lines
	 * are added as preceding block
	 * so we keep all includes, definition and other stuff
	 * @return void
	 */
	protected function onClassDefinitionFound() {
		$classPreComment = '';
		foreach (array_values($this->lines) as $line) {
			if (strlen(trim($line)) > 0 && !preg_match($this->nameSpaceRegex, $line)) {
				$classPreComment .= $line;
			}
		}
		$this->classObject->setPrecedingBlock($classPreComment);
	}

	/**
	 * The end of a multiLine property definition
	 * @return void
	 */
	protected function onMultiLinePropertyEnd() {
		// add all lines from startline to current line as value of the multiline property
		$this->multiLinePropertyMatches['value'][0] .= "\n" . $this->concatLinesFromArray($this->lines, $this->multiLinePropertyMatches['startLine']);
		$this->multiLinePropertyMatches['value'][0] .= str_replace(';', '', $this->currentLine);
		$this->addProperty($this->multiLinePropertyMatches, $this->multiLinePropertyMatches['startLine']);
		// reset the array
		$this->multiLinePropertyMatches = array();
		$this->lastMatchedLineNumber = $this->lineCount;
		$this->inMultiLineProperty = FALSE;
	}

	/**
	 *
	 * @return void
	 */
	protected function debugInfo() {
		if (count($this->classObject->getMethods()) != count($this->classReflection->getNotInheritedMethods())) {
			debug('Errorr: method count does not match: ' . count($this->classObject->getMethods()) . ' methods found, should be ' . count($this->classReflection->getNotInheritedMethods()));
			debug($this->classObject->getMethods());
			debug($this->classReflection->getNotInheritedMethods());
		}
		if (count($this->classObject->getProperties()) != count($this->classReflection->getNotInheritedProperties())) {
			debug('Error: property count does not match:' . count($this->classObject->getProperties()) . ' properties found, should be ' . count($this->classReflection->getNotInheritedProperties()));
			debug($this->classObject->getProperties());
			debug($this->classReflection->getNotInheritedProperties());
		}

		$info = $this->classObject->getInfo();

		$endtime = microtime(TRUE);
		$totaltime = $endtime - $this->starttime;
		$totaltime = round($totaltime, 5);

		$info['Parsetime:'] = $totaltime . ' s';

		debug($info);
	}

	protected function onMethodEnd($trimmedLine) {
		if ($this->currentMethod['reflection']->getEndline() - $this->currentMethod['reflection']->getStartLine() >= 2) {
			$this->currentMethod['methodBody'] = $this->concatLinesFromArray($this->lines, $this->currentMethod['reflection']->getStartLine());
		}
		else $this->currentMethod['methodBody'] = '';

		if ($this->currentMethod['reflection']->getEndline() == $this->currentMethod['reflection']->getStartLine()) {
			// a one line method: we have to strip the method body from the rest
			$methodBodyRegex = '/\{(.*)/';
			$methodBodyMatches = array();
			preg_match_all($methodBodyRegex, $this->currentLine, $methodBodyMatches);
			if (!empty($methodBodyMatches[1][0])) {
				$trimmedLine = trim($methodBodyMatches[1][0]);
				// trimmed line now still has a bracket at the end!
			}
		}

		if ($trimmedLine != '}' && strlen($trimmedLine) > 0) {

			// remove the bracket from last line
			$trimmedLine = substr(rtrim($trimmedLine), 0, -1);
			// add the trimmed $line
			$this->currentMethod['methodBody'] .= $trimmedLine;
		}
		$this->currentMethod['methodObject']->setBody($this->currentMethod['methodBody']);
		$this->classObject->addMethod($this->currentMethod['methodObject']);
		// end of a method body
		$this->inMethodBody = FALSE;
		$this->lastMatchedLineNumber = $this->lineCount;
		//TODO what if a method is defined in the same line as the preceding method ends? Should be checked with tokenizer?
	}

	/**
	 * If a method startline was found in the current line
	 * @param array $methodMatches (regex matches)
	 * @throws Tx_ExtensionBuilder_Exception_ParseError
	 * @return void
	 */
	protected function onMethodFound($methodMatches) {
		$this->inMethodBody = TRUE;
		$methodName = $methodMatches['methodName'][0];

		try {
			// the method has to exist in the classReflection
			$this->currentMethod['reflection'] = $this->classReflection->getMethod($methodName);
			if ($this->currentMethod['reflection']) {
				$classMethod = new Tx_ExtensionBuilder_Domain_Model_Class_Method($methodName, $this->currentMethod['reflection']);
				$precedingBlock = $this->concatLinesFromArray($this->lines, $this->lastMatchedLineNumber, NULL, FALSE);
				$classMethod->setPrecedingBlock($precedingBlock);
				$this->currentMethod['methodObject'] = $classMethod;

			}
			else {
				throw new Tx_ExtensionBuilder_Exception_ParseError(
					'Method ' . $methodName . ' does not exist. Parsed from line ' . $this->lineCount . 'in ' . $this->classReflection->getFileName()
				);
			}
		}
		catch (ReflectionException $e) {
			// ReflectionClass throws an exception if a method was not found
			\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Exception: ' . $e->getMessage(), 'extension_builder', 2);
		}
	}


	/**
	 * Test for singleLineComment
	 *
	 * @return boolean
	 */
	protected function isSingleLineComment() {
		// single comment line
		if (preg_match('/^\s*\/\\//', $this->currentLine)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Test for multiLineComment
	 * switch the $this->inMultiLineComment to TRUE or FALSE
	 * return TRUE if the parser is in a multiline comment
	 *
	 * @return boolean $isMultiLineComment
	 */
	protected function isMultiLineComment() {
		// end of multiline comment found (maybe this part could be better solved with tokenizer?)
		if (strrpos($this->currentLine, '*/') > -1) {
			if (strrpos($this->currentLine, '/**') > -1) {
				// if a multiline comment starts in the same line after a multiline comment end
				$this->inMultiLineComment = (strrpos($this->currentLine, '/**') > strrpos($this->currentLine, '*/'));
			}
			else {
				$this->inMultiLineComment = FALSE;
			}
		}
		else if (strrpos($this->currentLine, '/**') > -1) {
			// multiline comment start
			$this->inMultiLineComment = TRUE;
		}
		return $this->inMultiLineComment;
	}

	/**
	 * Adds one (or multiple) constants found in a source code line to the classObject
	 *
	 * @param array $constantMatches as returned from preg_match_all
	 */
	protected function addConstant($constantMatches) {
		for ($i = 0; $i < count($constantMatches[0]); $i++) {
			try {
				$constantName = $constantMatches[1][$i];
				// the constant has to exist in the classReflection
				$reflectionConstantValue = $this->classReflection->getConstant($constantName);

				$this->classObject->setConstant($constantName, json_encode($reflectionConstantValue));
			}
			catch (ReflectionException $e) {
				// ReflectionClass throws an exception if a property was not found
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Exception in line : ' . $e->getMessage() . ' Constant ' . $constantName . ' found in line ' . $this->lineCount, 'extension_builder');
			}
		}
	}

	/**
	 * Adds one (or multiple) properties found in a source code line to the classObject
	 *
	 * @param array $propertyMatches as returned from preg_match_all
	 */
	protected function addProperty(array $propertyMatches, $startLine = NULL) {
		$properties = array_combine($propertyMatches['name'], $propertyMatches['value']);
		$isFirstProperty = TRUE;
		foreach ($properties as $propertyName => $propertyValue) {
			try {
				// the property has to exist in the classReflection
				$reflectionProperty = $this->classReflection->getProperty($propertyName);

				if ($reflectionProperty) {

					$classProperty = new Tx_ExtensionBuilder_Domain_Model_Class_Property($propertyName);
					$classProperty->mapToReflectionProperty($reflectionProperty);

					// get the default value from regex matches
					if (!empty($propertyValue)) {
						if (strlen($classProperty->getVarType()) < 1) {
							// try to detect the varType from default value
							if (strpos($propertyValue, 'array') > -1) {
								$varType = 'array';
							}
							else {
								eval('$varType = gettype(' . $propertyValue . ');');
							}

							if (!empty($varType) && $varType != 'NULL') {
								$classProperty->setVarType($varType);
							}
						}
						$classProperty->setValue(trim($propertyValue));
						$classProperty->setDefault(TRUE);
					}

					if ($isFirstProperty) {
						// only the first property will get the preceding block assigned
						$precedingBlock = $this->concatLinesFromArray($this->lines, $this->lastMatchedLineNumber, $startLine, FALSE);
						$classProperty->setPrecedingBlock($precedingBlock);
						$isFirstProperty = FALSE;
					}

					$this->classObject->addProperty($classProperty);
					$this->lastMatchedLineNumber = $this->lineCount;
				}
				else {
					throw new Tx_ExtensionBuilder_Exception_ParseError(
						' Property ' . $propertyName . ' does not exist. Parsed from line ' . $this->lineCount . 'in ' . $this->classReflection->getFileName()
					);
				}
			}
			catch (ReflectionException $e) {
				// ReflectionClass throws an exception if a property was not found
				\TYPO3\CMS\Core\Utility\GeneralUtility::devlog('Exception in line : ' . $e->getMessage() . 'Property ' . $propertyName . ' found in line ' . $this->lineCount, 'extension_builder');
			}
		}
	}

	/**
	 * Helper function for method bodies
	 *
	 * @param array $lines
	 * @param int $start
	 * @param int $end (optional)
	 * @param boolean $skipEmptyLines
	 * @return string concatenated lines
	 */
	public function concatLinesFromArray($lines, $start, $end = NULL, $skipEmptyLines = TRUE) {
		$result = '';
		$lastLine = 'not empty';
		foreach ($lines as $lineNumber => $lineContent) {
			if ($end && $lineNumber == $end) {
				return $result;
			}
			if ($lineNumber > $start) {
				// remove multiple empty lines
				if ($skipEmptyLines && empty($lineContent) && empty($lastLine)) {
					continue;
				}
				else {
					$result .= $lineContent;
					$lastLine = $lineContent;
				}

			}
		}
		return $result;
	}
}

?>