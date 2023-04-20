<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvEncodingTest extends TestCase
{
    /**
     * @dataProvider providerEncodings
     *
     * @param string $filename
     * @param string $encoding
     */
    public function testEncodings($filename, $encoding): void
    {
        $reader = new Csv();
        $reader->setInputEncoding($encoding);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('Å', $sheet->getCell('A1')->getValue());
    }

    /**
     * @dataProvider providerEncodings
     *
     * @param string $filename
     * @param string $encoding
     */
    public function testWorkSheetInfo($filename, $encoding): void
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

    public static function providerEncodings(): array
    {
        return [
            ['tests/data/Reader/CSV/encoding.iso88591.csv', 'ISO-8859-1'],
            ['tests/data/Reader/CSV/encoding.utf8.csv', 'UTF-8'],
            ['tests/data/Reader/CSV/encoding.utf8bom.csv', 'UTF-8'],
            ['tests/data/Reader/CSV/encoding.utf16be.csv', 'UTF-16BE'],
            ['tests/data/Reader/CSV/encoding.utf16le.csv', 'UTF-16LE'],
            ['tests/data/Reader/CSV/encoding.utf32be.csv', 'UTF-32BE'],
            ['tests/data/Reader/CSV/encoding.utf32le.csv', 'UTF-32LE'],
        ];
    }

    /**
     * @dataProvider providerGuessEncoding
     */
    public function testGuessEncoding(string $filename): void
    {
        $reader = new Csv();
        $reader->setInputEncoding(Csv::guessEncoding($filename));
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('première', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixième', $sheet->getCell('C2')->getValue());
    }

    public function testSurrogate(): void
    {
        // Surrogates should occur only in UTF-16, and should
        //   be properly converted to UTF8 when read.
        // FFFE/FFFF are illegal, and should be converted to
        //   substitution character when read.
        // Excel does not handle any of the cells in row 3 well.
        // LibreOffice handles A3 fine, and discards B3/C3,
        //   which is a reasonable action.
        $filename = 'tests/data/Reader/CSV/premiere.utf16le.csv';
        $reader = new Csv();
        $reader->setInputEncoding(Csv::guessEncoding($filename));
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('𐐀', $sheet->getCell('A3')->getValue());
        self::assertEquals('�', $sheet->getCell('B3')->getValue());
        self::assertEquals('�', $sheet->getCell('C3')->getValue());
    }

    /**
     * @dataProvider providerGuessEncoding
     */
    public function testFallbackEncoding(string $filename): void
    {
        $reader = new Csv();
        $reader->setInputEncoding(Csv::GUESS_ENCODING);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('première', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixième', $sheet->getCell('C2')->getValue());
    }

    public static function providerGuessEncoding(): array
    {
        return [
            ['tests/data/Reader/CSV/premiere.utf8.csv'],
            ['tests/data/Reader/CSV/premiere.utf8bom.csv'],
            ['tests/data/Reader/CSV/premiere.utf16be.csv'],
            ['tests/data/Reader/CSV/premiere.utf16bebom.csv'],
            ['tests/data/Reader/CSV/premiere.utf16le.csv'],
            ['tests/data/Reader/CSV/premiere.utf16lebom.csv'],
            ['tests/data/Reader/CSV/premiere.utf32be.csv'],
            ['tests/data/Reader/CSV/premiere.utf32bebom.csv'],
            ['tests/data/Reader/CSV/premiere.utf32le.csv'],
            ['tests/data/Reader/CSV/premiere.utf32lebom.csv'],
            ['tests/data/Reader/CSV/premiere.win1252.csv'],
        ];
    }

    public function testGuessEncodingDefltIso2(): void
    {
        $filename = 'tests/data/Reader/CSV/premiere.win1252.csv';
        $reader = new Csv();
        $reader->setInputEncoding(Csv::guessEncoding($filename, 'ISO-8859-2'));
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('premičre', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixičme', $sheet->getCell('C2')->getValue());
    }

    public function testFallbackEncodingDefltIso2(): void
    {
        $filename = 'tests/data/Reader/CSV/premiere.win1252.csv';
        $reader = new Csv();
        self::assertSame('CP1252', $reader->getFallbackEncoding());
        $reader->setInputEncoding(Csv::GUESS_ENCODING);
        $reader->setFallbackEncoding('ISO-8859-2');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('premičre', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixičme', $sheet->getCell('C2')->getValue());
    }
}
