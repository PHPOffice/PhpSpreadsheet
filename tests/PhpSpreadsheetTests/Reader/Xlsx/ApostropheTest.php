<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ApostropheTest extends AbstractFunctional
{
    public function testApostropheInSheetName(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('sheet1');
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(2);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getCell('A4')->setValue(4);
        $spreadsheet->addNamedRange(new NamedRange('sheet14cells', $sheet, '$A$1:$A$4'));
        $sheet->getCell('C1')
            ->setValue('=sheet14cells*sheet14cells');
        $sheet->getCell('E1')->setValue('=ANCHORARRAY(sheet1!C1)');
        $sheet->getCell('G1')->setValue('=SINGLE(sheet1!C1:C4)');

        $sheet1 = $spreadsheet->createSheet();
        $sheet1->setTitle("Apo'strophe");
        $sheet1->getCell('A1')->setValue(2);
        $sheet1->getCell('A2')->setValue(3);
        $sheet1->getCell('A3')->setValue(4);
        $sheet1->getCell('A4')->setValue(5);
        $spreadsheet->addNamedRange(new NamedRange('sheet24cells', $sheet1, '$A$1:$A$4'));
        $sheet1->getCell('C1')
            ->setValue('=sheet24cells*sheet24cells');
        $sheet1->getCell('E1')->setValue("=ANCHORARRAY('Apo''strophe'!C1)");
        $sheet1->getCell('G1')->setValue("=SINGLE('Apo''strophe'!C1:C4)");

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        Calculation::getInstance($reloadedSpreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $rsheet = $reloadedSpreadsheet->getSheet(0);
        // make sure results aren't from cache
        $rsheet->getCell('E1')->setCalculatedValue(-1);
        $rsheet->getCell('G1')->setCalculatedValue(-1);
        self::assertSame([[1], [4], [9], [16]], $rsheet->getCell('C1')->getCalculatedValue());
        self::assertSame([[1], [4], [9], [16]], $rsheet->getCell('E1')->getCalculatedValue());
        self::assertSame(1, $rsheet->getCell('G1')->getCalculatedValue());

        $rsheet1 = $reloadedSpreadsheet->getSheet(1);
        // make sure results aren't from cache
        $rsheet1->getCell('E1')->setCalculatedValue(-1);
        $rsheet1->getCell('G1')->setCalculatedValue(-1);
        self::assertSame([[4], [9], [16], [25]], $rsheet1->getCell('C1')->getCalculatedValue());
        self::assertSame([[4], [9], [16], [25]], $rsheet1->getCell('E1')->getCalculatedValue());
        self::assertSame(4, $rsheet1->getCell('G1')->getCalculatedValue());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
