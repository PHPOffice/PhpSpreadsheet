<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
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
        self::assertTrue($result);
    }

    public function testGetDecimalSeparator()
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['decimal_point'])) ? $localeconv['decimal_point'] : ',';
        $result = StringHelper::getDecimalSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetDecimalSeparator()
    {
        $expectedResult = ',';
        StringHelper::setDecimalSeparator($expectedResult);

        $result = StringHelper::getDecimalSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testGetThousandsSeparator()
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['thousands_sep'])) ? $localeconv['thousands_sep'] : ',';
        $result = StringHelper::getThousandsSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetThousandsSeparator()
    {
        $expectedResult = ' ';
        StringHelper::setThousandsSeparator($expectedResult);

        $result = StringHelper::getThousandsSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testGetCurrencyCode()
    {
        $localeconv = localeconv();
        $expectedResult = (!empty($localeconv['currency_symbol']) ? $localeconv['currency_symbol'] : (!empty($localeconv['int_curr_symbol']) ? $localeconv['int_curr_symbol'] : '$'));
        $result = StringHelper::getCurrencyCode();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetCurrencyCode()
    {
        $expectedResult = '£';
        StringHelper::setCurrencyCode($expectedResult);

        $result = StringHelper::getCurrencyCode();
        self::assertEquals($expectedResult, $result);
    }

    public function testControlCharacterPHP2OOXML()
    {
        $expectedResult = 'foo_x000B_bar';
        $result = StringHelper::controlCharacterPHP2OOXML('foo' . chr(11) . 'bar');

        self::assertEquals($expectedResult, $result);
    }

    public function testControlCharacterOOXML2PHP()
    {
        $expectedResult = 'foo' . chr(11) . 'bar';
        $result = StringHelper::controlCharacterOOXML2PHP('foo_x000B_bar');

        self::assertEquals($expectedResult, $result);
    }

    public function testSYLKtoUTF8()
    {
        $expectedResult = 'foo' . chr(11) . 'bar';
        $result = StringHelper::SYLKtoUTF8("foo\x1B ;bar");

        self::assertEquals($expectedResult, $result);
    }
}
