<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Engineering
// to their own classes. A deprecated version remains in Engineering;
// this class contains cursory tests to ensure that those work properly.
// If Scrutinizer fails the PR because of these deprecations, I will
// remove this class from the PR.

class MovedBitwiseTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertEquals(1, Engineering::BITAND(1, 3));
        self::assertEquals(3, Engineering::BITOR(1, 3));
        self::assertEquals(2, Engineering::BITXOR(1, 3));
        self::assertEquals(32, Engineering::BITLSHIFT(8, 2));
        self::assertEquals(2, Engineering::BITRSHIFT(8, 2));
    }
}
