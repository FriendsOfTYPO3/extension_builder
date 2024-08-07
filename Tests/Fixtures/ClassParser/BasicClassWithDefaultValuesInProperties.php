<?php
declare(strict_types=1);

class Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicClassWithDefaultValuesInProperties
{
    final public const FOO = -1;

    /**
     * @var array
     */
    protected $names = [];

    /**
     * @var \DateTime
     */
    protected $date;

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
