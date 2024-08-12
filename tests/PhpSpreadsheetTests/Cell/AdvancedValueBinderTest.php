<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AdvancedValueBinderTest extends TestCase
{
    const AVB_PRECISION = 1.0E-8;

    private string $originalLocale;

    private IValueBinder $valueBinder;

    protected function setUp(): void
    {
        $this->originalLocale = Settings::getLocale();

        $this->valueBinder = Cell::getValueBinder();
        Cell::setValueBinder(new AdvancedValueBinder());
    }

    protected function tearDown(): void
    {
        StringHelper::setCurrencyCode(null);
        StringHelper::setDecimalSeparator(null);
        StringHelper::setThousandsSeparator(null);
        Settings::setLocale($this->originalLocale);
        Cell::setValueBinder($this->valueBinder);
    }

    public function testNullValue(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue(null);
        self::assertNull($sheet->getCell('A1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testBoolean(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue(true);
        self::assertTrue($sheet->getCell('A1')->getValue());

        $sheet->getCell('A2')->setValue(false);
        self::assertFalse($sheet->getCell('A2')->getValue());

        $sheet->getCell('A3')->setValue('true');
        self::assertTrue($sheet->getCell('A3')->getValue());

        $sheet->getCell('A4')->setValue('false');
        self::assertFalse($sheet->getCell('A4')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testBooleanLocale(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        Settings::setLocale('nl_NL');

        $sheet->getCell('A1')->setValue('Waar');
        self::assertTrue($sheet->getCell('A1')->getValue());

        $sheet->getCell('A2')->setValue('OnWaar');
        self::assertFalse($sheet->getCell('A2')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider currencyProvider
     */
    public function testCurrency(string $value, float $valueBinded, string $thousandsSeparator, string $decimalSeparator, string $currencyCode): void
    {
        StringHelper::setCurrencyCode($currencyCode);
        StringHelper::setDecimalSeparator($decimalSeparator);
        StringHelper::setThousandsSeparator($thousandsSeparator);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue($value);
        self::assertEquals($valueBinded, $sheet->getCell('A1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public static function currencyProvider(): array
    {
        return [
            ['$10.11', 10.11, ',', '.', '$'],
            ['$1,010.12', 1010.12, ',', '.', '$'],
            ['$20,20', 20.2, '.', ',', '$'],
            ['$2.020,20', 2020.2, '.', ',', '$'],
            ['€2.020,20', 2020.2, '.', ',', '€'],
            ['€ 2.020,20', 2020.2, '.', ',', '€'],
            ['€2,020.22', 2020.22, ',', '.', '€'],
            ['$10.11', 10.11, ',', '.', '€'],
            ['€2,020.20', 2020.2, ',', '.', '$'],
            'slash as group separator' => ['€2/020.20', 2020.2, '/', '.', '$'],
            'slash as decimal separator' => ['€2,020/20', 2020.2, ',', '/', '$'],
            ['-2,020.20€', -2020.2, ',', '.', '$'],
            ['- 2,020.20 € ', -2020.2, ',', '.', '$'],
        ];
    }

    /**
     * @dataProvider fractionProvider
     */
    public function testFractions(mixed $value, mixed $valueBinded): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue($value);
        self::assertEquals($valueBinded, $sheet->getCell('A1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public static function fractionProvider(): array
    {
        return [
            ['1/5', 0.2],
            ['-1/5', -0.2],
            ['- 1/5', -0.2],
            ['12/5', 2.4],
            ['2/100', 0.02],
            ['15/12', 1.25],
            ['20/100', 0.2],
            ['1 3/5', 1.6],
            ['-1 3/5', -1.6],
            ['1 4/20', 1.2],
            ['1 16/20', 1.8],
            ['12 20/100', 12.2],
            ['-1 4/20', -1.2],
            ['407 / ', '407 / '],
            ['407 /', '407 /'],
            ['407 3/', '407 3/'],
            ['-407 /4', -101.75],
            [' /', ' /'],
            [' / ', ' / '],
        ];
    }

    /**
     * @dataProvider percentageProvider
     */
    public function testPercentages(mixed $value, mixed $valueBinded): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue($value);
        self::assertEquals($valueBinded, $sheet->getCell('A1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public static function percentageProvider(): array
    {
        return [
            ['10%', 0.1],
            ['-12%', -0.12],
            ['120%', 1.2],
            ['12.5%', 0.125],
            ['-12.5%', -0.125],
            ['12,345%', 123.45],
            ['12,345.67%', 123.4567],
        ];
    }

    /**
     * @dataProvider timeProvider
     */
    public function testTimes(mixed $value, mixed $valueBinded): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue($value);
        self::assertEqualsWithDelta($valueBinded, $sheet->getCell('A1')->getValue(), self::AVB_PRECISION);

        $spreadsheet->disconnectWorksheets();
    }

    public static function timeProvider(): array
    {
        return [
            ['1:20', 0.05555555556],
            ['09:17', 0.386805555556],
            ['15:00', 0.625],
            ['17:12:35', 0.71707175926],
            ['23:58:20', 0.99884259259],
        ];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testStringWrapping(string $value): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue($value);
        self::assertEquals($value, $sheet->getCell('A1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public static function stringProvider(): array
    {
        return [
            ['Hello World', false],
            ["Hello\nWorld", true],
        ];
    }

    /**
     * @dataProvider formulaProvider
     */
    public function testFormula(string $value, string $dataType): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('A1')->setValue($value);
        self::assertSame($dataType, $sheet->getCell('A1')->getDataType());
        if ($dataType === DataType::TYPE_FORMULA) {
            self::assertFalse($sheet->getStyle('A1')->getQuotePrefix());
        } else {
            self::assertTrue($sheet->getStyle('A1')->getQuotePrefix());
        }

        $spreadsheet->disconnectWorksheets();
    }

    public static function formulaProvider(): array
    {
        return [
            'normal formula' => ['=SUM(A1:C3)', DataType::TYPE_FORMULA],
            'issue 1310' => ['======', DataType::TYPE_STRING],
        ];
    }
}
