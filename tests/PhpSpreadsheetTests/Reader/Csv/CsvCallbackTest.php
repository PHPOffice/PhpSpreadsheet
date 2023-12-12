<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PHPUnit\Framework\TestCase;

class CsvCallbackTest extends TestCase
{
    protected function tearDown(): void
    {
        Csv::setConstructorCallback(null);
    }

    public function callbackDoNothing(mixed $obj): void
    {
        self::assertInstanceOf(Csv::class, $obj);
    }

    public function testCallbackDoNothing(): void
    {
        Csv::setConstructorCallback([$this, 'callbackDoNothing']);
        $filename = 'tests/data/Reader/CSV/encoding.iso88591.csv';
        $reader = new Csv();
        $reader->setInputEncoding(Csv::GUESS_ENCODING);
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('Å', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function callbackSetFallbackEncoding(Csv $reader): void
    {
        $reader->setFallbackEncoding('ISO-8859-2');
        $reader->setInputEncoding(Csv::GUESS_ENCODING);
        $reader->setSheetNameIsFileName(true);
        $reader->setEscapeCharacter('');
    }

    public function testFallbackEncodingDefltIso2(): void
    {
        Csv::setConstructorCallback([$this, 'callbackSetFallbackEncoding']);
        $filename = 'tests/data/Reader/CSV/premiere.win1252.csv';
        $reader = new Csv();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('premičre', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixičme', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testIOFactory(): void
    {
        Csv::setConstructorCallback([$this, 'callbackSetFallbackEncoding']);
        $filename = 'tests/data/Reader/CSV/premiere.win1252.csv';
        $spreadsheet = IOFactory::load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('premičre', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixičme', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNonFallbackEncoding(): void
    {
        Csv::setConstructorCallback([$this, 'callbackSetFallbackEncoding']);
        $filename = 'tests/data/Reader/CSV/premiere.utf16be.csv';
        $reader = new Csv();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('première', $sheet->getCell('A1')->getValue());
        self::assertEquals('sixième', $sheet->getCell('C2')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testDefaultEscape(): void
    {
        self::assertNull(Csv::getConstructorCallback());
        $filename = 'tests/data/Reader/CSV/escape.csv';
        $spreadsheet = IOFactory::load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        // this is not how Excel views the file
        self::assertEquals('a\"hello', $sheet->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testBetterEscape(): void
    {
        Csv::setConstructorCallback([$this, 'callbackSetFallbackEncoding']);
        $filename = 'tests/data/Reader/CSV/escape.csv';
        $spreadsheet = IOFactory::load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        // this is how Excel views the file
        self::assertEquals('a\"hello;hello;hello;\"', $sheet->getCell('A1')->getValue());
        self::assertSame('escape', $sheet->getTitle(), 'callback set sheet title to use file name rather than default');
        $spreadsheet->disconnectWorksheets();
    }
}
