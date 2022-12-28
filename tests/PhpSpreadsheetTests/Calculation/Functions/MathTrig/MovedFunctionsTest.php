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
        self::assertSame(1, /** @scrutinizer ignore-deprecated */ MathTrig::builtinABS(1));
        self::assertEqualsWithDelta(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinACOS(1), 1E-9);
        self::assertEqualsWithDelta(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinACOSH(1), 1E-9);
        self::assertEqualsWithDelta(3.04192400109863, /** @scrutinizer ignore-deprecated */ MathTrig::ACOT(-10), 1E-9);
        self::assertEqualsWithDelta(-0.20273255405408, /** @scrutinizer ignore-deprecated */ MathTrig::ACOTH(-5), 1E-9);
        self::assertSame(49, /** @scrutinizer ignore-deprecated */ MathTrig::ARABIC('XLIX'));
        self::assertEqualsWithDelta(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinASIN(0), 1E-9);
        self::assertEqualsWithDelta(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinASINH(0), 1E-9);
        self::assertEqualsWithDelta(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinATAN(0), 1E-9);
        self::assertEqualsWithDelta(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinATANH(0), 1E-9);
        self::assertEqualsWithDelta('#DIV/0!', /** @scrutinizer ignore-deprecated */ MathTrig::ATAN2(0, 0), 1E-9);
        self::assertEquals('12', /** @scrutinizer ignore-deprecated */ MathTrig::BASE(10, 8));
        self::assertEquals(-6, /** @scrutinizer ignore-deprecated */ MathTrig::CEILING(-4.5, -2));
        self::assertEquals(15, /** @scrutinizer ignore-deprecated */ MathTrig::COMBIN(6, 2));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::builtinCOS(0));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::builtinCOSH(0));
        self::assertEquals('#DIV/0!', /** @scrutinizer ignore-deprecated */ MathTrig::COT(0));
        self::assertEquals('#DIV/0!', /** @scrutinizer ignore-deprecated */ MathTrig::COTH(0));
        self::assertEquals('#DIV/0!', /** @scrutinizer ignore-deprecated */ MathTrig::CSC(0));
        self::assertEquals('#DIV/0!', /** @scrutinizer ignore-deprecated */ MathTrig::CSCH(0));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinDEGREES(0));
        self::assertEquals(6, /** @scrutinizer ignore-deprecated */ MathTrig::EVEN(4.5));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::builtinEXP(0));
        self::assertEquals(6, /** @scrutinizer ignore-deprecated */ MathTrig::FACT(3));
        self::assertEquals(105, /** @scrutinizer ignore-deprecated */ MathTrig::FACTDOUBLE(7));
        self::assertEquals(-6, /** @scrutinizer ignore-deprecated */ MathTrig::FLOOR(-4.5, 2));
        self::assertEquals(0.23, /** @scrutinizer ignore-deprecated */ MathTrig::FLOORMATH(0.234, 0.01));
        self::assertEquals(-4, /** @scrutinizer ignore-deprecated */ MathTrig::FLOORPRECISE(-2.5, 2));
        self::assertEquals(2, /** @scrutinizer ignore-deprecated */ MathTrig::GCD(4, 6));
        self::assertEquals(-9, /** @scrutinizer ignore-deprecated */ MathTrig::INT(-8.3));
        self::assertEquals(12, /** @scrutinizer ignore-deprecated */ MathTrig::LCM(4, 6));
        self::assertEqualswithDelta(2.302585, /** @scrutinizer ignore-deprecated */ MathTrig::builtinLN(10), 1E-6);
        self::assertEqualswithDelta(0.306762486567556, /** @scrutinizer ignore-deprecated */ MathTrig::logBase(1.5, 3.75), 1E-6);
        self::assertEqualswithDelta(0.301030, /** @scrutinizer ignore-deprecated */ MathTrig::builtinLOG10(2), 1E-6);
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::MDETERM([1]));
        self::assertEquals(
            [[2, 2], [2, 1]],
            /** @scrutinizer ignore-deprecated */
            MathTrig::MINVERSE([[-0.5, 1.0], [1.0, -1.0]])
        );
        self::assertEquals(
            [[23], [53]],
            /** @scrutinizer ignore-deprecated */
            MathTrig::MMULT([[1, 2], [3, 4]], [[7], [8]])
        );
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::MOD(5, 2));
        self::assertEquals(6, /** @scrutinizer ignore-deprecated */ MathTrig::MROUND(7.3, 3));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::MULTINOMIAL(1));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::numberOrNan(0));
        self::assertEquals(5, /** @scrutinizer ignore-deprecated */ MathTrig::ODD(4.5));
        self::assertEquals(8, /** @scrutinizer ignore-deprecated */ MathTrig::POWER(2, 3));
        self::assertEquals(8, /** @scrutinizer ignore-deprecated */ MathTrig::PRODUCT(1, 2, 4));
        self::assertEquals(8, /** @scrutinizer ignore-deprecated */ MathTrig::QUOTIENT(17, 2));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinRADIANS(0));
        self::assertGreaterThanOrEqual(0, /** @scrutinizer ignore-deprecated */ MathTrig::RAND());
        self::assertEquals('I', /** @scrutinizer ignore-deprecated */ MathTrig::ROMAN(1));
        self::assertEquals(3.3, /** @scrutinizer ignore-deprecated */ MathTrig::builtinROUND(3.27, 1));
        self::assertEquals(662, /** @scrutinizer ignore-deprecated */ MathTrig::ROUNDDOWN(662.79, 0));
        self::assertEquals(663, /** @scrutinizer ignore-deprecated */ MathTrig::ROUNDUP(662.79, 0));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::SEC(0));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::SECH(0));
        self::assertEquals(3780, /** @scrutinizer ignore-deprecated */ MathTrig::SERIESSUM(5, 1, 1, [1, 1, 0, 1, 1]));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::SIGN(79.2));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinSIN(0));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinSINH(0));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinSQRT(0));
        self::assertEqualswithDelta(3.54490770181103, /** @scrutinizer ignore-deprecated */ MathTrig::SQRTPI(4), 1E-6);
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::SUBTOTAL(2, [0, 0]));
        self::assertEquals(7, /** @scrutinizer ignore-deprecated */ MathTrig::SUM(1, 2, 4));
        self::assertEquals(4, /** @scrutinizer ignore-deprecated */ MathTrig::SUMIF([[2], [4]], '>2'));
        self::assertEquals(2, /** @scrutinizer ignore-deprecated */ MathTrig::SUMIFS(
            [[1], [1], [1]],
            [['Y'], ['Y'], ['N']],
            '=Y',
            [['H'], ['H'], ['H']],
            '=H'
        ));
        self::assertEquals(17, /** @scrutinizer ignore-deprecated */ MathTrig::SUMPRODUCT([1, 2, 3], [5, 0, 4]));
        self::assertEquals(21, /** @scrutinizer ignore-deprecated */ MathTrig::SUMSQ(1, 2, 4));
        self::assertEquals(-20, /** @scrutinizer ignore-deprecated */ MathTrig::SUMX2MY2([1, 2], [3, 4]));
        self::assertEquals(30, /** @scrutinizer ignore-deprecated */ MathTrig::SUMX2PY2([1, 2], [3, 4]));
        self::assertEquals(8, /** @scrutinizer ignore-deprecated */ MathTrig::SUMXMY2([1, 2], [3, 4]));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinTAN(0));
        self::assertEquals(0, /** @scrutinizer ignore-deprecated */ MathTrig::builtinTANH(0));
        self::assertEquals(70, /** @scrutinizer ignore-deprecated */ MathTrig::TRUNC(79.2, -1));
        self::assertEquals(1, /** @scrutinizer ignore-deprecated */ MathTrig::returnSign(79.2));
        self::assertEquals(80, /** @scrutinizer ignore-deprecated */ MathTrig::getEven(79.2));
        $nullVal = null;
        /** @scrutinizer ignore-deprecated */
        MathTrig::nullFalseTrueToNumber($nullVal);
        self::assertSame(0, $nullVal);
        $nullVal = true;
        /** @scrutinizer ignore-deprecated */
        MathTrig::nullFalseTrueToNumber($nullVal);
        self::assertSame(1, $nullVal);
    }
}
