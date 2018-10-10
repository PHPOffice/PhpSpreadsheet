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
                __DIR__ . '/../../data/Reader/CSV/line_break_in_enclosure.csv',
                ',',
                'A3',
                'Test',
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

    /**
     * @dataProvider providerCanLoad
     *
     * @param bool $expected
     * @param string $filename
     */
    public function testCanLoad($expected, $filename)
    {
        $reader = new Csv();
        self::assertSame($expected, $reader->canRead($filename));
    }

    public function providerCanLoad()
    {
        return [
            [false, 'data/Reader/Ods/data.ods'],
            [false, 'data/Reader/Xml/WithoutStyle.xml'],
            [true, 'data/Reader/CSV/enclosure.csv'],
            [true, 'data/Reader/CSV/semicolon_separated.csv'],
            [true, 'data/Reader/CSV/contains_html.csv'],
            [true, 'data/Reader/CSV/csv_without_extension'],
            [true, 'data/Reader/HTML/csv_with_angle_bracket.csv'],
            [true, 'data/Reader/CSV/empty.csv'],
            [true, '../samples/Reader/sampleData/example1.csv'],
            [true, '../samples/Reader/sampleData/example2.csv'],
        ];
    }

    public function testEscapeCharacters()
    {
        $reader = (new Csv())->setEscapeCharacter('"');
        $worksheet = $reader->load(__DIR__ . '/../../data/Reader/CSV/backslash.csv')
            ->getActiveSheet();

        $expected = [
            ['field 1', 'field 2\\'],
            ['field 3\\', 'field 4'],
        ];

        $this->assertSame('"', $reader->getEscapeCharacter());
        $this->assertSame($expected, $worksheet->toArray());
    }
}
