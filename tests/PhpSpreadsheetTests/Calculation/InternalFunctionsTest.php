<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

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
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('SheetOne'); // no space in sheet title
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet Two'); // space in sheet title

        $sheet1->setCellValue('C3', '=SEQUENCE(3,3,-4)', true, 'C3:E5');
        $sheet2->setCellValue('C3', '=SEQUENCE(3,3, 9, -1)', true, 'C3:E5');

        $sheet1->setCellValue('A8', "=ANCHORARRAY({$reference})", true, $range);

        $result1 = $sheet1->getCell('A8')->getCalculatedValue(true, true);
        self::assertSame($expectedResult, $result1);
        $attributes1 = $sheet1->getCell('A8')->getFormulaAttributes();
        self::assertSame(['t' => 'array', 'ref' => $range], $attributes1);
    }

    public function anchorArrayDataProvider(): array
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
    public function testSingleArrayFormula(string $reference, array $expectedResult): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('SheetOne'); // no space in sheet title
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet Two'); // space in sheet title

        $sheet1->setCellValue('C3', '=SEQUENCE(3,3,-4)', true, 'C3:E5');
        $sheet2->setCellValue('C3', '=SEQUENCE(3,3, 9, -1)', true, 'C3:E5');

        $sheet1->setCellValue('A8', "=SINGLE({$reference})");

        $result1 = $sheet1->getCell('A8')->getCalculatedValue(true, true);
        self::assertSame($expectedResult, $result1);
    }

    public function singleDataProvider(): array
    {
        return [
            [
                'C3',
                [[-4]],
            ],
            [
                "'Sheet Two'!C3",
                [[9]],
            ],
        ];
    }
}
