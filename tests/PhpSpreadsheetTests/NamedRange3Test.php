<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class NamedRange3Test extends TestCase
{
    public function testSheetNamePlusDefinedName(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('sheet1');
        $sheet1->setCellValue('B1', 100);
        $sheet1->setCellValue('B2', 200);
        $sheet1->setCellValue('B3', 300);
        $sheet1->setCellValue('B4', 400);
        $sheet1->setCellValue('B5', 500);

        $sheet2 = $spreadsheet->createsheet();
        $sheet2->setTitle('sheet2');
        $sheet2->setCellValue('A1', 10);
        $sheet2->setCellValue('A2', 20);
        $sheet2->setCellValue('A3', 30);
        $sheet2->setCellValue('A4', 40);
        $sheet2->setCellValue('A5', 50);

        $spreadsheet->addNamedRange(
            new NamedRange('somecells', $sheet2, '$A$1:$A$5', true)
        );
        $spreadsheet->addNamedRange(
            new NamedRange('cellsonsheet1', $sheet1, '$B$1:$B$5')
        );

        $sheet1->getCell('G1')->setValue('=SUM(cellsonsheet1)');
        self::assertSame(1500, $sheet1->getCell('G1')->getCalculatedValue());
        $sheet1->getCell('G2')->setValue('=SUM(sheet2!somecells)');
        self::assertSame(150, $sheet1->getCell('G2')->getCalculatedValue());
        $sheet1->getCell('G3')->setValue('=SUM(somecells)');
        self::assertSame('#NAME?', $sheet1->getCell('G3')->getCalculatedValue());
        $sheet1->getCell('G4')->setValue('=SUM(sheet2!cellsonsheet1)');
        self::assertSame(1500, $sheet1->getCell('G4')->getCalculatedValue());
        $sheet1->getCell('G5')->setValue('=SUM(sheet2xxx!cellsonsheet1)');
        self::assertSame('#NAME?', $sheet1->getCell('G5')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }
}
