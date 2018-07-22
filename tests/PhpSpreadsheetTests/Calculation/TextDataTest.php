<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class TextDataTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');
    }

    public function tearDown()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');
    }

    /**
     * @dataProvider providerCHAR
     *
     * @param mixed $expectedResult
     */
    public function testCHAR($expectedResult, ...$args)
    {
        $result = TextData::CHARACTER(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCHAR()
    {
        return require 'data/Calculation/TextData/CHAR.php';
    }

    /**
     * @dataProvider providerCODE
     *
     * @param mixed $expectedResult
     */
    public function testCODE($expectedResult, ...$args)
    {
        $result = TextData::ASCIICODE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCODE()
    {
        return require 'data/Calculation/TextData/CODE.php';
    }

    /**
     * @dataProvider providerCONCATENATE
     *
     * @param mixed $expectedResult
     */
    public function testCONCATENATE($expectedResult, ...$args)
    {
        $result = TextData::CONCATENATE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCONCATENATE()
    {
        return require 'data/Calculation/TextData/CONCATENATE.php';
    }

    /**
     * @dataProvider providerLEFT
     *
     * @param mixed $expectedResult
     */
    public function testLEFT($expectedResult, ...$args)
    {
        $result = TextData::LEFT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLEFT()
    {
        return require 'data/Calculation/TextData/LEFT.php';
    }

    /**
     * @dataProvider providerMID
     *
     * @param mixed $expectedResult
     */
    public function testMID($expectedResult, ...$args)
    {
        $result = TextData::MID(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMID()
    {
        return require 'data/Calculation/TextData/MID.php';
    }

    /**
     * @dataProvider providerRIGHT
     *
     * @param mixed $expectedResult
     */
    public function testRIGHT($expectedResult, ...$args)
    {
        $result = TextData::RIGHT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerRIGHT()
    {
        return require 'data/Calculation/TextData/RIGHT.php';
    }

    /**
     * @dataProvider providerLOWER
     *
     * @param mixed $expectedResult
     */
    public function testLOWER($expectedResult, ...$args)
    {
        $result = TextData::LOWERCASE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLOWER()
    {
        return require 'data/Calculation/TextData/LOWER.php';
    }

    /**
     * @dataProvider providerUPPER
     *
     * @param mixed $expectedResult
     */
    public function testUPPER($expectedResult, ...$args)
    {
        $result = TextData::UPPERCASE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerUPPER()
    {
        return require 'data/Calculation/TextData/UPPER.php';
    }

    /**
     * @dataProvider providerPROPER
     *
     * @param mixed $expectedResult
     */
    public function testPROPER($expectedResult, ...$args)
    {
        $result = TextData::PROPERCASE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerPROPER()
    {
        return require 'data/Calculation/TextData/PROPER.php';
    }

    /**
     * @dataProvider providerLEN
     *
     * @param mixed $expectedResult
     */
    public function testLEN($expectedResult, ...$args)
    {
        $result = TextData::STRINGLENGTH(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLEN()
    {
        return require 'data/Calculation/TextData/LEN.php';
    }

    /**
     * @dataProvider providerSEARCH
     *
     * @param mixed $expectedResult
     */
    public function testSEARCH($expectedResult, ...$args)
    {
        $result = TextData::SEARCHINSENSITIVE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerSEARCH()
    {
        return require 'data/Calculation/TextData/SEARCH.php';
    }

    /**
     * @dataProvider providerFIND
     *
     * @param mixed $expectedResult
     */
    public function testFIND($expectedResult, ...$args)
    {
        $result = TextData::SEARCHSENSITIVE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerFIND()
    {
        return require 'data/Calculation/TextData/FIND.php';
    }

    /**
     * @dataProvider providerREPLACE
     *
     * @param mixed $expectedResult
     */
    public function testREPLACE($expectedResult, ...$args)
    {
        $result = TextData::REPLACE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerREPLACE()
    {
        return require 'data/Calculation/TextData/REPLACE.php';
    }

    /**
     * @dataProvider providerSUBSTITUTE
     *
     * @param mixed $expectedResult
     */
    public function testSUBSTITUTE($expectedResult, ...$args)
    {
        $result = TextData::SUBSTITUTE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerSUBSTITUTE()
    {
        return require 'data/Calculation/TextData/SUBSTITUTE.php';
    }

    /**
     * @dataProvider providerTRIM
     *
     * @param mixed $expectedResult
     */
    public function testTRIM($expectedResult, ...$args)
    {
        $result = TextData::TRIMSPACES(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerTRIM()
    {
        return require 'data/Calculation/TextData/TRIM.php';
    }

    /**
     * @dataProvider providerCLEAN
     *
     * @param mixed $expectedResult
     */
    public function testCLEAN($expectedResult, ...$args)
    {
        $result = TextData::TRIMNONPRINTABLE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCLEAN()
    {
        return require 'data/Calculation/TextData/CLEAN.php';
    }

    /**
     * @dataProvider providerDOLLAR
     *
     * @param mixed $expectedResult
     */
    public function testDOLLAR($expectedResult, ...$args)
    {
        $result = TextData::DOLLAR(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDOLLAR()
    {
        return require 'data/Calculation/TextData/DOLLAR.php';
    }

    /**
     * @dataProvider providerFIXED
     *
     * @param mixed $expectedResult
     */
    public function testFIXED($expectedResult, ...$args)
    {
        $result = TextData::FIXEDFORMAT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerFIXED()
    {
        return require 'data/Calculation/TextData/FIXED.php';
    }

    /**
     * @dataProvider providerT
     *
     * @param mixed $expectedResult
     */
    public function testT($expectedResult, ...$args)
    {
        $result = TextData::RETURNSTRING(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerT()
    {
        return require 'data/Calculation/TextData/T.php';
    }

    /**
     * @dataProvider providerTEXT
     *
     * @param mixed $expectedResult
     */
    public function testTEXT($expectedResult, ...$args)
    {
        //    Enforce decimal and thousands separator values to UK/US, and currency code to USD
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');

        $result = TextData::TEXTFORMAT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerTEXT()
    {
        return require 'data/Calculation/TextData/TEXT.php';
    }

    /**
     * @dataProvider providerVALUE
     *
     * @param mixed $expectedResult
     */
    public function testVALUE($expectedResult, ...$args)
    {
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(' ');
        StringHelper::setCurrencyCode('$');

        $result = TextData::VALUE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerVALUE()
    {
        return require 'data/Calculation/TextData/VALUE.php';
    }

    /**
     * @dataProvider providerEXACT
     *
     * @param mixed $expectedResult
     * @param array $args
     */
    public function testEXACT($expectedResult, ...$args)
    {
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(' ');
        StringHelper::setCurrencyCode('$');

        $result = TextData::EXACT(...$args);
        self::assertSame($expectedResult, $result, null);
    }

    /**
     * @return array
     */
    public function providerEXACT()
    {
        return require 'data/Calculation/TextData/EXACT.php';
    }

    /**
     * @dataProvider providerTEXTJOIN
     *
     * @param mixed $expectedResult
     * @param array $args
     */
    public function testTEXTJOIN($expectedResult, array $args)
    {
        $result = TextData::TEXTJOIN(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerTEXTJOIN()
    {
        return require 'data/Calculation/TextData/TEXTJOIN.php';
    }

    /**
     * @dataProvider providerNUMBERVALUE
     *
     * @param mixed $expectedResult
     * @param array $args
     */
    public function testNUMBERVALUE($expectedResult, array $args)
    {
        $result = TextData::NUMBERVALUE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerNUMBERVALUE()
    {
        return require 'data/Calculation/TextData/NUMBERVALUE.php';
    }
}
