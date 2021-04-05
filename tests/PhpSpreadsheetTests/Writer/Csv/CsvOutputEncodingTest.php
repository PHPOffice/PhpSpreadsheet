<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheetTests\Functional;

class CsvOutputEncodingTest extends Functional\AbstractFunctional
{
    public function testEncoding(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'こんにちは！');
        $sheet->setCellValue('B1', 'Hello!');

        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test-UTF-8');
        $writer->useBOM(false);
        $writer->setOutputEncoding('');
        $writer->save($filename);
        $a = file_get_contents($filename);
        unlink($filename);
        
        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test-SJIS-win');
        $writer->useBOM(false);
        $writer->setOutputEncoding('SJIS-win');
        $writer->save($filename);
        $b = file_get_contents($filename);
        unlink($filename);
        
        self::assertEquals(mb_convert_encoding($a, 'SJIS-win'), $b);
    }
}
