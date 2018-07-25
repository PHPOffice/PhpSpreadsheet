<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class XlsxTest extends TestCase
{
    /**
     * Test load Xlsx file without cell reference.
     */
    public function testLoadXlsxWithoutCellReference()
    {
        $filename = './data/Reader/XLSX/without_cell_reference.xlsx';
        $reader = new Xlsx();
        $reader->load($filename);
    }

    public function testReadColumnWidth()
    {

        $testFile = './data/Reader/XLSX/test.xlsx';
        @unlink($testFile);
        $this->assertFileNotExists($testFile);

        // create new sheet with column width
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $sheet->getColumnDimension('A')->setWidth(20);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($testFile);

        $columnDimensions = $sheet->getColumnDimensions();
        $this->assertArrayHasKey('A', $columnDimensions);
        $column = array_shift($columnDimensions);
        $this->assertEquals(20, $column->getWidth());

        // read the just created file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($testFile);
        $sheet = $spreadsheet->getActiveSheet();

        // check column width
        $columnDimensions = $sheet->getColumnDimensions();
        $this->assertArrayHasKey('A', $columnDimensions);
        $column = array_shift($columnDimensions);
        $this->assertEquals(20, $column->getWidth());
    }
}
