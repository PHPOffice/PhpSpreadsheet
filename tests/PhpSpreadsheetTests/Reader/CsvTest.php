<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
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
                __DIR__ . '/../../data/Reader/CSV/line_break_in_enclosure_with_escaped_quotes.csv',
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
            [
                'data/Reader/CSV/empty.csv',
                ',',
                'A1',
                null,
            ],
            [
                'data/Reader/CSV/no_delimiter.csv',
                ',',
                'A1',
                'SingleLine',
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

    /**
     * @dataProvider providerEncodings
     *
     * @param string $filename
     * @param string $encoding
     */
    public function testEncodings($filename, $encoding)
    {
        $reader = new Csv();
        $reader->setInputEncoding($encoding);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('Å', $sheet->getCell('A1')->getValue());
    }

    public function testInvalidWorkSheetInfo()
    {
        $this->expectException(ReaderException::class);
        $reader = new Csv();
        $reader->listWorksheetInfo('');
    }

    /**
     * @dataProvider providerEncodings
     *
     * @param string $filename
     * @param string $encoding
     */
    public function testWorkSheetInfo($filename, $encoding)
    {
        $reader = new Csv();
        $reader->setInputEncoding($encoding);
        $info = $reader->listWorksheetInfo($filename);
        self::assertEquals('Worksheet', $info[0]['worksheetName']);
        self::assertEquals('B', $info[0]['lastColumnLetter']);
        self::assertEquals(1, $info[0]['lastColumnIndex']);
        self::assertEquals(2, $info[0]['totalRows']);
        self::assertEquals(2, $info[0]['totalColumns']);
    }

    public function providerEncodings()
    {
        return [
            ['data/Reader/CSV/encoding.iso88591.csv', 'ISO-8859-1'],
            ['data/Reader/CSV/encoding.utf8.csv', 'UTF-8'],
            ['data/Reader/CSV/encoding.utf8bom.csv', 'UTF-8'],
            ['data/Reader/CSV/encoding.utf16be.csv', 'UTF-16BE'],
            ['data/Reader/CSV/encoding.utf16le.csv', 'UTF-16LE'],
            ['data/Reader/CSV/encoding.utf32be.csv', 'UTF-32BE'],
            ['data/Reader/CSV/encoding.utf32le.csv', 'UTF-32LE'],
        ];
    }

    public function testUtf16LineBreak()
    {
        $reader = new Csv();
        $reader->setInputEncoding('UTF-16BE');
        $spreadsheet = $reader->load('data/Reader/CSV/utf16be.line_break_in_enclosure.csv');
        $sheet = $spreadsheet->getActiveSheet();
        $expected = <<<EOF
This is a test
with line breaks
that breaks the
delimiters
EOF;
        self::assertEquals($expected, $sheet->getCell('B3')->getValue());
    }

    public function testSeparatorLine()
    {
        $reader = new Csv();
        $reader->setSheetIndex(3);
        $spreadsheet = $reader->load('data/Reader/CSV/sep.csv');
        self::assertEquals(';', $reader->getDelimiter());
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals(3, $reader->getSheetIndex());
        self::assertEquals(3, $spreadsheet->getActiveSheetIndex());
        self::assertEquals('A', $sheet->getCell('A1')->getValue());
        self::assertEquals(1, $sheet->getCell('B1')->getValue());
        self::assertEquals(2, $sheet->getCell('A2')->getValue());
        self::assertEquals(3, $sheet->getCell('B2')->getValue());
    }

    public function testDefaultSettings()
    {
        $reader = new Csv();
        self::assertEquals('UTF-8', $reader->getInputEncoding());
        self::assertEquals('"', $reader->getEnclosure());
        $reader->setEnclosure('\'');
        self::assertEquals('\'', $reader->getEnclosure());
        $reader->setEnclosure('');
        self::assertEquals('"', $reader->getEnclosure());
    }

    public function testReadEmptyFileName()
    {
        $this->expectException(ReaderException::class);
        $reader = new Csv();
        $filename = '';
        $reader->load($filename);
    }

    public function testReadNonexistentFileName()
    {
        $this->expectException(ReaderException::class);
        $reader = new Csv();
        $reader->load('data/Reader/CSV/encoding.utf8.csvxxx');
    }
}
