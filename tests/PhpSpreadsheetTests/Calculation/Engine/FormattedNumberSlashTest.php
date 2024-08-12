<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\FormattedNumber;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PHPUnit\Framework\TestCase;

class FormattedNumberSlashTest extends TestCase
{
    protected function tearDown(): void
    {
        StringHelper::setCurrencyCode(null);
        StringHelper::setDecimalSeparator(null);
        StringHelper::setThousandsSeparator(null);
    }

    /**
     * @dataProvider providerNumbers
     */
    public function testNumber(float $expected, string $value, string $thousandsSeparator = ',', string $decimalSeparator = '.'): void
    {
        StringHelper::setThousandsSeparator($thousandsSeparator);
        StringHelper::setDecimalSeparator($decimalSeparator);
        $result = FormattedNumber::convertToNumberIfFormatted($value);
        self::assertTrue($result);
        self::assertSame($expected, $value);
    }

    public static function providerNumbers(): array
    {
        return [
            'normal' => [1234.5, '1,234.5'],
            'slash as thousands separator' => [-1234.5, '- 1/234.5', '/', '.'],
            'slash as decimal separator' => [-1234.5, '- 1,234/5', ',', '/'],
        ];
    }

    /**
     * @dataProvider providerPercentages
     */
    public function testPercentage(string $expected, string $value, string $thousandsSeparator = ',', string $decimalSeparator = '.'): void
    {
        $originalValue = $value;
        StringHelper::setThousandsSeparator($thousandsSeparator);
        StringHelper::setDecimalSeparator($decimalSeparator);
        $result = FormattedNumber::convertToNumberIfPercent($value);
        self::assertTrue($result);
        self::assertSame($expected, (string) $value);
        self::assertNotEquals($value, $originalValue);
    }

    public static function providerPercentages(): array
    {
        return [
            'normal' => ['21.5034', '2,150.34%'],
            'slash as thousands separator' => ['21.5034', '2/150.34%', '/', '.'],
            'slash as decimal separator' => ['21.5034', '2,150/34%', ',', '/'],
        ];
    }

    /**
     * @dataProvider providerCurrencies
     */
    public function testCurrencies(string $expected, string $value, string $thousandsSeparator = ',', string $decimalSeparator = '.', ?string $currencyCode = null): void
    {
        $originalValue = $value;
        StringHelper::setThousandsSeparator($thousandsSeparator);
        StringHelper::setDecimalSeparator($decimalSeparator);
        if ($currencyCode !== null) {
            StringHelper::setCurrencyCode($currencyCode);
        }
        $result = FormattedNumber::convertToNumberIfCurrency($value);
        self::assertTrue($result);
        self::assertSame($expected, (string) $value);
        self::assertNotEquals($value, $originalValue);
    }

    public static function providerCurrencies(): array
    {
        return [
            'switched delimiters' => ['2134.56', '$2.134,56', '.', ','],
            'normal' => ['2134.56', '$2,134.56'],
            'slash as thousands separator' => ['2134.56', '$2/134.56', '/', '.'],
            'slash as decimal separator' => ['2134.56', '$2,134/56', ',', '/'],
            'slash as currency code' => ['2134.56', '/2,134.56', ',', '.', '/'],
        ];
    }
}
