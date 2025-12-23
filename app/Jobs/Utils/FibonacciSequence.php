<?php

namespace App\Jobs\Utils;

class FibonacciSequence
{
    public static function calculate(int $number): int
    {
        if ($number <= 0) {
            return 0;
        }

        if ($number === 1) {
            return 1;
        }

        $previous = 0;
        $current = 1;

        for ($i = 2; $i <= $number; $i++) {
            $next = $previous + $current;
            $previous = $current;
            $current = $next;
        }

        return $current;
    }
}
