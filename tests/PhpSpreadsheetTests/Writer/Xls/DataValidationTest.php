<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DataValidationTest extends AbstractFunctional
{
    public function testWholeRow(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_NONE);
        $sheet->setDataValidation('C1:F1', $dv);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST)
            ->setShowDropDown(true)
            ->setFormula1('"Item A,Item B,Item C"')
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Input Error')
            ->setError('Value is not a member of allowed list');
        $sheet->setDataValidation('1:1', $dv);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_NONE);
        $sheet->setDataValidation('H1', $dv);

        $robj = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        self::assertSame(['H1', 'C1:F1', '1:1'], array_keys($sheet0->getDataValidationCollection()));
        $robj->disconnectWorksheets();
    }

    public function testWholeColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('C1')->setValue(1);
        $sheet->getCell('C2')->setValue(2);
        $sheet->getCell('C3')->setValue(3);
        $dv = $sheet->getDataValidation('A5:A7');
        $dv->setType(DataValidation::TYPE_NONE);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST)
            ->setShowDropDown(true)
            ->setFormula1('$C$1:$C$3')
            ->setErrorStyle(DataValidation::STYLE_STOP)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Input Error')
            ->setError('Value is not a member of allowed list');
        $sheet->setDataValidation('A:A', $dv);
        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_NONE);
        $sheet->setDataValidation('A9', $dv);

        $robj = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet0 = $robj->getActiveSheet();
        self::assertSame(['A9', 'A5:A7', 'A:A'], array_keys($sheet0->getDataValidationCollection()));
        $robj->disconnectWorksheets();
    }
}
