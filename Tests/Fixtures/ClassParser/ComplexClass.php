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
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Fixtures/ClassParser/BasicClass.php');

final class Tx_ExtensionBuilder_Tests_Examples_ClassParser_ComplexClass
{

    protected $name='test;';
    private $propertiesInOneLine;

    const testConstant = '123';
    const testConstant2 = 0.56;

    protected $defaultOrderings = [
        'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        'subtitle' =>  \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
        'test' => 'test;',
    ];

    /**
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }
    // some methods
    public function getNames()
    {
        return $this->names;
    }

    public function getNames1()
    {
    }

    public function getNames2()
    {
    }

    public function getNames3()
    {
        return $this->names;
    }

    //startPrecedingBlock

    /***********************************************************/

    /*********/ //some  strange comments /*/ test \*\*\*
    //  include_once('typo3conf/ext/extension_builder/Tests/Examples/ComplexClass.php'); // test

    /**
     *
     * @param string $name
     * @return void
     * @lazy
     */
    public function methodWithStrangePrecedingBlock($name)
    {
        /**
         * multi-line comment in a method
         * explaining some strange things
         */
        $this->name = $name;
    }
    private $another_Property = 'test456_";';
    private $anotherProperty = "test456_'\"";
    private $arrayProperty1 = [2,6,'test'];
    private $arrayProperty2 = ['test'=>3,'b' => 'q'];
    public static $constProperty = testConstant;

    /**
     * @static
     * @param $param1
     * @param $param2
     * @param string $param3
     * @param array $param4
     * @return int
     */
    public static function methodWithVariousParameter($param1, &$param2, $param3= 'default', array $param4 = ['test'=>[1, 2, 3]])
    {
        /**
         * test test
         */
        $test = 234;
        return 5; // test test
    }
    const another_Constant = 'r5r_8';
    // single line comment
public $testProperty4 = 123;
}

/**
 *  dfg dfg dfg dfg
 */
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility:: extPath('extension_builder') . 'Tests/Fixtures/ClassParser/BasicClass.php');   include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Fixtures/ClassParser/ComplexClass.php'); // test

include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extension_builder') . 'Tests/Fixtures/ClassParser/ComplexClass.php'); // test
