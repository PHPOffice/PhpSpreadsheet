<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class NonUtf8Test extends TestCase
{
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
        self::assertSame($expected, $sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    // Same as above, but with extending Reader class.
    public function testUtf16LineBreak2(): void
    {
        $reader = new CsvIconv2();
        $reader->setInputEncoding('UTF-16BE');
        $spreadsheet = $reader->load('tests/data/Reader/CSV/utf16be.line_break_in_enclosure.csv');
        $sheet = $spreadsheet->getActiveSheet();
        $expected = <<<EOF
            This is a test
            with line breaks
            that breaks the
            delimiters
            EOF;
        self::assertSame($expected, $sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
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
        self::assertSame($expected, $sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    // Same as above, but with extending Reader class.
    public function testUtf32LineBreakEscape2(): void
    {
        $reader = new CsvIconv2();
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
        self::assertSame($expected, $sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
    }
}
