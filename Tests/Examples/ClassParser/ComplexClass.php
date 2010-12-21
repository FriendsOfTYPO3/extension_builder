<?php 
/**
 * multiline comment test
 * @author Nico de Haen
 * 

	empty line in multiline comment
	
	// single comment in multiline
	 * 
	some keywords: $property  function
	static 
	
	

 *
 * @test testtag
 */
require_once(t3lib_extmgm::extPath('extbase_kickstarter') . 'Tests/Examples/ClassParser/BasicClass.php');
		

final class Tx_ExtbaseKickstarter_Tests_Examples_ClassParser_ComplexClass extends Tx_ExtbaseKickstarter_Tests_Examples_ClassParser_BasicClass{
	
	protected $name; private $propertiesInOneLine;
	
	const testConstant = "123"; const testConstant2 = 0.56;
	
	/**
	 * 
	 * @return string $name
	 */
	public function getName(){
		return $this->name;
	}
	// some methods
	public function getNames(){	return $this->names;}
	
	public function getNames1(){  }
	
	public function getNames2(){	
	}
	
	public function getNames3(){	
		return $this->names;		}
		
	//startPrecedingBlock
	
	/***********************************************************/
	
	
	/*********/ //some  strange comments /*/ test \*\*\*
	//  include_once('typo3conf/ext/extbase_kickstarter/Tests/Examples/ComplexClass.php'); // test
	
	/**
	 * 
	 * @param string $name
	 * @return void
	 * @lazy
	 */
	public function methodWithStrangePrecedingBlock(string $name){
		/**
		 * multi line comment in a method
		 * @var unknown_type
		 */
		$this->name = $name;
	}
	private $another_Property = 'test456_"';
	static function method_2($param1,&$param2,$param3= 'default',array $param4 = array('test'=>array(1,2,3))){
		/**
		 * test test
		 */
		$test = 234;
		return 5; // test test
	}
	const another_Constant = "r5r_8";
	// single line comment
var $testProperty4 = 123;
}

/**
 *  dfg dfg dfg dfg
 */
require_once(t3lib_extmgm:: extPath('extbase_kickstarter') . 'Tests/Examples/ClassParser/BasicClass.php');   include_once(t3lib_extmgm::extPath('extbase_kickstarter') . 'Tests/Examples/ComplexClass.php'); // test

 include_once(t3lib_extmgm::extPath('extbase_kickstarter') . 'Tests/Examples/ClassParser/ComplexClass.php'); // test
?>
