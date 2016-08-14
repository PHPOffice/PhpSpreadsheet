<?php

namespace PhpSpreadsheet\Tests\Calculation;

use PHPExcel\Shared\StringHelper;
use PHPExcel\Calculation\Functions;
use PHPExcel\Calculation\TextData;

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
        $result = call_user_func_array(array(TextData::class,'CHARACTER'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCHAR()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/CHAR.data');
    }

    /**
     * @dataProvider providerCODE
     */
    public function testCODE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'ASCIICODE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCODE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/CODE.data');
    }

    /**
     * @dataProvider providerCONCATENATE
     */
    public function testCONCATENATE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'CONCATENATE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCONCATENATE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/CONCATENATE.data');
    }

    /**
     * @dataProvider providerLEFT
     */
    public function testLEFT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'LEFT'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLEFT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/LEFT.data');
    }

    /**
     * @dataProvider providerMID
     */
    public function testMID()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'MID'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerMID()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/MID.data');
    }

    /**
     * @dataProvider providerRIGHT
     */
    public function testRIGHT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'RIGHT'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerRIGHT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/RIGHT.data');
    }

    /**
     * @dataProvider providerLOWER
     */
    public function testLOWER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'LOWERCASE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLOWER()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/LOWER.data');
    }

    /**
     * @dataProvider providerUPPER
     */
    public function testUPPER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'UPPERCASE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerUPPER()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/UPPER.data');
    }

    /**
     * @dataProvider providerPROPER
     */
    public function testPROPER()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'PROPERCASE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerPROPER()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/PROPER.data');
    }

    /**
     * @dataProvider providerLEN
     */
    public function testLEN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'STRINGLENGTH'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerLEN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/LEN.data');
    }

    /**
     * @dataProvider providerSEARCH
     */
    public function testSEARCH()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'SEARCHINSENSITIVE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerSEARCH()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/SEARCH.data');
    }

    /**
     * @dataProvider providerFIND
     */
    public function testFIND()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'SEARCHSENSITIVE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFIND()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/FIND.data');
    }

    /**
     * @dataProvider providerREPLACE
     */
    public function testREPLACE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'REPLACE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerREPLACE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/REPLACE.data');
    }

    /**
     * @dataProvider providerSUBSTITUTE
     */
    public function testSUBSTITUTE()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'SUBSTITUTE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerSUBSTITUTE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/SUBSTITUTE.data');
    }

    /**
     * @dataProvider providerTRIM
     */
    public function testTRIM()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'TRIMSPACES'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTRIM()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/TRIM.data');
    }

    /**
     * @dataProvider providerCLEAN
     */
    public function testCLEAN()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'TRIMNONPRINTABLE'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCLEAN()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/CLEAN.data');
    }

    /**
     * @dataProvider providerDOLLAR
     */
    public function testDOLLAR()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'DOLLAR'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDOLLAR()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/DOLLAR.data');
    }

    /**
     * @dataProvider providerFIXED
     */
    public function testFIXED()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'FIXEDFORMAT'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerFIXED()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/FIXED.data');
    }

    /**
     * @dataProvider providerT
     */
    public function testT()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class,'RETURNSTRING'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/T.data');
    }

    /**
     * @dataProvider providerTEXT
     */
    public function testTEXT()
    {
        //    Enforce decimal and thousands separator values to UK/US, and currency code to USD
        call_user_func(array(StringHelper::class, 'setDecimalSeparator'), '.');
        call_user_func(array(StringHelper::class, 'setThousandsSeparator'), ',');
        call_user_func(array(StringHelper::class, 'setCurrencyCode'), '$');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class, 'TEXTFORMAT'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTEXT()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/TEXT.data');
    }

    /**
     * @dataProvider providerVALUE
     */
    public function testVALUE()
    {
        call_user_func(array(StringHelper::class, 'setDecimalSeparator'), '.');
        call_user_func(array(StringHelper::class, 'setThousandsSeparator'), ' ');
        call_user_func(array(StringHelper::class, 'setCurrencyCode'), '$');

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array(TextData::class, 'VALUE'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerVALUE()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Calculation/TextData/VALUE.data');
    }
}
