<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of Engineering
// to their own classes. A deprecated version remains in Engineering;
// this class contains cursory tests to ensure that those work properly.
// If Scrutinizer fails the PR because of these deprecations, I will
// remove this class from the PR.

class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertEquals(178, Engineering::BINTODEC(10110010));
        self::assertEquals('B2', Engineering::BINTOHEX(10110010));
        self::assertEquals(144, Engineering::BINTOOCT(1100100));
        self::assertEquals(101100101, Engineering::DECTOBIN(357));
        self::assertEquals(165, Engineering::DECTOHEX(357));
        self::assertEquals(545, Engineering::DECTOOCT(357));
        self::assertEquals(1100100, Engineering::HEXTOBIN(64));
        self::assertEquals(357, Engineering::HEXTODEC(165));
        self::assertEquals(653, Engineering::HEXTOOCT('01AB'));
        self::assertEquals(1100100, Engineering::OCTTOBIN(144));
        self::assertEquals(357, Engineering::OCTTODEC(545));
        self::assertEquals('1AB', Engineering::OCTTOHEX(653));
    }
}
