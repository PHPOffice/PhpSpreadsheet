<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class TextDataTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHAR
     */
    public function testCHAR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'CHARACTER'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCHAR()
    {
        return require 'data/Calculation/TextData/CHAR.php';
    }

    /**
     * @dataProvider providerCODE
     */
    public function testCODE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'ASCIICODE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCODE()
    {
        return require 'data/Calculation/TextData/CODE.php';
    }

    /**
     * @dataProvider providerCONCATENATE
     */
    public function testCONCATENATE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'CONCATENATE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCONCATENATE()
    {
        return require 'data/Calculation/TextData/CONCATENATE.php';
    }

    /**
     * @dataProvider providerLEFT
     */
    public function testLEFT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'LEFT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLEFT()
    {
        return require 'data/Calculation/TextData/LEFT.php';
    }

    /**
     * @dataProvider providerMID
     */
    public function testMID()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'MID'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerMID()
    {
        return require 'data/Calculation/TextData/MID.php';
    }

    /**
     * @dataProvider providerRIGHT
     */
    public function testRIGHT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'RIGHT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerRIGHT()
    {
        return require 'data/Calculation/TextData/RIGHT.php';
    }

    /**
     * @dataProvider providerLOWER
     */
    public function testLOWER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'LOWERCASE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLOWER()
    {
        return require 'data/Calculation/TextData/LOWER.php';
    }

    /**
     * @dataProvider providerUPPER
     */
    public function testUPPER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'UPPERCASE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerUPPER()
    {
        return require 'data/Calculation/TextData/UPPER.php';
    }

    /**
     * @dataProvider providerPROPER
     */
    public function testPROPER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'PROPERCASE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerPROPER()
    {
        return require 'data/Calculation/TextData/PROPER.php';
    }

    /**
     * @dataProvider providerLEN
     */
    public function testLEN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'STRINGLENGTH'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLEN()
    {
        return require 'data/Calculation/TextData/LEN.php';
    }

    /**
     * @dataProvider providerSEARCH
     */
    public function testSEARCH()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'SEARCHINSENSITIVE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerSEARCH()
    {
        return require 'data/Calculation/TextData/SEARCH.php';
    }

    /**
     * @dataProvider providerFIND
     */
    public function testFIND()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'SEARCHSENSITIVE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFIND()
    {
        return require 'data/Calculation/TextData/FIND.php';
    }

    /**
     * @dataProvider providerREPLACE
     */
    public function testREPLACE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'REPLACE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerREPLACE()
    {
        return require 'data/Calculation/TextData/REPLACE.php';
    }

    /**
     * @dataProvider providerSUBSTITUTE
     */
    public function testSUBSTITUTE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'SUBSTITUTE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerSUBSTITUTE()
    {
        return require 'data/Calculation/TextData/SUBSTITUTE.php';
    }

    /**
     * @dataProvider providerTRIM
     */
    public function testTRIM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'TRIMSPACES'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTRIM()
    {
        return require 'data/Calculation/TextData/TRIM.php';
    }

    /**
     * @dataProvider providerCLEAN
     */
    public function testCLEAN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'TRIMNONPRINTABLE'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCLEAN()
    {
        return require 'data/Calculation/TextData/CLEAN.php';
    }

    /**
     * @dataProvider providerDOLLAR
     */
    public function testDOLLAR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'DOLLAR'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDOLLAR()
    {
        return require 'data/Calculation/TextData/DOLLAR.php';
    }

    /**
     * @dataProvider providerFIXED
     */
    public function testFIXED()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'FIXEDFORMAT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFIXED()
    {
        return require 'data/Calculation/TextData/FIXED.php';
    }

    /**
     * @dataProvider providerT
     */
    public function testT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'RETURNSTRING'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerT()
    {
        return require 'data/Calculation/TextData/T.php';
    }

    /**
     * @dataProvider providerTEXT
     */
    public function testTEXT()
    {
        //    Enforce decimal and thousands separator values to UK/US, and currency code to USD
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
        StringHelper::setCurrencyCode('$');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'TEXTFORMAT'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTEXT()
    {
        return require 'data/Calculation/TextData/TEXT.php';
    }

    /**
     * @dataProvider providerVALUE
     */
    public function testVALUE()
    {
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(' ');
        StringHelper::setCurrencyCode('$');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([TextData::class, 'VALUE'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerVALUE()
    {
        return require 'data/Calculation/TextData/VALUE.php';
    }
}
