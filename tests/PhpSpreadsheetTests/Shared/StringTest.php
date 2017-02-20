<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Reset Currency Code
        StringHelper::setCurrencyCode(null);
    }

    public function testGetIsIconvEnabled()
    {
        $result = StringHelper::getIsIconvEnabled();
        $this->assertTrue($result);
    }

    public function testGetDecimalSeparator()
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['decimal_point'])) ? $localeconv['decimal_point'] : ',';
        $result = StringHelper::getDecimalSeparator();
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetDecimalSeparator()
    {
        $expectedResult = ',';
        StringHelper::setDecimalSeparator($expectedResult);

        $result = StringHelper::getDecimalSeparator();
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetThousandsSeparator()
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['thousands_sep'])) ? $localeconv['thousands_sep'] : ',';
        $result = StringHelper::getThousandsSeparator();
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetThousandsSeparator()
    {
        $expectedResult = ' ';
        StringHelper::setThousandsSeparator($expectedResult);

        $result = StringHelper::getThousandsSeparator();
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetCurrencyCode()
    {
        $localeconv = localeconv();
        $expectedResult = (!empty($localeconv['currency_symbol']) ? $localeconv['currency_symbol'] : (!empty($localeconv['int_curr_symbol']) ? $localeconv['int_curr_symbol'] : '$'));
        $result = StringHelper::getCurrencyCode();
        $this->assertEquals($expectedResult, $result);
    }

    public function testSetCurrencyCode()
    {
        $expectedResult = '£';
        StringHelper::setCurrencyCode($expectedResult);

        $result = StringHelper::getCurrencyCode();
        $this->assertEquals($expectedResult, $result);
    }
}
