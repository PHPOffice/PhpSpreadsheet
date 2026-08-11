<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Worksheet\Table;

class Issue3659Test extends SetupTeardown
{
    public function testTableOnOtherSheet(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $this->getSheet();
        $sheet->setTitle('Feuil1');
        $tableSheet = $spreadsheet->createSheet();
        $tableSheet->setTitle('sheet_with_table');
        $tableSheet->fromArray(
            [
                ['MyCol', 'Colonne2', 'Colonne3'],
                [10, 20],
                [2],
                [3],
                [4],
            ],
            null,
            'B1',
            true
        );
        $table = new Table('B1:D5', 'Tableau1');
        $tableSheet->addTable($table);
        $sheet->setSelectedCells('F7');
        $tableSheet->setSelectedCells('F8');
        self::assertSame($sheet, $spreadsheet->getActiveSheet());
        $sheet->getCell('A1')->setValue('=SUM(Tableau1[MyCol])');
        $sheet->getCell('A2')->setValue('=SUM(Tableau1[])');
        $sheet->getCell('A3')->setValue('=SUM(Tableau1)');
        $sheet->getCell('A4')->setValue('=CONCAT(Tableau1)');
        self::assertSame(19, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame(39, $sheet->getCell('A2')->getCalculatedValue());
        self::assertSame(39, $sheet->getCell('A3')->getCalculatedValue());
        self::assertSame('1020234', $sheet->getCell('A4')->getCalculatedValue(), 'Header row not included');
        self::assertSame('F7', $sheet->getSelectedCells());
        self::assertSame('F8', $tableSheet->getSelectedCells());
        self::assertSame($sheet, $spreadsheet->getActiveSheet());
    }

    public function testTableAsArray(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $spreadsheet->returnArrayAsArray();
        $sheet = $this->getSheet();
        $sheet->setTitle('Feuil1');
        $tableSheet = $spreadsheet->createSheet();
        $tableSheet->setTitle('sheet_with_table');
        $tableSheet->fromArray(
            [
                ['MyCol', 'Colonne2', 'Colonne3'],
                [10, 20],
                [2],
                [3],
                [4],
            ],
            null,
            'B1',
            true
        );
        $table = new Table('B1:D5', 'Tableau1');
        $tableSheet->addTable($table);
        $sheet->setSelectedCells('F7');
        $tableSheet->setSelectedCells('F8');
        self::assertSame($sheet, $spreadsheet->getActiveSheet());
        $sheet->getCell('F1')->setValue('=Tableau1[MyCol]');
        $sheet->getCell('H1')->setValue('=Tableau1[]');
        $sheet->getCell('F9')->setValue('=Tableau1');
        $sheet->getCell('J9')->setValue('=CONCAT(Tableau1)');
        $sheet->getCell('J11')->setValue('=SUM(Tableau1[])');
        $expectedResult = [[10], [2], [3], [4]];
        self::assertSame($expectedResult, $sheet->getCell('F1')->getCalculatedValue());
        $expectedResult = [
            [10, 20, null],
            [2, null, null],
            [3, null, null],
            [4, null, null],
        ];
        self::assertSame($expectedResult, $sheet->getCell('H1')->getCalculatedValue());
        self::assertSame($expectedResult, $sheet->getCell('F9')->getCalculatedValue());
        self::assertSame('1020234', $sheet->getCell('J9')->getCalculatedValue(), 'Header row not included');
        self::assertSame(39, $sheet->getCell('J11')->getCalculatedValue(), 'Header row not included');
        self::assertSame('F7', $sheet->getSelectedCells());
        self::assertSame('F8', $tableSheet->getSelectedCells());
        self::assertSame($sheet, $spreadsheet->getActiveSheet());
    }
}
