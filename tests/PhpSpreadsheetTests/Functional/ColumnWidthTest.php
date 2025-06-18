<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ColumnWidthTest extends AbstractFunctional
{
    public function providerFormats()
    {
        return [
            ['Xlsx'],
        ];
    }

    /**
     * @dataProvider providerFormats
     *
     * @param $format
     */
    public function testReadColumnWidth($format)
    {
        // create new sheet with column width
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $sheet->getColumnDimension('A')->setWidth(20);
        $this->assertColumn($spreadsheet);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $this->assertColumn($reloadedSpreadsheet);
    }

    private function assertColumn(Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $columnDimensions = $sheet->getColumnDimensions();

        self::assertArrayHasKey('A', $columnDimensions);
        $column = array_shift($columnDimensions);
        self::assertEquals(20, $column->getWidth());
    }
}
