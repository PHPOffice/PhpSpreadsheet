<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class TypeAttributePreservaionTest extends AbstractFunctional
{
    public function providerFormulae()
    {
        return require 'data/Functional/TypeAttributePreservation/Formula.php';
    }

    /**
     * Ensure saved spreadsheets maintain the correct data type.
     *
     * @dataProvider providerFormulae
     *
     * @param  mixed $formula
     * @param mixed $extraCells
     */
    public function testFormulae($formula, $extraCells = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $formula);
        foreach ($extraCells as $coordinate => $value) {
            $sheet->setCellValue($coordinate, $value);
        }

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $reloadedSheet = $reloadedSpreadsheet->getActiveSheet();
        if ($sheet->getCell('A1')->getDataType() == 'f') {
            self::assertSame(
                $sheet->getCell('A1')->getCalculatedValue(),
                $reloadedSheet->getCell('A1')->getOldCalculatedValue()
            );
        } else {
            self::assertSame(
                $sheet->getCell('A1')->getValue(),
                $reloadedSheet->getCell('A1')->getValue()
            );
        }
    }
}
