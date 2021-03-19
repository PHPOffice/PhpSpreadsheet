<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of MathTrig
// to their own classes. A deprecated version remains in MathTrig;
// this class contains cursory tests to ensure that those work properly.
// If Scrutinizer fails the PR because of these deprecations, I will
// remove this class from the PR.

class MovedFunctionsTest extends TestCase
{
    public function testMovedFunctions(): void
    {
        self::assertEquals(-6, MathTrig::CEILING(-4.5, -2));
        self::assertEquals(-6, MathTrig::FLOOR(-4.5, 2));
        self::assertEquals(0.23, MathTrig::FLOORMATH(0.234, 0.01));
        self::assertEquals(-4, MathTrig::FLOORPRECISE(-2.5, 2));
        self::assertEquals(-9, MathTrig::INT(-8.3));
        self::assertEquals(6, MathTrig::MROUND(7.3, 3));
        self::assertEquals(3.3, MathTrig::builtinROUND(3.27, 1));
        self::assertEquals(662, MathTrig::ROUNDDOWN(662.79, 0));
        self::assertEquals(663, MathTrig::ROUNDUP(662.79, 0));
        self::assertEquals(70, MathTrig::TRUNC(79.2, -1));
    }
}
