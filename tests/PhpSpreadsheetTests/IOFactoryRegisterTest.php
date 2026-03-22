<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PHPUnit\Framework\TestCase;

class IOFactoryRegisterTest extends TestCase
{
    protected function tearDown(): void
    {
        IOFactory::restoreDefaultReadersAndWriters();
    }

    public function testRegisterWriter(): void
    {
        IOFactory::registerWriter('Pdf', Writer\Pdf\Mpdf::class);
        $spreadsheet = new Spreadsheet();
        $actual = IOFactory::createWriter($spreadsheet, 'Pdf');
        self::assertInstanceOf(Writer\Pdf\Mpdf::class, $actual);
    }

    public function testRegisterReader(): void
    {
        IOFactory::registerReader('Custom', Reader\Html::class);
        $actual = IOFactory::createReader('Custom');
        self::assertInstanceOf(Reader\Html::class, $actual);
    }

    public function testRegisterInvalidWriter(): void
    {
        $this->expectException(Writer\Exception::class);
        $this->expectExceptionMessage('writers must implement');
        IOFactory::registerWriter('foo', 'bar'); // @phpstan-ignore-line
    }

    public function testRegisterInvalidReader(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('readers must implement');
        IOFactory::registerReader('foo', 'bar'); // @phpstan-ignore-line
    }

    public static function testRegisterCustomReader(): void
    {
        IOFactory::registerReader(IOFactory::READER_XLSX, CustomReader::class);
        $inputFileName = 'tests/data/Reader/XLSX/1900_Calendar.xlsx';
        $loadSpreadsheet = IOFactory::load($inputFileName);
        $sheet = $loadSpreadsheet->getActiveSheet();
        self::assertSame('2022-01-01', $sheet->getCell('A1')->getFormattedValue());
        $loadSpreadsheet->disconnectWorksheets();

        $reader = new CustomReader();
        $newSpreadsheet = $reader->load($inputFileName);
        $newSheet = $newSpreadsheet->getActiveSheet();
        self::assertSame('2022-01-01', $newSheet->getCell('A1')->getFormattedValue());
        $newSpreadsheet->disconnectWorksheets();

        $inputFileType = IOFactory::identify($inputFileName, null, true);
        $objReader = IOFactory::createReader($inputFileType);
        self::assertInstanceOf(CustomReader::class, $objReader);
        $objSpreadsheet = $objReader->load($inputFileName);
        $objSheet = $objSpreadsheet->getActiveSheet();
        self::assertSame('2022-01-01', $objSheet->getCell('A1')->getFormattedValue());
        $objSpreadsheet->disconnectWorksheets();
    }

    public static function testRegisterCustomWriter(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 1);
        $writer = new CustomWriter($spreadsheet);
        $html = $writer->generateHtmlAll();
        self::assertStringContainsString('<td class="column0 style0 n">1</td>', $html);
        IOFactory::registerWriter(IOFactory::WRITER_HTML, CustomWriter::class);
        $objWriter = IOFactory::createWriter($spreadsheet, CustomWriter::class);
        self::assertInstanceOf(CustomWriter::class, $objWriter);
        $html2 = $objWriter->generateHtmlAll();
        self::assertStringContainsString('<td class="column0 style0 n">1</td>', $html2);
        $spreadsheet->disconnectWorksheets();
    }

    public static function testRegisterCsvNoEscape(): void
    {
        IOFactory::registerReader(IOFactory::READER_CSV, Reader\CsvNoEscape::class);
        $inputFileName = 'tests/data/Reader/CSV/backslash.csv';
        $spreadsheet = IOFactory::load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            ['field 1', 'field 2\\'],
            ['field 3\\', 'field 4'],
        ];
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();

        $inputFileType = IOFactory::identify($inputFileName, null, true);
        $objReader = IOFactory::createReader($inputFileType);
        self::assertInstanceOf(Reader\CsvNoEscape::class, $objReader);
        $objSpreadsheet = $objReader->load($inputFileName);
        $objSheet = $objSpreadsheet->getActiveSheet();
        self::assertSame($expected, $objSheet->toArray());
        $objSpreadsheet->disconnectWorksheets();
    }

    public static function testAlternateCsvNoEscape1(): void
    {
        $inputFileName = 'tests/data/Reader/CSV/backslash.csv';

        $inputFileType = IOFactory::identify($inputFileName, null, true);
        $objReader = IOFactory::createReader($inputFileType);
        self::assertInstanceOf(Reader\Csv::class, $objReader);

        $spreadsheet = IOFactory::load($inputFileName, mergeArray: IOFactory::USE_CSV_NO_ESCAPE);
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            ['field 1', 'field 2\\'],
            ['field 3\\', 'field 4'],
        ];
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }

    public static function testAlternateCsvNoEscape2(): void
    {
        $inputFileName = 'tests/data/Reader/CSV/backslash.csv';

        $inputFileType = IOFactory::identify($inputFileName, null, true);
        $objReader = IOFactory::createReader($inputFileType);
        self::assertInstanceOf(Reader\Csv::class, $objReader);

        $inputFileType2 = IOFactory::identify($inputFileName, null, true, mergeArray: IOFactory::USE_CSV_NO_ESCAPE);
        self::assertSame(Reader\CsvNoEscape::class, $inputFileType2);
        $objReader2 = IOFactory::createReaderForFile($inputFileName, mergeArray: IOFactory::USE_CSV_NO_ESCAPE);
        self::assertInstanceOf(Reader\CsvNoEscape::class, $objReader2);

        $spreadsheet = $objReader2->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            ['field 1', 'field 2\\'],
            ['field 3\\', 'field 4'],
        ];
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
    }
}
