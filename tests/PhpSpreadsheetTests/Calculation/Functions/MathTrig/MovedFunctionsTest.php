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
        self::assertSame(1, MathTrig::builtinABS(1));
        self::assertEqualsWithDelta(0, MathTrig::builtinACOS(1), 1E-9);
        self::assertEqualsWithDelta(0, MathTrig::builtinACOSH(1), 1E-9);
        self::assertEqualsWithDelta(3.04192400109863, MathTrig::ACOT(-10), 1E-9);
        self::assertEqualsWithDelta(-0.20273255405408, MathTrig::ACOTH(-5), 1E-9);
        self::assertSame(49, MathTrig::ARABIC('XLIX'));
        self::assertEqualsWithDelta(0, MathTrig::builtinASIN(0), 1E-9);
        self::assertEqualsWithDelta(0, MathTrig::builtinASINH(0), 1E-9);
        self::assertEqualsWithDelta(0, MathTrig::builtinATAN(0), 1E-9);
        self::assertEqualsWithDelta(0, MathTrig::builtinATANH(0), 1E-9);
        self::assertEqualsWithDelta('#DIV/0!', MathTrig::ATAN2(0, 0), 1E-9);
        self::assertEquals('12', MathTrig::BASE(10, 8));
        self::assertEquals(-6, MathTrig::CEILING(-4.5, -2));
        self::assertEquals(15, MathTrig::COMBIN(6, 2));
        self::assertEquals(1, MathTrig::builtinCOS(0));
        self::assertEquals(1, MathTrig::builtinCOSH(0));
        self::assertEquals('#DIV/0!', MathTrig::COT(0));
        self::assertEquals('#DIV/0!', MathTrig::COTH(0));
        self::assertEquals('#DIV/0!', MathTrig::CSC(0));
        self::assertEquals('#DIV/0!', MathTrig::CSCH(0));
        self::assertEquals(0, MathTrig::builtinDEGREES(0));
        self::assertEquals(6, MathTrig::EVEN(4.5));
        self::assertEquals(1, MathTrig::builtinEXP(0));
        self::assertEquals(6, MathTrig::FACT(3));
        self::assertEquals(105, MathTrig::FACTDOUBLE(7));
        self::assertEquals(-6, MathTrig::FLOOR(-4.5, 2));
        self::assertEquals(0.23, MathTrig::FLOORMATH(0.234, 0.01));
        self::assertEquals(-4, MathTrig::FLOORPRECISE(-2.5, 2));
        self::assertEquals(2, MathTrig::GCD(4, 6));
        self::assertEquals(-9, MathTrig::INT(-8.3));
        self::assertEquals(12, MathTrig::LCM(4, 6));
        self::assertEqualswithDelta(2.302585, MathTrig::builtinLN(10), 1E-6);
        self::assertEqualswithDelta(0.306762486567556, MathTrig::logBase(1.5, 3.75), 1E-6);
        self::assertEqualswithDelta(0.301030, MathTrig::builtinLOG10(2), 1E-6);
        self::assertEquals(1, MathTrig::MDETERM([1]));
        self::assertEquals(
            [[2, 2], [2, 1]],
            MathTrig::MINVERSE([[-0.5, 1.0], [1.0, -1.0]])
        );
        self::assertEquals(
            [[23], [53]],
            MathTrig::MMULT([[1, 2], [3, 4]], [[7], [8]])
        );
        self::assertEquals(1, MathTrig::MOD(5, 2));
        self::assertEquals(6, MathTrig::MROUND(7.3, 3));
        self::assertEquals(1, MathTrig::MULTINOMIAL(1));
        self::assertEquals(0, MathTrig::numberOrNan(0));
        self::assertEquals(5, MathTrig::ODD(4.5));
        self::assertEquals(8, MathTrig::POWER(2, 3));
        self::assertEquals(8, MathTrig::PRODUCT(1, 2, 4));
        self::assertEquals(8, MathTrig::QUOTIENT(17, 2));
        self::assertEquals(0, MathTrig::builtinRADIANS(0));
        self::assertGreaterThanOrEqual(0, MATHTRIG::RAND());
        self::assertEquals('I', MathTrig::ROMAN(1));
        self::assertEquals(3.3, MathTrig::builtinROUND(3.27, 1));
        self::assertEquals(662, MathTrig::ROUNDDOWN(662.79, 0));
        self::assertEquals(663, MathTrig::ROUNDUP(662.79, 0));
        self::assertEquals(1, MathTrig::SEC(0));
        self::assertEquals(1, MathTrig::SECH(0));
        self::assertEquals(3780, MathTrig::SERIESSUM(5, 1, 1, [1, 1, 0, 1, 1]));
        self::assertEquals(1, MathTrig::SIGN(79.2));
        self::assertEquals(0, MathTrig::builtinSIN(0));
        self::assertEquals(0, MathTrig::builtinSINH(0));
        self::assertEquals(0, MathTrig::builtinSQRT(0));
        self::assertEqualswithDelta(3.54490770181103, MathTrig::SQRTPI(4), 1E-6);
        self::assertEquals(0, MathTrig::SUBTOTAL(2, [0, 0]));
        self::assertEquals(7, MathTrig::SUM(1, 2, 4));
        self::assertEquals(4, MathTrig::SUMIF([[2], [4]], '>2'));
        self::assertEquals(2, MathTrig::SUMIFS(
            [[1], [1], [1]],
            [['Y'], ['Y'], ['N']],
            '=Y',
            [['H'], ['H'], ['H']],
            '=H'
        ));
        self::assertEquals(17, MathTrig::SUMPRODUCT([1, 2, 3], [5, 0, 4]));
        self::assertEquals(21, MathTrig::SUMSQ(1, 2, 4));
        self::assertEquals(-20, MathTrig::SUMX2MY2([1, 2], [3, 4]));
        self::assertEquals(30, MathTrig::SUMX2PY2([1, 2], [3, 4]));
        self::assertEquals(8, MathTrig::SUMXMY2([1, 2], [3, 4]));
        self::assertEquals(0, MathTrig::builtinTAN(0));
        self::assertEquals(0, MathTrig::builtinTANH(0));
        self::assertEquals(70, MathTrig::TRUNC(79.2, -1));
        self::assertEquals(1, MathTrig::returnSign(79.2));
        self::assertEquals(80, MathTrig::getEven(79.2));
        $nullVal = null;
        MathTrig::nullFalseTrueToNumber($nullVal);
        self::assertSame(0, $nullVal);
        $nullVal = true;
        MathTrig::nullFalseTrueToNumber($nullVal);
        self::assertSame(1, $nullVal);
    }
}
