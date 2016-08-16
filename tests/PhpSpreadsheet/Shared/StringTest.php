<?php

namespace PhpSpreadsheet\Tests\Shared;

use PhpSpreadsheet\Shared\StringHelper;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Reset Currency Code
        call_user_func(array(StringHelper::class,'setCurrencyCode'), null);
    }

    public function testGetIsMbStringEnabled()
    {
        $result = call_user_func(array(StringHelper::class,'getIsMbstringEnabled'));
        $this->assertTrue($result);
    }

    public function testGetIsIconvEnabled()
    {
        $result = call_user_func(array(StringHelper::class,'getIsIconvEnabled'));
        $this->assertTrue($result);
    }

    public function testGetDecimalSeparator()
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['decimal_point'])) ? $localeconv['decimal_point'] : ',';
        $result = call_user_func(array(StringHelper::class,'getDecimalSeparator'));
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetDecimalSeparator()
    {
        $expectedResult = ',';
        call_user_func(array(StringHelper::class,'setDecimalSeparator'), $expectedResult);

        $result = call_user_func(array(StringHelper::class,'getDecimalSeparator'));
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetThousandsSeparator()
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['thousands_sep'])) ? $localeconv['thousands_sep'] : ',';
        $result = call_user_func(array(StringHelper::class,'getThousandsSeparator'));
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetThousandsSeparator()
    {
        $expectedResult = ' ';
        call_user_func(array(StringHelper::class,'setThousandsSeparator'), $expectedResult);

        $result = call_user_func(array(StringHelper::class,'getThousandsSeparator'));
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetCurrencyCode()
    {
        $localeconv = localeconv();
        $expectedResult = (!empty($localeconv['currency_symbol']) ? $localeconv['currency_symbol'] : (!empty($localeconv['int_curr_symbol']) ? $localeconv['int_curr_symbol']: '$'));
        $result = call_user_func(array(StringHelper::class,'getCurrencyCode'));
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetCurrencyCode()
    {
        $expectedResult = '£';
        call_user_func(array(StringHelper::class,'setCurrencyCode'), $expectedResult);

        $result = call_user_func(array(StringHelper::class,'getCurrencyCode'));
        $this->assertEquals($expectedResult, $result);
    }
}
