<?php

function simpleFunction()
{
    // do something
}

/**
 * @param $foo
 * @param $bar
 *
 * @return mixed
 */
function functionWithParameter($foo, $bar)
{
    if ($foo != $bar) {
        return $foo;
    }

    return $bar;
}
