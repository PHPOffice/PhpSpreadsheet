<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheetTests\Functional;

class CsvWriteTest extends Functional\AbstractFunctional
{
    public function testNotFirstSheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'First Sheet');
        $sheet = $spreadsheet->createSheet();
        $sheet->setCellValue('A1', 'Second Sheet');
        $sheet = $spreadsheet->createSheet();
        $sheet->setCellValue('A1', 'Third Sheet');
        $writer = new CsvWriter($spreadsheet);
        $writer->setSheetIndex(1);
        self::assertEquals(1, $writer->getSheetIndex());
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $reader = new CsvReader();
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        self::assertEquals('Second Sheet', $sheet->getCell('A1')->getValue());
        self::assertEquals(0, $newspreadsheet->getActiveSheetIndex());
    }

    public function testWriteEmptyFileName(): void
    {
        $this->expectException(WriterException::class);
        $spreadsheet = new Spreadsheet();
        $writer = new CsvWriter($spreadsheet);
        $filename = '';
        $writer->save($filename);
    }

    public function testDefaultSettings(): void
    {
        $spreadsheet = new Spreadsheet();
        $writer = new CsvWriter($spreadsheet);
        self::assertEquals('"', $writer->getEnclosure());
        $writer->setEnclosure('\'');
        self::assertEquals('\'', $writer->getEnclosure());
        $writer->setEnclosure('');
        self::assertEquals('', $writer->getEnclosure());
        $writer->setEnclosure();
        self::assertEquals('"', $writer->getEnclosure());
        self::assertEquals(PHP_EOL, $writer->getLineEnding());
        self::assertFalse($writer->getUseBOM());
        self::assertFalse($writer->getIncludeSeparatorLine());
        self::assertFalse($writer->getExcelCompatibility());
        self::assertEquals(0, $writer->getSheetIndex());
    }
}
