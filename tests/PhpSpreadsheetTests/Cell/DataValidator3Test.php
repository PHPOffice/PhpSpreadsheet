<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DataValidator3Test extends TestCase
{
    public function testArrayFunctionAsList(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(3);
        $sheet->getCell('A3')->setValue(5);
        $sheet->getCell('A4')->setValue(7);
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );

        $sheet->getCell('G1')->setValue('=UNIQUE(A1:A4)');
        $validation = $sheet->getCell('H4')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST)
            ->setFormula1('ANCHORARRAY(G1)');
        $sheet->getCell('H4')->setValue(2);
        self::assertFalse($sheet->getCell('H4')->hasValidValue());
        $sheet->getCell('H4')->setValue(3);
        self::assertTrue($sheet->getCell('H4')->hasValidValue());

        $spreadsheet->disconnectWorksheets();
    }
}
