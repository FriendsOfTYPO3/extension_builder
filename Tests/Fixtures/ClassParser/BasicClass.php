<?php

class Tx_ExtensionBuilder_Tests_Examples_ClassParser_BasicClass
{

    protected $names;

    const TEST = 'test';

    const TEST2 = 'test';

    /**
     *
     * @return array $names
     */
    public function getNames()
    {
        return $this->names;
    }

    public function getNames0()
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

    /**
     *
     * @param array $names
     * @return void
     */
    public function setNames(array $names)
    {
        $this->names = $names;
    }
}
