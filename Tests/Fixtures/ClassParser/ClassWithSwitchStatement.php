<?php

declare(strict_types=1);

class Tx_Test_ClassWithSwitchStatement
{
    public function getSomeValue(string $input): int
    {
        $result = 0;
        switch ($input) {
            case 'foo':
                $result = 1;
                break;
            case 'bar':
                $result = 2;
                break;
            default:
                $result = 3;
        }
        return $result;
    }
}
