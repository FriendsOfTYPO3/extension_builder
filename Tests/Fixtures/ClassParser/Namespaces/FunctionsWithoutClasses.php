<?php
declare(strict_types=1);

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
    }

    return $bar;
}
