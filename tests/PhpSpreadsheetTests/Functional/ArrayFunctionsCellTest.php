<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ArrayFunctionsCellTest extends TestCase
{
    public function testArrayAndNonArrayOutput(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                [1.0, 0.0, 1.0],
                [0.0, 2.0, 0.0],
                [0.0, 0.0, 1.0],
            ],
            strictNullComparison: true
        );
        $sheet->setCellValue('E1', '=MINVERSE(A1:C3)');
        $sheet->setCellValue('I1', '=E1#');
        $sheet->setCellValue('M1', '=MMULT(E1#,I1#)');
        $sheet->setCellValue('E6', '=SUM(E1)');
        $sheet->setCellValue('E7', '=SUM(SINGLE(E1))');
        $sheet->setCellValue('E8', '=SUM(E1#)');
        $sheet->setCellValue('I6', '=E1+I1');
        $sheet->setCellValue('J6', '=E1#+I1#');

        $expectedE1 = [
            [1.0, 0.0, -1.0],
            [0.0, 0.5, 0.0],
            [0.0, 0.0, 1.0],
        ];
        self::assertSame($expectedE1, $sheet->getCell('E1')->getCalculatedValue(), 'MINVERSE function');
        self::assertSame($expectedE1, $sheet->getCell('I1')->getCalculatedValue(), 'Assignment with spill operator');

        $expectedM1 = [
            [1.0, 0.0, -2.0],
            [0.0, 0.25, 0.0],
            [0.0, 0.0, 1.0],
        ];
        self::assertSame($expectedM1, $sheet->getCell('M1')->getCalculatedValue(), 'MMULT with 2 spill operators');

        self::assertSame(1.0, $sheet->getCell('E6')->getCalculatedValue(), 'SUM referring to anchor cell');
        self::assertSame(1.0, $sheet->getCell('E7')->getCalculatedValue(), 'SUM referring to anchor cell wrapped in Single');
        self::assertSame(1.5, $sheet->getCell('E8')->getCalculatedValue(), 'SUM referring to anchor cell with Spill Operator');

        self::assertSame(2.0, $sheet->getCell('I6')->getCalculatedValue(), 'addition operator for 2 anchor cells');
        $expectedJ6 = [
            [2.0, 0.0, -2.0],
            [0.0, 1.0, 0.0],
            [0.0, 0.0, 2.0],
        ];
        self::assertSame($expectedJ6, $sheet->getCell('J6')->getCalculatedValue(), 'addition operator for 2 anchor cells with Spill operators');

        $spreadsheet->disconnectWorksheets();
    }
}
