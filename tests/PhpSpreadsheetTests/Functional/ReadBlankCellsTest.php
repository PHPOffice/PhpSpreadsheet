<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReadBlankCellsTest extends AbstractFunctional
{
    public function providerSheetFormat()
    {
        return [
            ['Xlsx'],
            ['Xls'],
            // ['Ods'], // Broken. Requires fix in Ods reader.
            // ['Csv'], // never reads blank cells
            // ['Html'], // never reads blank cells
        ];
    }

    /**
     * Test generate file with some empty cells.
     *
     * @dataProvider providerSheetFormat
     *
     * @param array $arrayData
     * @param mixed $format
     */
    public function testXlsxLoadWithNoBlankCells($format)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->getCell('B2')->setValue('');
        $spreadsheet->getActiveSheet()->getCell('C1')->setValue('C1');
        $spreadsheet->getActiveSheet()->getCell('C3')->setValue('C3');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $this->assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('B2'));
        $this->assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C2'));
        $this->assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C3'));

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, function ($reader) {
            $reader->setReadEmptyCells(false);
        });
        $this->assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('B2'));
        $this->assertFalse($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C2'));
        $this->assertTrue($reloadedSpreadsheet->getActiveSheet()->getCellCollection()->has('C3'));
    }
}
