<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheetTests\Functional;

class CsvOutputEncodingTest extends Functional\AbstractFunctional
{
    public function testEncoding(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'こんにちは！');
        $sheet->setCellValue('B1', 'Hello!');

        $writer = new CsvWriter($spreadsheet);

        $filename = File::temporaryFilename();
        $writer->setUseBOM(false);
        $writer->setOutputEncoding('SJIS-win');
        $writer->save($filename);
        $contents = file_get_contents($filename);
        unlink($filename);

        // self::assertStringContainsString(mb_convert_encoding('こんにちは！', 'SJIS-win'), $contents);
        self::assertStringContainsString("\x82\xb1\x82\xf1\x82\xc9\x82\xbf\x82\xcd\x81\x49", $contents);
    }
}
