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
        self::assertEquals(178, /** @scrutinizer ignore-deprecated */ Engineering::BINTODEC(10110010));
        self::assertEquals('B2', /** @scrutinizer ignore-deprecated */ Engineering::BINTOHEX(10110010));
        self::assertEquals(144, /** @scrutinizer ignore-deprecated */ Engineering::BINTOOCT(1100100));
        self::assertEquals(101100101, /** @scrutinizer ignore-deprecated */ Engineering::DECTOBIN(357));
        self::assertEquals(165, /** @scrutinizer ignore-deprecated */ Engineering::DECTOHEX(357));
        self::assertEquals(545, /** @scrutinizer ignore-deprecated */ Engineering::DECTOOCT(357));
        self::assertEquals(1100100, /** @scrutinizer ignore-deprecated */ Engineering::HEXTOBIN(64));
        self::assertEquals(357, /** @scrutinizer ignore-deprecated */ Engineering::HEXTODEC(165));
        self::assertEquals(653, /** @scrutinizer ignore-deprecated */ Engineering::HEXTOOCT('01AB'));
        self::assertEquals(1100100, /** @scrutinizer ignore-deprecated */ Engineering::OCTTOBIN(144));
        self::assertEquals(357, /** @scrutinizer ignore-deprecated */ Engineering::OCTTODEC(545));
        self::assertEquals('1AB', /** @scrutinizer ignore-deprecated */ Engineering::OCTTOHEX(653));
    }
}
