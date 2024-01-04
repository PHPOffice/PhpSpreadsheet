<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvNumberFormatLocaleTest extends TestCase
{
    private bool $localeAdjusted;

    /**
     * @var false|string
     */
    private $currentLocale;

    protected string $filename;

    protected Csv $csvReader;

    protected function setUp(): void
    {
        $this->currentLocale = setlocale(LC_ALL, '0');

        if (!setlocale(LC_ALL, 'de_DE.UTF-8', 'deu_deu.utf8')) {
            $this->localeAdjusted = false;

            return;
        }

        $this->localeAdjusted = true;

        $this->filename = 'tests/data/Reader/CSV/NumberFormatTest.de.csv';
        $this->csvReader = new Csv();
    }

    protected function tearDown(): void
    {
        if ($this->localeAdjusted && is_string($this->currentLocale)) {
            setlocale(LC_ALL, $this->currentLocale);
        }
    }

    /**
     * @dataProvider providerNumberFormatNoConversionTest
     *
     * @runInSeparateProcess
     */
    public function testNumberFormatNoConversion(mixed $expectedValue, string $expectedFormat, string $cellAddress): void
    {
        if (!$this->localeAdjusted) {
            self::markTestSkipped('Unable to set locale for testing.');
        }
        $localeconv = localeconv();
        self::assertSame(',', $localeconv['decimal_point'], 'unexpected change to German decimal separator');
        self::assertSame('.', $localeconv['thousands_sep'], 'unexpected change to German thousands separator');

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
                '12.345,67',
                '12.345,67',
                'C1',
            ],
            [
                '-1.234,567',
                '-1.234,567',
                'A3',
            ],
        ];
    }

    /**
     * @dataProvider providerNumberValueConversionTest
     *
     * @runInSeparateProcess
     */
    public function testNumberValueConversion(mixed $expectedValue, string $cellAddress): void
    {
        if (!$this->localeAdjusted) {
            self::markTestSkipped('Unable to set locale for testing.');
        }
        $localeconv = localeconv();
        self::assertSame(',', $localeconv['decimal_point'], 'unexpected change to German decimal separator');
        self::assertSame('.', $localeconv['thousands_sep'], 'unexpected change to German thousands separator');

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
        ];
    }
}
