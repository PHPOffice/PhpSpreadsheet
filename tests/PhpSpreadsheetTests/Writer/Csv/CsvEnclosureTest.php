<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheetTests\Functional;

class CsvEnclosureTest extends Functional\AbstractFunctional
{
    private static $cellValues = [
        'A1' => '2020-06-03',
        'B1' => '000123',
        'C1' => '06.53',
        'D1' => '14.22',
        'A2' => '2020-06-04',
        'B2' => '000234',
        'C2' => '07.12',
        'D2' => '15.44',
    ];

    public function testNormalEnclosure(): void
    {
        $delimiter = ';';
        $enclosure = '"';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (self::$cellValues as $key => $value) {
            $sheet->setCellValue($key, $value);
        }
        $writer = new CsvWriter($spreadsheet);
        $writer->setDelimiter($delimiter);
        $writer->setEnclosure($enclosure);
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        $writer->save($filename);
        $filedata = file_get_contents($filename);
        $filedata = preg_replace('/(\\r)?\\n/', $delimiter, $filedata);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        $expected = '';
        foreach (self::$cellValues as $key => $value) {
            self::assertEquals($value, $sheet->getCell($key)->getValue());
            $expected .= "$enclosure$value$enclosure$delimiter";
        }
        self::assertEquals($expected, $filedata);
    }

    public function testNoEnclosure(): void
    {
        $delimiter = ';';
        $enclosure = '';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (self::$cellValues as $key => $value) {
            $sheet->setCellValue($key, $value);
        }
        $writer = new CsvWriter($spreadsheet);
        $writer->setDelimiter($delimiter);
        $writer->setEnclosure($enclosure);
        self::assertEquals('', $writer->getEnclosure());
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        $writer->save($filename);
        $filedata = file_get_contents($filename);
        $filedata = preg_replace('/(\\r)?\\n/', $delimiter, $filedata);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        self::assertEquals('"', $reader->getEnclosure());
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        $expected = '';
        foreach (self::$cellValues as $key => $value) {
            self::assertEquals($value, $sheet->getCell($key)->getValue());
            $expected .= "$enclosure$value$enclosure$delimiter";
        }
        self::assertEquals($expected, $filedata);
    }
}
