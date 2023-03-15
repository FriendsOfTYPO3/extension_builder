<?php
declare(strict_types=1);

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
final class Tx_ExtensionBuilder_Tests_Examples_ClassParser_ComplexClass
{
    public $names;
    protected $name='test;';

    public const testConstant = '123';
    public const testConstant2 = 0.56;

    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'subtitle' =>  QueryInterface::ORDER_DESCENDING,
        'test' => 'test;',
    ];

    /**
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
     */
    public function methodWithStrangePrecedingBlock($name)
    {
        /**
         * multi-line comment in a method
         * explaining some strange things
         */
        $this->name = $name;
    }
    public static $constProperty = self::testConstant;

    /**
     * @static
     * @param $param1
     * @param $param2
     * @param string $param3
     */
    public static function methodWithVariousParameter($param1, &$param2, $param3= 'default', array $param4 = ['test'=>[1, 2, 3]]): int
    {
        return 5; // test test
    }
    public const another_Constant = 'r5r_8';
    // single line comment
    public $testProperty4 = 123;
}
