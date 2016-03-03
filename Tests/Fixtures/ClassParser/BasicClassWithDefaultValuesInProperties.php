<?php
class Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicClassWithDefaultValuesInProperties
{

    const FOO = -1;

    /**
     * @var array
     */
    protected $names = array();

    /**
     * @var \DateTime
     */
    protected $date = null;

    /**
     * @var int
     */
    protected $integer = 0;

    /**
     * @var int
     */
    protected $anotherInteger = -1;

    /**
     * @var float
     */
    protected $float = 0.45;

    /**
     * @var string
     */
    protected $string = '';

    /**
     * @var string
     */
    protected $anotherWeirdString = 'Test"-"Foo';
}
