<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ReferenceHelper3Test extends TestCase
{
    public function testIssue3661(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');

        $spreadsheet->addNamedRange(new NamedRange('FIRST', $sheet, '=$A1'));
        $spreadsheet->addNamedRange(new NamedRange('SECOND', $sheet, '=$B1'));
        $spreadsheet->addNamedRange(new NamedRange('THIRD', $sheet, '=$C1'));

        $sheet->fromArray([
            [1, 2, 3, '=FIRST', '=SECOND', '=THIRD', '=10*$A1'],
            [4, 5, 6, '=FIRST', '=SECOND', '=THIRD'],
            [7, 8, 9, '=FIRST', '=SECOND', '=THIRD'],
        ]);

        $sheet->insertNewRowBefore(1, 4);
        $sheet->insertNewColumnBefore('A', 1);
        self::assertSame(1, $sheet->getCell('E5')->getCalculatedValue());
        self::assertSame(5, $sheet->getCell('F6')->getCalculatedValue());
        self::assertSame(9, $sheet->getCell('G7')->getCalculatedValue());
        self::assertSame('=10*$B5', $sheet->getCell('H5')->getValue());
        self::assertSame(10, $sheet->getCell('H5')->getCalculatedValue());
        $firstColumn = $spreadsheet->getNamedRange('FIRST');
        /** @var NamedRange $firstColumn */
        self::assertSame('=$B1', $firstColumn->getRange());
        $spreadsheet->disconnectWorksheets();
    }

    public function testCompletelyRelative(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data');

        $spreadsheet->addNamedRange(new NamedRange('CellAbove', $sheet, '=A1048576'));
        $spreadsheet->addNamedRange(new NamedRange('CellBelow', $sheet, '=A2'));
        $spreadsheet->addNamedRange(new NamedRange('CellToLeft', $sheet, '=XFD1'));
        $spreadsheet->addNamedRange(new NamedRange('CellToRight', $sheet, '=B1'));

        $sheet->fromArray([
            [null, 'Above', null, null, 'Above', null, null, 'Above', null, null, 'Above', null],
            ['Left', '=CellAbove', 'Right', 'Left', '=CellBelow', 'Right', 'Left', '=CellToLeft', 'Right', 'Left', '=CellToRight', 'Right'],
            [null, 'Below', null, null, 'Below', null, null, 'Below', null, null, 'Below', null],
        ], null, 'A1', true);
        self::assertSame('Above', $sheet->getCell('B2')->getCalculatedValue());
        self::assertSame('Below', $sheet->getCell('E2')->getCalculatedValue());
        self::assertSame('Left', $sheet->getCell('H2')->getCalculatedValue());
        self::assertSame('Right', $sheet->getCell('K2')->getCalculatedValue());

        Calculation::getInstance($spreadsheet)->flushInstance();
        self::assertNull($sheet->getCell('L7')->getCalculatedValue(), 'value in L7 after flush is null');
        // Reset it once more
        Calculation::getInstance($spreadsheet)->flushInstance();
        // shift 5 rows down and 1 column to the right
        $sheet->insertNewRowBefore(1, 5);
        $sheet->insertNewColumnBefore('A', 1);

        self::assertSame('Above', $sheet->getCell('C7')->getCalculatedValue()); // Above
        self::assertSame('Below', $sheet->getCell('F7')->getCalculatedValue());
        self::assertSame('Left', $sheet->getCell('I7')->getCalculatedValue());
        self::assertSame('Right', $sheet->getCell('L7')->getCalculatedValue());

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setCellValue('L6', 'NotThisCell');
        $sheet2->setCellValue('L7', '=CellAbove');
        self::assertSame('Above', $sheet2->getCell('L7')->getCalculatedValue(), 'relative value uses cell on worksheet where name is defined');
        $spreadsheet->disconnectWorksheets();
    }

    private static bool $sumFormulaWorking = false;

    public function testSumAboveCell(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->addNamedRange(new NamedRange('AboveCell', $sheet, 'A1048576'));
        $sheet->setCellValue('C2', 123);
        $sheet->setCellValue('C3', '=AboveCell');
        $sheet->fromArray([
            ['Column 1', 'Column 2'],
            [2, 1],
            [4, 3],
            [6, 5],
            [8, 7],
            [10, 9],
            [12, 11],
            [14, 13],
            [16, 15],
            ['=SUM(A2:AboveCell)', '=SUM(B2:AboveCell)'],
        ], null, 'A1', true);
        self::assertSame(123, $sheet->getCell('C3')->getCalculatedValue());
        if (self::$sumFormulaWorking) {
            self::assertSame(72, $sheet->getCell('A10')->getCalculatedValue());
        } else {
            $spreadsheet->disconnectWorksheets();
            self::markTestIncomplete('PhpSpreadsheet does not handle this correctly');
        }
        $spreadsheet->disconnectWorksheets();
    }
}
