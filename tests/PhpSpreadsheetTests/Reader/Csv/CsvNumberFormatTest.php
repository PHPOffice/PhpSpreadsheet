<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvNumberFormatTest extends TestCase
{
    protected string $filename;

    protected Csv $csvReader;

    protected function setUp(): void
    {
        $this->filename = 'tests/data/Reader/CSV/NumberFormatTest.csv';
        $this->csvReader = new Csv();
    }

    /**
     * @dataProvider providerNumberFormatNoConversionTest
     */
    public function testNumberFormatNoConversion(int|string $expectedValue, string $expectedFormat, string $cellAddress): void
    {
        $spreadsheet = $this->csvReader->load($this->filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell($cellAddress);

        self::assertSame($expectedValue, $cell->getValue(), 'Expected value check');
        self::assertSame($expectedFormat, $cell->getFormattedValue(), 'Format mask check');
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
                '12,345.67',
                '12,345.67',
                'C1',
            ],
            [
                '-1,234.567',
                '-1,234.567',
                'A3',
            ],
        ];
    }

    /**
     * @dataProvider providerNumberValueConversionTest
     */
    public function testNumberValueConversion(mixed $expectedValue, string $cellAddress): void
    {
        $this->csvReader->castFormattedNumberToNumeric(true);
        $spreadsheet = $this->csvReader->load($this->filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell($cellAddress);

        self::assertSame(DataType::TYPE_NUMERIC, $cell->getDataType(), 'Datatype check');
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
            'B3' => [
                1234.567,
                'B3',
            ],
        ];
    }

    /**
     * @dataProvider providerNumberFormatConversionTest
     */
    public function testNumberFormatConversion(mixed $expectedValue, string $expectedFormat, string $cellAddress): void
    {
        $this->csvReader->castFormattedNumberToNumeric(true, true);
        $spreadsheet = $this->csvReader->load($this->filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell($cellAddress);

        self::assertSame(DataType::TYPE_NUMERIC, $cell->getDataType(), 'Datatype check');
        self::assertSame($expectedValue, $cell->getValue(), 'Expected value check');
        self::assertSame($expectedFormat, $cell->getFormattedValue(), 'Format mask check');
    }

    public static function providerNumberFormatConversionTest(): array
    {
        return [
            'A1' => [
                -123,
                '-123',
                'A1',
            ],
            'B1' => [
                1234,
                '1,234',
                'B1',
            ],
            'C1' => [
                12345.67,
                '12,345.67',
                'C1',
            ],
            'A2' => [
                123.4567,
                '123.4567',
                'A2',
            ],
            'B2' => [
                123.456789012,
                '123.456789',
                'B2',
            ],
            'A3' => [
                -1234.567,
                '-1,234.567',
                'A3',
            ],
            'B3' => [
                1234.567,
                '1234.567',
                'B3',
            ],
        ];
    }
}
