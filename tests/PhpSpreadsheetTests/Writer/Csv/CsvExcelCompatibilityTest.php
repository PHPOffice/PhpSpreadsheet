<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheetTests\Functional;

class CsvExcelCompatibilityTest extends Functional\AbstractFunctional
{
    // Excel seems to have changed with how they handle this.
    // In particular, it does not recognize UTF-8 non-ASCII characters
    //    if a file is written with ExcelCompatibility on.
    //    The initial 'sep=;' line seems to confuse it, even though
    //    it has a BOM. The Unix "file" command also indicates a difference
    //    when the sep line is or is not included:
    //        UTF-8 Unicode (with BOM) text, with CRLF line terminators
    //     vs CSV text (without sep line, with or without BOM)
    // So, this test has no UTF-8 yet while more research is conducted.
    public function testExcelCompatibility(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '1');
        $sheet->setCellValue('B1', '2');
        $sheet->setCellValue('C1', '3');
        $sheet->setCellValue('A2', '4');
        $sheet->setCellValue('B2', '5');
        $sheet->setCellValue('C2', '6');
        $writer = new CsvWriter($spreadsheet);
        $writer->setExcelCompatibility(true);
        self::assertSame('', $writer->getOutputEncoding());
        $filename = File::temporaryFilename();
        $writer->save($filename);
        $reader = new CsvReader();
        $spreadsheet2 = $reader->load($filename);
        $contents = file_get_contents($filename);
        unlink($filename);
        self::assertEquals(1, $spreadsheet2->getActiveSheet()->getCell('A1')->getValue());
        self::assertEquals(6, $spreadsheet2->getActiveSheet()->getCell('C2')->getValue());
        self::assertStringContainsString(CsvReader::UTF8_BOM, $contents);
        self::assertStringContainsString("\r\n", $contents);
        self::assertStringContainsString('sep=;', $contents);
        self::assertStringContainsString('"1";"2";"3"', $contents);
        self::assertStringContainsString('"4";"5";"6"', $contents);
    }
}
