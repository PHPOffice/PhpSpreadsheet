<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $decimalSeparator;

    /**
     * @var string
     */
    private $thousandsSeparator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyCode = StringHelper::getCurrencyCode();
        $this->decimalSeparator = StringHelper::getDecimalSeparator();
        $this->thousandsSeparator = StringHelper::getThousandsSeparator();
    }

    protected function tearDown(): void
    {
        StringHelper::setCurrencyCode($this->currencyCode);
        StringHelper::setDecimalSeparator($this->decimalSeparator);
        StringHelper::setThousandsSeparator($this->thousandsSeparator);
    }

    public function testGetIsIconvEnabled(): void
    {
        $result = StringHelper::getIsIconvEnabled();
        self::assertTrue($result);
    }

    public function testGetDecimalSeparator(): void
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['decimal_point'])) ? $localeconv['decimal_point'] : ',';
        $result = StringHelper::getDecimalSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetDecimalSeparator(): void
    {
        $expectedResult = ',';
        StringHelper::setDecimalSeparator($expectedResult);

        $result = StringHelper::getDecimalSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testGetThousandsSeparator(): void
    {
        $localeconv = localeconv();

        $expectedResult = (!empty($localeconv['thousands_sep'])) ? $localeconv['thousands_sep'] : ',';
        $result = StringHelper::getThousandsSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetThousandsSeparator(): void
    {
        $expectedResult = ' ';
        StringHelper::setThousandsSeparator($expectedResult);

        $result = StringHelper::getThousandsSeparator();
        self::assertEquals($expectedResult, $result);
    }

    public function testGetCurrencyCode(): void
    {
        $localeconv = localeconv();
        $expectedResult = (!empty($localeconv['currency_symbol']) ? $localeconv['currency_symbol'] : (!empty($localeconv['int_curr_symbol']) ? $localeconv['int_curr_symbol'] : '$'));
        $result = StringHelper::getCurrencyCode();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetCurrencyCode(): void
    {
        $expectedResult = '£';
        StringHelper::setCurrencyCode($expectedResult);

        $result = StringHelper::getCurrencyCode();
        self::assertEquals($expectedResult, $result);
    }

    public function testControlCharacterPHP2OOXML(): void
    {
        $expectedResult = 'foo_x000B_bar';
        $result = StringHelper::controlCharacterPHP2OOXML('foo' . chr(11) . 'bar');

        self::assertEquals($expectedResult, $result);
    }

    public function testControlCharacterOOXML2PHP(): void
    {
        $expectedResult = 'foo' . chr(11) . 'bar';
        $result = StringHelper::controlCharacterOOXML2PHP('foo_x000B_bar');

        self::assertEquals($expectedResult, $result);
    }

    public function testSYLKtoUTF8(): void
    {
        $expectedResult = 'foo' . chr(11) . 'bar';
        $result = StringHelper::SYLKtoUTF8("foo\x1B ;bar");

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider providerFractions
     */
    public function testFraction(string $expected, string $value): void
    {
        $originalValue = $value;
        $result = StringHelper::convertToNumberIfFraction($value);
        if ($result === false) {
            self::assertSame($expected, $originalValue);
            self::assertSame($expected, $value);
        } else {
            self::assertSame($expected, (string) $value);
            self::assertNotEquals($value, $originalValue);
        }
    }

    public function providerFractions(): array
    {
        return [
            'non-fraction' => ['1', '1'],
            'common fraction' => ['1.5', '1 1/2'],
            'fraction between -1 and 0' => ['-0.5', '-1/2'],
            'fraction between -1 and 0 with space' => ['-0.5', ' - 1/2'],
            'fraction between 0 and 1' => ['0.75', '3/4 '],
            'fraction between 0 and 1 with space' => ['0.75', ' 3/4'],
            'improper fraction' => ['1.75', '7/4'],
        ];
    }
}
