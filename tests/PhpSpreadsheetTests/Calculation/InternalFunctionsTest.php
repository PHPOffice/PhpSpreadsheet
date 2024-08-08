<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class InternalFunctionsTest extends TestCase
{
    /**
     * @dataProvider anchorArrayDataProvider
     */
    public function testAnchorArrayFormula(string $reference, string $range, array $expectedResult): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('SheetOne'); // no space in sheet title
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet Two'); // space in sheet title

        $sheet1->setCellValue('C3', '=SEQUENCE(3,3,-4)');
        $sheet2->setCellValue('C3', '=SEQUENCE(3,3, 9, -1)');
        $sheet1->calculateArrays();
        $sheet2->calculateArrays();
        $sheet1->setCellValue('A8', "=ANCHORARRAY({$reference})");

        $result1 = $sheet1->getCell('A8')->getCalculatedValue();
        self::assertSame($expectedResult, $result1);
        $attributes1 = $sheet1->getCell('A8')->getFormulaAttributes();
        self::assertSame(['t' => 'array', 'ref' => $range], $attributes1);
        $spreadsheet->disconnectWorksheets();
    }

    public static function anchorArrayDataProvider(): array
    {
        return [
            [
                'C3',
                'A8:C10',
                [[-4, -3, -2], [-1, 0, 1], [2, 3, 4]],
            ],
            [
                "'Sheet Two'!C3",
                'A8:C10',
                [[9, 8, 7], [6, 5, 4], [3, 2, 1]],
            ],
        ];
    }

    /**
     * @dataProvider singleDataProvider
     */
    public function testSingleArrayFormula(string $reference, mixed $expectedResult): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('SheetOne'); // no space in sheet title
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet Two'); // space in sheet title

        $sheet1->setCellValue('C3', '=SEQUENCE(3,3,-4)');
        $sheet2->setCellValue('C3', '=SEQUENCE(3,3, 9, -1)');

        $sheet1->setCellValue('A8', "=SINGLE({$reference})");
        $sheet1->setCellValue('G3', 'three');
        $sheet1->setCellValue('G4', 'four');
        $sheet1->setCellValue('G5', 'five');
        $sheet1->setCellValue('G7', 'seven');
        $sheet1->setCellValue('G8', 'eight');
        $sheet1->setCellValue('G9', 'nine');

        $sheet1->calculateArrays();
        $sheet2->calculateArrays();

        $result1 = $sheet1->getCell('A8')->getCalculatedValue();
        self::assertSame($expectedResult, $result1);
        $spreadsheet->disconnectWorksheets();
    }

    public static function singleDataProvider(): array
    {
        return [
            'array cell on same sheet' => [
                'C3',
                -4,
            ],
            'array cell on different sheet' => [
                "'Sheet Two'!C3",
                9,
            ],
            'range which includes current row' => [
                'G7:G9',
                'eight',
            ],
            'range which does not include current row' => [
                'G3:G5',
                '#VALUE!',
            ],
            'range which includes current row but spans columns' => [
                'F7:G9',
                '#VALUE!',
            ],
        ];
    }
}
