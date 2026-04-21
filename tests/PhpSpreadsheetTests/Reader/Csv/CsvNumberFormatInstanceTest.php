<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Same as CsvNumberFormatLocaleTest, except that
 * instance properties are used rather than static/locale.
 */
class CsvNumberFormatInstanceTest extends TestCase
{
    protected string $filename = 'tests/data/Reader/CSV/NumberFormatTest.de.csv';

    protected Csv $csvReader;

    #[DataProvider('providerNumberFormatNoConversionTest')]
    public function testNumberFormatNoConversion(mixed $expectedValue, string $expectedFormat, string $cellAddress): void
    {
        $csvReader = new Csv();
        $spreadsheet = $csvReader->load($this->filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($cellAddress);
        self::assertSame($expectedValue, $cell->getValue(), 'Expected value check');
        self::assertSame($expectedFormat, $cell->getFormattedValue(), 'Format mask check');
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerNumberFormatNoConversionTest(): array
    {
        return [
            [
                -123,
                '-123',
                'A1',
            ],
            [
                '12.345,67',
                '12.345,67',
                'C1',
            ],
            [
                '-1.234,567',
                '-1.234,567',
                'A3',
            ],
            [
                'WAHR',
                'WAHR',
                'A4',
            ],
            [
                'FALSCH',
                'FALSCH',
                'B4',
            ],
            [
                false,
                'FALSE',
                'A5',
            ],
            [
                true,
                'TRUE',
                'B5',
            ],
        ];
    }

    #[DataProvider('providerNumberValueConversionTest')]
    public function testNumberValueConversion(mixed $expectedValue, string $cellAddress): void
    {
        $csvReader = new Csv();
        $binder = new StringValueBinder();
        $binder->setNumericConversion(false)
            ->setBooleanConversion(false);
        $csvReader->setValueBinder($binder)
            ->setGetTrue('WAHR')
            ->setGetFalse('FALSCH')
            ->setDecimalSeparator(',')
            ->setThousandsSeparator('.')
            ->castFormattedNumberToNumeric(true);
        $spreadsheet = $csvReader->load($this->filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($cellAddress);
        if (is_bool($expectedValue)) {
            self::assertSame(DataType::TYPE_BOOL, $cell->getDataType(), 'Datatype check');
        } else {
            self::assertSame(DataType::TYPE_NUMERIC, $cell->getDataType(), 'Datatype check');
        }
        self::assertSame($expectedValue, $cell->getValue(), 'Expected value check');
    }

    public static function providerNumberValueConversionTest(): array
    {
        return [
            'A1' => [
                -123,
                'A1',
            ],
            'B1' => [
                1234,
                'B1',
            ],
            'C1' => [
                12345.67,
                'C1',
            ],
            'A2' => [
                123.4567,
                'A2',
            ],
            'B2' => [
                123.456789012,
                'B2',
            ],
            'A3' => [
                -1234.567,
                'A3',
            ],
            'A4' => [
                true,
                'A4',
            ],
            'B4' => [
                false,
                'B4',
            ],
            'A5' => [
                false,
                'A5',
            ],
            'B5' => [
                true,
                'B5',
            ],
        ];
    }
}
