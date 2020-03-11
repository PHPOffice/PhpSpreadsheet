<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class VariableColumnsTest extends TestCase
{
    public function testVariableColumns()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'A1');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'B1');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'A2');
        $spreadsheet->getActiveSheet()->setCellValue('B2', 'B2');
        $spreadsheet->getActiveSheet()->setCellValue('C2', 'C2');
        $spreadsheet->getActiveSheet()->setCellValue('A3', 'A3');

        $filename = tempnam(File::sysGetTempDir(), 'phpspreadsheet-test');
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');
        $writer->setVariableColumns(true);
        $writer->save($filename);

        $contents = file_get_contents($filename);

        $rows = explode(PHP_EOL, $contents);

        $this->assertEquals('"A1","B1"', $rows[0]);
        $this->assertEquals('"A2","B2","C2"', $rows[1]);
        $this->assertEquals('"A3"', $rows[2]);
    }
}
