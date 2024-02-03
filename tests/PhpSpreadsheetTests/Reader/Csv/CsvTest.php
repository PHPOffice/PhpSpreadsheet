<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PHPUnit\Framework\TestCase;

class CsvTest extends TestCase
{
    /**
     * @dataProvider providerDelimiterDetection
     */
    public function testDelimiterDetection(string $filename, string $expectedDelimiter, string $cell, string|float|int|null $expectedValue): void
    {
        $reader = new Csv();
        $delim1 = $reader->getDelimiter();
        self::assertNull($delim1);

        $spreadsheet = $reader->load($filename);

        self::assertSame($expectedDelimiter, $reader->getDelimiter(), 'should be able to infer the delimiter');

        $actual = $spreadsheet->getActiveSheet()->getCell($cell)->getValue();
        self::assertSame($expectedValue, $actual, 'should be able to retrieve correct value');
    }

    public static function providerDelimiterDetection(): array
    {
        return [
            [
                'tests/data/Reader/CSV/enclosure.csv',
                ',',
                'C4',
                'username2',
            ],
            [
                'tests/data/Reader/CSV/semicolon_separated.csv',
                ';',
                'C2',
                '25,5',
            ],
            [
                'tests/data/Reader/CSV/line_break_in_enclosure.csv',
                ',',
                'A3',
                'Test',
            ],
            [
                'tests/data/Reader/CSV/line_break_in_enclosure_with_escaped_quotes.csv',
                ',',
                'A3',
                'Test',
            ],
            [
                'tests/data/Reader/HTML/csv_with_angle_bracket.csv',
                ',',
                'B1',
                'Number of items with weight <= 50kg',
            ],
            [
                'samples/Reader2/sampleData/example1.csv',
                ',',
                'I4',
                '100%',
            ],
            [
                'samples/Reader2/sampleData/example2.csv',
                ',',
                'D8',
                -58.373161,
            ],
            [
                'tests/data/Reader/CSV/empty.csv',
                ',',
                'A1',
                null,
            ],
            [
                'tests/data/Reader/CSV/no_delimiter.csv',
                ',',
                'A1',
                'SingleLine',
            ],
        ];
    }

    /**
     * @dataProvider providerCanLoad
     */
    public function testCanLoad(bool $expected, string $filename): void
    {
        $reader = new Csv();
        self::assertSame($expected, $reader->canRead($filename));
    }

    public static function providerCanLoad(): array
    {
        return [
            [false, 'tests/data/Reader/Ods/data.ods'],
            [false, 'samples/templates/excel2003.xml'],
            [true, 'tests/data/Reader/CSV/enclosure.csv'],
            [true, 'tests/data/Reader/CSV/semicolon_separated.csv'],
            [true, 'tests/data/Reader/CSV/contains_html.csv'],
            [true, 'tests/data/Reader/CSV/csv_without_extension'],
            [true, 'tests/data/Reader/HTML/csv_with_angle_bracket.csv'],
            [true, 'tests/data/Reader/CSV/empty.csv'],
            [true, 'samples/Reader2/sampleData/example1.csv'],
            [true, 'samples/Reader2/sampleData/example2.csv'],
        ];
    }

    public function testEscapeCharacters(): void
    {
        $reader = (new Csv())->setEscapeCharacter('"');
        $worksheet = $reader->load('tests/data/Reader/CSV/backslash.csv')
            ->getActiveSheet();

        $expected = [
            ['field 1', 'field 2\\'],
            ['field 3\\', 'field 4'],
        ];

        self::assertSame('"', $reader->getEscapeCharacter());
        self::assertSame($expected, $worksheet->toArray());
    }

    public function testInvalidWorkSheetInfo(): void
    {
        $this->expectException(ReaderException::class);
        $reader = new Csv();
        $reader->listWorksheetInfo('');
    }

    public function testUtf16LineBreak(): void
    {
        $reader = new Csv();
        $reader->setInputEncoding('UTF-16BE');
        $spreadsheet = $reader->load('tests/data/Reader/CSV/utf16be.line_break_in_enclosure.csv');
        $sheet = $spreadsheet->getActiveSheet();
        $expected = <<<EOF
            This is a test
            with line breaks
            that breaks the
            delimiters
            EOF;
        self::assertEquals($expected, $sheet->getCell('B3')->getValue());
    }

    public function testLineBreakEscape(): void
    {
        $reader = new Csv();
        $spreadsheet = $reader->load('tests/data/Reader/CSV/line_break_in_enclosure_with_escaped_quotes.csv');
        $sheet = $spreadsheet->getActiveSheet();
        $expected = <<<EOF
            This is a "test csv file"
            with both "line breaks"
            and "escaped
            quotes" that breaks
            the delimiters
            EOF;
        self::assertEquals($expected, $sheet->getCell('B3')->getValue());
    }

    public function testUtf32LineBreakEscape(): void
    {
        $reader = new Csv();
        $reader->setInputEncoding('UTF-32LE');
        $spreadsheet = $reader->load('tests/data/Reader/CSV/line_break_escaped_32le.csv');
        $sheet = $spreadsheet->getActiveSheet();
        $expected = <<<EOF
            This is a "test csv file"
            with both "line breaks"
            and "escaped
            quotes" that breaks
            the delimiters
            EOF;
        self::assertEquals($expected, $sheet->getCell('B3')->getValue());
    }

    public function testSeparatorLine(): void
    {
        $reader = new Csv();
        $reader->setSheetIndex(3);
        $spreadsheet = $reader->load('tests/data/Reader/CSV/sep.csv');
        self::assertEquals(';', $reader->getDelimiter());
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals(3, $reader->getSheetIndex());
        self::assertEquals(3, $spreadsheet->getActiveSheetIndex());
        self::assertEquals('A', $sheet->getCell('A1')->getValue());
        self::assertEquals(1, $sheet->getCell('B1')->getValue());
        self::assertEquals(2, $sheet->getCell('A2')->getValue());
        self::assertEquals(3, $sheet->getCell('B2')->getValue());
    }

    public function testDefaultSettings(): void
    {
        $reader = new Csv();
        self::assertEquals('UTF-8', $reader->getInputEncoding());
        self::assertEquals('"', $reader->getEnclosure());
        $reader->setEnclosure('\'');
        self::assertEquals('\'', $reader->getEnclosure());
        $reader->setEnclosure('');
        self::assertEquals('"', $reader->getEnclosure());
        // following tests from BaseReader
        self::assertTrue($reader->getReadEmptyCells());
        self::assertFalse($reader->getIncludeCharts());
        self::assertNull($reader->getLoadSheetsOnly());
    }

    public function testReadEmptyFileName(): void
    {
        $this->expectException(ReaderException::class);
        $reader = new Csv();
        $filename = '';
        $reader->load($filename);
    }

    public function testReadNonexistentFileName(): void
    {
        $this->expectException(ReaderException::class);
        $reader = new Csv();
        $reader->load('tests/data/Reader/CSV/encoding.utf8.csvxxx');
    }

    /**
     * @dataProvider providerEscapes
     */
    public function testInferSeparator(string $escape, string $delimiter): void
    {
        $reader = new Csv();
        $reader->setEscapeCharacter($escape);
        $filename = 'tests/data/Reader/CSV/escape.csv';
        $reader->listWorksheetInfo($filename);
        self::assertEquals($delimiter, $reader->getDelimiter());
    }

    public static function providerEscapes(): array
    {
        return [
            ['\\', ';'],
            ["\x0", ','],
            ['', ','],
        ];
    }

    public function testSetDelimiterNull(): void
    {
        $reader = new Csv();
        $reader->setDelimiter(',');
        self::assertSame(',', $reader->getDelimiter());
        $reader->setDelimiter(null);
        self::assertNull($reader->getDelimiter());
    }
}
