<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ArrayFormulaTest extends TestCase
{
    /**
     * @dataProvider providerArrayFormulae
     */
    public function testArrayFormula(string $formula, mixed $expectedResult): void
    {
        $result = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerArrayFormulae(): array
    {
        return [
            [
                '=MAX(ABS({-3, 4, -2; 6, -3, -12}))',
                12,
            ],
            'unary operator applied to function' => [
                '=MAX(-ABS({-3, 4, -2; 6, -3, -12}))',
                -2,
            ],
            [
                '=SUM(SEQUENCE(3,3,0,1))',
                36,
            ],
            [
                '=IFERROR({5/2, 5/0}, MAX(ABS({-2,4,-6})))',
                [[2.5, 6]],
            ],
            [
                '=MAX(IFERROR({5/2, 5/0}, 2.1))',
                2.5,
            ],
            [
                '=IF(FALSE,{1,2,3},{4,5,6})',
                [[4, 5, 6]],
            ],
            [
                '=IFS(FALSE, {1,2,3}, TRUE, {4,5,6})',
                [[4, 5, 6]],
            ],
            'some invalid values' => [
                '=ABS({1,-2,"X3"; "B4",5,6})',
                [[1, 2, '#VALUE!'], ['#VALUE!', 5, 6]],
            ],
            'some invalid values with unary minus' => [
                '=-({1,-2,"X3"; "B4",5,6})',
                [[-1, 2, '#VALUE!'], ['#VALUE!', -5, -6]],
            ],
        ];
    }

    public function testArrayFormulaUsingCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A4')->setValue(-3);
        $sheet->getCell('B4')->setValue(4);
        $sheet->getCell('C4')->setValue(-2);
        $sheet->getCell('A5')->setValue(6);
        $sheet->getCell('B5')->setValue(-3);
        $sheet->getCell('C5')->setValue(-12);
        $sheet->getCell('E4')->setValue('=MAX(-ABS(A4:C5))');
        self::assertSame(-2, $sheet->getCell('E4')->getCalculatedValue());
        $sheet->getCell('C4')->setValue('XYZ');
        $sheet->getCell('F4')->setValue('=MAX(-ABS(A4:C5))');
        self::assertSame('#VALUE!', $sheet->getCell('F4')->getCalculatedValue());
        $sheet->getCell('G4')->setValue('=-C4:E4');
        self::assertSame('#VALUE!', $sheet->getCell('G4')->getCalculatedValue());
        $sheet->getCell('H4')->setValue('=-A4:B4');
        self::assertSame(3, $sheet->getCell('H4')->getCalculatedValue());
        $sheet->getCell('I4')->setValue('=25%');
        self::assertEqualsWithDelta(0.25, $sheet->getCell('I4')->getCalculatedValue(), 1.0E-8);
        $spreadsheet->disconnectWorksheets();
    }
}
