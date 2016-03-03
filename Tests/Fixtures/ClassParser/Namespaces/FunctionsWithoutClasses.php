<?php
namespace Tx_PhpParser\Tests;

function simpleFunction()
{
    // do something
}

/**
 * @param $foo
 * @param $bar
 */
function functionWithParameter($foo, $bar)
{
    if ($foo != $bar) {
        return $foo;
    } else {
        return $bar;
    }
}
