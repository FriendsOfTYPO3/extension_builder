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
 * @package ExtbaseKickstarter
 * @version $ID:$
 */
class Tx_ExtbaseKickstarter_Utility_ClassParser implements t3lib_singleton{
	  
	/**
	 * 
	 * @var Tx_ExtbaseKickstarter_Domain_Model_Class_Class
	 */
	protected $classObject;
	
	/**
	 * 
	 * @var Tx_ExtbaseKickstarter_Reflection_ClassReflection
	 */
	protected $classReflection;
	
	/**
	 * the current line number 
	 * @var int
	 */
	protected $lineCount;
	
	/**
	 * might be set to true from "outside"
	 * @var boolean
	 */
	public $debugMode = false;
	
	/**
	 * The default indent for lines in method bodies
	 * @var string
	 */
	public $indentToken = "\t\t";
	
	/**
	 * The regular expression to detect a method in a line 
	 * @var string regular expression
	 */
	public $methodRegex = "/\s*function\s*([a-zA-Z0-9_]*)/";
	
	/**
	 * The regular expression to detect a property (or multiple) in a line 
	 * @var string regular expression
	 */
	public $propertyRegex = "/\s*\\$([a-zA-Z0-9_]*)/";
	
	/**
	 * The regular expression to detect a constant in a line 
	 * @var string regular expression
	 */
	public $constantRegex = "/\s*const\s*([a-zA-Z0-9_]*)\s*\=\s*\'*\"*([^;\"']*)'*\"*;/";
	
	// TODO parse definitions of namespaces
	public $namespaceRegex = "/^namespace|^use|^declare/";
	
	public $includeRegex = "/(require_once|require|include_once|include)+\s*\(([^;]*)\)/";
	
	// TODO parse definitions of constants
	public $defineRegex = "/define+\s*\(([a-zA-Z0-9_-,\\'\"\s]*)/";

	/**
	 * builds a classSchema from a className, you have to require_once before importing the class
	 * @param string $className
	 * @return Tx_ExtbaseKickstarter_Domain_Model_Class_Class 
	 */
	public function parse($className){
		
		$this->starttime = microtime(true); // for profiling
		
		if(!class_exists($className)){
			throw new Exception('Class not exists: '.$className);
		} 
		
		$this->classObject = new Tx_ExtbaseKickstarter_Domain_Model_Class_Class($className);
		
		$this->classReflection = new Tx_ExtbaseKickstarter_Reflection_ClassReflection($className);
		
		if($className == 'Tx_KickstartTest_Controller_BlogController'){
			//die(json_encode($this->classReflection));
		}
		
		$propertiesToMap = array('FileName','Modifiers','Tags','ParentClass');
		
		// map class variables from ClassReflection to classObject 
		foreach($propertiesToMap as $propertyToMap){
			// these are all "value objects" so there is no need to parse them
			$getterMethod = 'get'.$propertyToMap;
			$setterMethod = 'set'.$propertyToMap;
			
			$this->classObject->$setterMethod($this->classReflection->$getterMethod());
		}
		
		$file = $this->classReflection->getFileName();
		$fileHandler = fopen($file,'r');
		
		if(!$fileHandler){
			throw new Exception('Could not open file: '.$file);
		} 
		
		/**
		 * various flags used during parsing process
		 */
		
		$isSingleLineComment = false; 
		$isMultilineComment = false;
		$isMethodBody = false;
		
		// the Tx_ExtbaseKickstarter_Reflection_MethodReflection returned from ClassReflection
		$currentMethodReflection = NULL; 
		
		 // the new created Tx_ExtbaseKickstarter_Domain_Model_Class_Method
		$currentClassMethod = NULL;
		
		// remember the last line that matched either a property, a constant or a method end
		// this is needed to get all comments between two methods or properties 
		// (not only the doc comment
		$lastMatchedLine = 0; 
		
		$currentMethodEndLine = 0;
		
		$lines = array();
		
		$this->lineCount = 1;
		
		while(!feof($fileHandler)){
			$line = fgets($fileHandler);
			
			$trimmedLine = trim($line);
			
			// save all comment found before the class start line
			if($this->lineCount == $this->classReflection->getStartLine()){
				$classPreComment = '';
				foreach($lines as $lN => $lContent){
					if(strlen(trim($lContent))>0){
						$classPreComment .= $lContent;
					}
				}
				$this->classObject->setPrecedingBlock($classPreComment);
				
				$lastMatchedLine = $this->lineCount;
			}
			
			if(!empty($trimmedLine) && !$isMethodBody){
				
				// process multi line comment
				$isMultilineComment = $this->isMultiLineComment($line,$isMultilineComment);
				
				// process single line comment
				$isSingleLineComment = $this->isSingleLineComment($line);
				
				// if not in a comment we look for methods, properties or constants
				if(!$isSingleLineComment && !$isMultilineComment && !empty($trimmedLine)){
					

					$methodMatches = array();
					$propertyMatches = array();
					$constantMatches = array();
					
					// process methods
					if(preg_match_all($this->methodRegex,$trimmedLine,$methodMatches)){
						$isMethodBody = true;
						$methodName = $methodMatches[1][0];
						
						try{
							// the method has to exist in the classReflection
							$currentMethodReflection = $this->classReflection->getMethod($methodName);
							if($currentMethodReflection){
								//$parameters = $currentMethodReflection->getParameters();
								$precedingBlock = $this->concatLinesFromArray($lines,$lastMatchedLine);
								
								$currentClassMethod = new Tx_ExtbaseKickstarter_Domain_Model_Class_Method($methodName,$currentMethodReflection);
								$currentClassMethod->setPrecedingBlock($precedingBlock);
								//$currentClassMethod->setTags($currentMethodReflection->getTags());
								$currentMethodEndLine = $currentMethodReflection->getEndline();
								
							}
							else {
								throw new Tx_ExtbaseKickstarter_Exception_ParseError(
										'Method '. $methodName . ' does not exist. Parsed from line '.$this->lineCount . 'in '. $this->classReflection->getFileName()
									);
							}
						}
						catch(ReflectionException $e){
							// ReflectionClass throws an exception if a method was not found
							t3lib_div::devlog('Exception: '.$e->getMessage());
						}
						
					} // end of preg_match_all method
					
					if(!$isMethodBody){
						// process constants
						if(preg_match_all($this->constantRegex,$trimmedLine,$constantMatches)){
							$this->addConstant($constantMatches);
						}
						
						// process properties
						if(preg_match_all($this->propertyRegex,$trimmedLine,$propertyMatches)){
							$this->addProperty($propertyMatches);
						} 
						
					}
					
					$includeMatches = array();
					if( preg_match_all($this->includeRegex,$line,$includeMatches)){
						//preg_match_all($this->includeRegex,$trimmedLine,$includeMatches);
						foreach($includeMatches[2] as $include){
							$this->classObject->addInclude($include);
						}
					}
				} // end of not in comment
				
			} // end of not empty and not in method body
			
			// endline of a method
			if($isMethodBody && $this->lineCount == $currentMethodEndLine){

				$methodBodyStartLine = $currentMethodReflection->getStartLine();
				
				if($currentMethodEndLine - $currentMethodReflection->getStartLine() >= 2){
					$methodBody = $this->concatLinesFromArray($lines,$methodBodyStartLine);
				}
				else $methodBody = '';
				
				if($currentMethodEndLine == $currentMethodReflection->getStartLine()){
					// a one line method: we have to strip the method body from the rest
					$methodBodyRegex = '/\{(.*)/';
					$methodBodyMatches = array();
					preg_match_all($methodBodyRegex,$line,$methodBodyMatches);
					if(!empty($methodBodyMatches[1][0])){
						$trimmedLine = trim($methodBodyMatches[1][0]);
						// trimmed line now still has a bracket at the end!
					}
				}
				
				if($trimmedLine != '}' && strlen($trimmedLine)>0){
					
					// remove the bracket from last line
					$trimmedLine = substr(rtrim($trimmedLine),0,-1);
					// add the trimmed $line
					$methodBody .= $trimmedLine;
				}
				$currentClassMethod->setBody($methodBody);
				$this->classObject->addMethod($currentClassMethod);
				$currentMethodEndLine = 0;
				// end of a method body
				$isMethodBody = false;
				$lastMatchedLine = $this->lineCount;
				//TODO what if a method is defined in the same line as the preceding method ends? Should be checked with tokenizer?	
			}
			
			$lines[$this->lineCount] = $line;
			$this->lineCount++;
			
			
		} // end while feof

		if($this->lineCount > $this->classReflection->getEndLine()){
			$appendedBlock = $this->concatLinesFromArray($lines,$this->classReflection->getEndLine());
			$appendedBlock = str_replace('?>','',$appendedBlock);
			$this->classObject->setAppendedBlock($appendedBlock);
		}
		
		// debug output 
		if($this->debugMode){
			if(count($this->classObject->getMethods()) != count($this->classReflection->getNotInheritedMethods())){
				debug('Errorr: method count does not match: '.count($this->classObject->getMethods()).' methods found, should be '.count($this->classReflection->getNotInheritedMethods()));
				debug($this->classObject->getMethods());
				debug($this->classReflection->getNotInheritedMethods());
			}
			if(count($this->classObject->getProperties()) != count($this->classReflection->getNotInheritedProperties())){
				debug('Error: property count does not match:'.count($this->classObject->getProperties()).' properties found, should be '.count($this->classReflection->getNotInheritedProperties()));
				debug($this->classObject->getProperties());
				debug($this->classReflection->getNotInheritedProperties());
			}
			
			$info = $this->classObject->getInfo();
			
			$this->endtime = microtime(true);
	    	$totaltime = $this->endtime - $this->starttime;
	    	$totaltime = round($totaltime,5);
	    	
	    	$info['Parsetime:'] = $totaltime.' s';
			
	    	debug($info);
		}

		return $this->classObject;
	}
	
	/**
	 * Test for singleLineComment
	 * 
	 * $param string $line
	 */
	protected function isSingleLineComment($line){
		$isSingleLineComment = false;
		// single comment line
		if(!$isSingleLineComment && preg_match("/^\s*\/\\//",$line)){
			$isSingleLineComment = true;
		}
		return $isSingleLineComment;
	}
	
	/**
	 * Test for multiLineComment
	 * 
	 * @param string $line
	 * @param boolean $isMultilineComment
	 */
	protected function isMultiLineComment($line,$isMultilineComment){

		// end of multiline comment found (maybe this part could be better solved with tokenizer?)
		if(strrpos($line,'*/')>-1){
			if(strrpos($line,'/**')>-1){
				// if a multiline comment starts in the same line after a multiline comment end
				$isMultilineComment = (strrpos($line,'/**') > strrpos($line,'*/'));
			}
			else {
				$isMultilineComment = false;
			}
		}
		else if(strrpos($line,'/**')>-1){
			// multiline comment start
			$isMultilineComment = true;
		}
		return $isMultilineComment;
	}

	/**
	 * Adds on (or multiple) constants found in a source code line to the classObject
	 * 
	 * @param array $constantMatches as returned from preg_match_all
	 */
	protected function addConstant($constantMatches){
		for($i = 0;$i< count($constantMatches[0]);$i++){
			try{
				$constantName = $constantMatches[1][$i];
				// the constant has to exist in the classReflection
				$reflectionConstantValue = $this->classReflection->getConstant($constantName);
				
				$this->classObject->setConstant($constantName,json_encode($reflectionConstantValue));
			}
			catch(ReflectionException $e){
				// ReflectionClass throws an exception if a property was not found
				t3lib_div::devlog('Exception in line : '.$e->getMessage().' Constant '.$constantName.' found in line '.$this->lineCount);
			}
		}
	}
	
	/**
	 * Adds one (or multiple) properties found in a source code line to the classObject
	 * 
	 * @param array $propertyMatches as returned from preg_match_all
	 */
	protected function addProperty(array $propertyMatches){
		$propertyNames = $propertyMatches[1];
		$isFirstProperty = true;
		foreach($propertyNames as $propertyName){
			try{
				// the property has to exist in the classReflection
				$reflectionProperty = $this->classReflection->getProperty($propertyName);
				if($reflectionProperty){
					
					$classProperty = new Tx_ExtbaseKickstarter_Domain_Model_Class_Property($propertyName);
					$classProperty->mapToReflectionProperty($reflectionProperty);
					
					if($isFirstProperty){
						// only the first property will get the preceding block assigned
						$precedingBlock = $this->concatLinesFromArray($lines,$lastMatchedLine);
						$classProperty->setPrecedingBlock($precedingBlock);
						$isFirstProperty = false;
					}
					
					$this->classObject->addProperty($classProperty);
					$lastMatchedLine = $this->lineCount;
				}
				else {
					throw new Tx_ExtbaseKickstarter_Exception_ParseError(
							' Property '. $propertyName . ' does not exist. Parsed from line '.$this->lineCount . 'in '. $this->classReflection->getFileName()
						);
				}
			}
			catch(ReflectionException $e){
				// ReflectionClass throws an exception if a property was not found
				t3lib_div::devlog('Exception in line : '.$e->getMessage().'Property '.$propertyName.' found in line '.$this->lineCount);
			}
		}
	}
	
	/**
	 * Helper function for method bodies
	 * 
	 * @param array $lines
	 * @param int $start
	 * @param int $end (optional)
	 * @return string concatenated lines
	 */
	public function concatLinesFromArray($lines,$start,$end = NULL){
		$result = '';
		$lastLine = 'not empty';
		foreach($lines as $lineNumber => $lineContent){
			if($end && $lineNumber == $end){
				return $result;
			}
			if($lineNumber > $start){
				// remove multiple empty lines 
				if(empty($lineContent) && empty($lastLine)){
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