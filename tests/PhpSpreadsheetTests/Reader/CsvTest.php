<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvTest extends TestCase
{
    /**
     * @dataProvider providerDelimiterDetection
     *
     * @param string $filename
     * @param string $expectedDelimiter
     * @param string $cell
     * @param float|int|string $expectedValue
     */
    public function testDelimiterDetection($filename, $expectedDelimiter, $cell, $expectedValue)
    {
        $reader = new Csv();
        self::assertNull($reader->getDelimiter());

        $spreadsheet = $reader->load($filename);

        self::assertSame($expectedDelimiter, $reader->getDelimiter(), 'should be able to infer the delimiter');

        $actual = $spreadsheet->getActiveSheet()->getCell($cell)->getValue();
        self::assertSame($expectedValue, $actual, 'should be able to retrieve correct value');
    }

    public function providerDelimiterDetection()
    {
        return [
            [
                __DIR__ . '/../../data/Reader/CSV/enclosure.csv',
                ',',
                'C4',
                'username2',
            ],
            [
                __DIR__ . '/../../data/Reader/CSV/semicolon_separated.csv',
                ';',
                'C2',
                '25,5',
            ],
            [
                __DIR__ . '/../../data/Reader/HTML/csv_with_angle_bracket.csv',
                ',',
                'B1',
                'Number of items with weight <= 50kg',
            ],
            [
                __DIR__ . '/../../../samples/Reader/sampleData/example1.csv',
                ',',
                'I4',
                '100%',
            ],
            [
                __DIR__ . '/../../../samples/Reader/sampleData/example2.csv',
                ',',
                'D8',
                -58.373161,
            ],
        ];
    }
}
