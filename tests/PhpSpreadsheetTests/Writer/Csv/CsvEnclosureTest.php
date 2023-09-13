<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheetTests\Functional;

class CsvEnclosureTest extends Functional\AbstractFunctional
{
    private const CELL_VALUES = [
        'A1' => '2020-06-03',
        'B1' => '000123',
        'C1' => '06.53',
        'D1' => 14.22,
        'A2' => '2020-06-04',
        'B2' => '000234',
        'C2' => '07.12',
        'D2' => '15.44',
    ];

    private static function getFileData(string $filename): string
    {
        return file_get_contents($filename) ?: '';
    }

    public function testNormalEnclosure(): void
    {
        $delimiter = ';';
        $enclosure = '"';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (self::CELL_VALUES as $key => $value) {
            $sheet->setCellValue($key, $value);
        }
        $writer = new CsvWriter($spreadsheet);
        $writer->setDelimiter($delimiter);
        $writer->setEnclosure($enclosure);
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $filedata = self::getFileData($filename);
        $filedata = preg_replace('/\\r?\\n/', $delimiter, $filedata);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        $expected = '';
        foreach (self::CELL_VALUES as $key => $value) {
            self::assertEquals($value, $sheet->getCell($key)->getValue());
            $expected .= "$enclosure$value$enclosure$delimiter";
        }
        self::assertEquals($expected, $filedata);
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    public function testNoEnclosure(): void
    {
        $delimiter = ';';
        $enclosure = '';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (self::CELL_VALUES as $key => $value) {
            $sheet->setCellValue($key, $value);
        }
        $writer = new CsvWriter($spreadsheet);
        $writer->setDelimiter($delimiter);
        $writer->setEnclosure($enclosure);
        self::assertEquals('', $writer->getEnclosure());
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $filedata = self::getFileData($filename);
        $filedata = preg_replace('/\\r?\\n/', $delimiter, $filedata);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        self::assertEquals('"', $reader->getEnclosure());
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        $expected = '';
        foreach (self::CELL_VALUES as $key => $value) {
            self::assertEquals($value, $sheet->getCell($key)->getValue());
            $expected .= "$enclosure$value$enclosure$delimiter";
        }
        self::assertEquals($expected, $filedata);
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    public function testNotRequiredEnclosure1(): void
    {
        $delimiter = ';';
        $enclosure = '"';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach (self::CELL_VALUES as $key => $value) {
            $sheet->setCellValue($key, $value);
        }
        $writer = new CsvWriter($spreadsheet);
        self::assertTrue($writer->getEnclosureRequired());
        $writer->setEnclosureRequired(false)->setDelimiter($delimiter)->setEnclosure($enclosure);
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $filedata = self::getFileData($filename);
        $filedata = preg_replace('/\\r?\\n/', $delimiter, $filedata);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        $expected = '';
        foreach (self::CELL_VALUES as $key => $value) {
            self::assertEquals($value, $sheet->getCell($key)->getValue());
            $expected .= "$value$delimiter";
        }
        self::assertEquals($expected, $filedata);
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    public function testNotRequiredEnclosure2(): void
    {
        $cellValues2 = [
            'A1' => '2020-06-03',
            'B1' => 'has,separator',
            'C1' => 'has;non-separator',
            'D1' => 'has"enclosure',
            'A2' => 'has space',
            'B2' => "has\nnewline",
            'C2' => '',
            'D2' => 15.44,
            'A3' => ' leadingspace',
            'B3' => 'trailingspace ',
            'C3' => '=D2*2',
            'D3' => ',leadingcomma',
            'A4' => 'trailingquote"',
            'B4' => 'unused',
            'C4' => 'unused',
            'D4' => 'unused',
            'A5' => false,
            'B5' => true,
            'C5' => null,
            'D5' => 0,
        ];
        $calcc3 = '30.88';
        $expected1 = '2020-06-03,"has,separator",has;non-separator,"has""enclosure"';
        $expected2 = 'has space,"has' . "\n" . 'newline",,15.44';
        $expected3 = ' leadingspace,trailingspace ,' . $calcc3 . ',",leadingcomma"';
        $expected4 = '"trailingquote""",unused,unused,unused';
        $expected5 = 'FALSE,TRUE,,0';
        $expectedfile = "$expected1\n$expected2\n$expected3\n$expected4\n$expected5\n";
        $delimiter = ',';
        $enclosure = '"';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($cellValues2 as $key => $value) {
            $sheet->setCellValue($key, $value);
        }
        $writer = new CsvWriter($spreadsheet);
        self::assertTrue($writer->getEnclosureRequired());
        $writer->setEnclosureRequired(false)->setDelimiter($delimiter)->setEnclosure($enclosure);
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $filedata = self::getFileData($filename);
        $filedata = preg_replace('/\\r/', '', $filedata);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        foreach ($cellValues2 as $key => $value) {
            self::assertEquals(($key === 'C3') ? $calcc3 : $value, $sheet->getCell($key)->getValue(), "Failure for cell $key");
        }
        self::assertEquals($expectedfile, $filedata);
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    public function testRequiredEnclosure2(): void
    {
        $cellValues2 = [
            'A1' => '2020-06-03',
            'B1' => 'has,separator',
            'C1' => 'has;non-separator',
            'D1' => 'has"enclosure',
            'A2' => 'has space',
            'B2' => "has\nnewline",
            'C2' => '',
            'D2' => 15.44,
            'A3' => ' leadingspace',
            'B3' => 'trailingspace ',
            'C3' => '=D2*2',
            'D3' => ',leadingcomma',
            'A4' => 'trailingquote"',
            'B4' => 'unused',
            'C4' => 'unused',
            'D4' => 'unused',
            'A5' => false,
            'B5' => true,
            'C5' => null,
            'D5' => 0,
        ];
        $calcc3 = '30.88';
        $expected1 = '"2020-06-03","has,separator","has;non-separator","has""enclosure"';
        $expected2 = '"has space","has' . "\n" . 'newline","","15.44"';
        $expected3 = '" leadingspace","trailingspace ","' . $calcc3 . '",",leadingcomma"';
        $expected4 = '"trailingquote""","unused","unused","unused"';
        $expected5 = '"FALSE","TRUE","","0"';
        $expectedfile = "$expected1\n$expected2\n$expected3\n$expected4\n$expected5\n";
        $delimiter = ',';
        $enclosure = '"';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($cellValues2 as $key => $value) {
            $sheet->setCellValue($key, $value);
        }
        $writer = new CsvWriter($spreadsheet);
        self::assertTrue($writer->getEnclosureRequired());
        $writer->setEnclosureRequired(true)->setDelimiter($delimiter)->setEnclosure($enclosure);
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $filedata = self::getFileData($filename);
        $filedata = preg_replace('/\\r/', '', $filedata);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        foreach ($cellValues2 as $key => $value) {
            self::assertEquals(($key === 'C3') ? $calcc3 : $value, $sheet->getCell($key)->getValue(), "Failure for cell $key");
        }
        self::assertEquals($expectedfile, $filedata);
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    public function testGoodReread(): void
    {
        $delimiter = ',';
        $enclosure = '"';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '1');
        $sheet->setCellValue('B1', '2,3');
        $sheet->setCellValue('C1', '4');
        $writer = new CsvWriter($spreadsheet);
        $writer->setEnclosureRequired(false)->setDelimiter($delimiter)->setEnclosure($enclosure);
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        self::assertEquals('1', $sheet->getCell('A1')->getValue());
        self::assertEquals('2,3', $sheet->getCell('B1')->getValue());
        self::assertEquals('4', $sheet->getCell('C1')->getValue());
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }

    public function testBadReread(): void
    {
        $delimiter = ',';
        $enclosure = '';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '1');
        $sheet->setCellValue('B1', '2,3');
        $sheet->setCellValue('C1', '4');
        $writer = new CsvWriter($spreadsheet);
        $writer->setDelimiter($delimiter)->setEnclosure($enclosure);
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $reader = new CsvReader();
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $newspreadsheet = $reader->load($filename);
        unlink($filename);
        $sheet = $newspreadsheet->getActiveSheet();
        self::assertEquals('1', $sheet->getCell('A1')->getValue());
        self::assertEquals('2', $sheet->getCell('B1')->getValue());
        self::assertEquals('3', $sheet->getCell('C1')->getValue());
        self::assertEquals('4', $sheet->getCell('D1')->getValue());
        $spreadsheet->disconnectWorksheets();
        $newspreadsheet->disconnectWorksheets();
    }
}
