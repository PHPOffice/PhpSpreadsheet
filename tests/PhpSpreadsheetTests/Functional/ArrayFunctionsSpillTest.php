<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class ArrayFunctionsSpillTest extends TestCase
{
    public function testArrayOutput(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B5', 'OCCUPIED');

        $columnArray = [[1], [2], [2], [2], [3], [3], [3], [3], [4], [4], [4], [5]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [['#SPILL!'], [null], [null], [null], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'spill with B5 unchanged');
        self::assertFalse($sheet->isCellInSpillRange('B1'));
        self::assertFalse($sheet->isCellInSpillRange('B2'));
        self::assertFalse($sheet->isCellInSpillRange('B3'));
        self::assertFalse($sheet->isCellInSpillRange('B4'));
        self::assertFalse($sheet->isCellInSpillRange('B5'));
        self::assertFalse($sheet->isCellInSpillRange('Z9'));
        $calculation->clearCalculationCache();

        $columnArray = [[1], [2], [2], [2], [3], [3], [3], [3], [4], [4], [4], [4]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [[1], [2], [3], [4], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'fill B1:B4 with B5 unchanged');
        self::assertFalse($sheet->isCellInSpillRange('B1'));
        self::assertTrue($sheet->isCellInSpillRange('B2'));
        self::assertTrue($sheet->isCellInSpillRange('B3'));
        self::assertTrue($sheet->isCellInSpillRange('B4'));
        self::assertFalse($sheet->isCellInSpillRange('B5'));
        self::assertFalse($sheet->isCellInSpillRange('Z9'));
        $calculation->clearCalculationCache();

        $columnArray = [[1], [3], [3], [3], [3], [3], [3], [3], [3], [3], [3], [3]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [[1], [3], [null], [null], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'fill B1:B2(changed from prior) set B3:B4 to null B5 unchanged');
        self::assertFalse($sheet->isCellInSpillRange('B1'));
        self::assertTrue($sheet->isCellInSpillRange('B2'));
        self::assertFalse($sheet->isCellInSpillRange('B3'));
        self::assertFalse($sheet->isCellInSpillRange('B4'));
        self::assertFalse($sheet->isCellInSpillRange('B5'));
        self::assertFalse($sheet->isCellInSpillRange('Z9'));
        $calculation->clearCalculationCache();

        $columnArray = [[1], [2], [3], [3], [3], [3], [3], [3], [3], [3], [3], [3]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [[1], [2], [3], [null], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'fill B1:B3(B2 changed from prior) set B4 to null B5 unchanged');
        $calculation->clearCalculationCache();

        $columnArray = [[1], [2], [2], [2], [3], [3], [3], [3], [4], [4], [4], [5]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [['#SPILL!'], [null], [null], [null], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'spill clears B2:B4 with B5 unchanged');
        $calculation->clearCalculationCache();

        $sheet->setCellValue('Z1', '=SORT({7;5;1})');
        $sheet->getCell('Z1')->getCalculatedValue(); // populates Z1-Z3
        self::assertTrue($sheet->isCellInSpillRange('Z2'));
        self::assertTrue($sheet->isCellInSpillRange('Z3'));
        self::assertFalse($sheet->isCellInSpillRange('Z4'));
        self::assertFalse($sheet->isCellInSpillRange('Z1'));

        $spreadsheet->disconnectWorksheets();
    }

    public function testNonArrayOutput(): void
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B5', 'OCCUPIED');

        $columnArray = [[1], [2], [2], [2], [3], [3], [3], [3], [4], [4], [4], [4]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [[1], [null], [null], [null], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'only fill B1');
        self::assertFalse($sheet->isCellInSpillRange('B1'));
        self::assertFalse($sheet->isCellInSpillRange('B2'));
        self::assertFalse($sheet->isCellInSpillRange('B3'));
        self::assertFalse($sheet->isCellInSpillRange('B4'));
        self::assertFalse($sheet->isCellInSpillRange('B5'));
        self::assertFalse($sheet->isCellInSpillRange('Z9'));

        $spreadsheet->disconnectWorksheets();
    }

    public function testSpillOperator(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Product', 'Quantity', 'Price', 'Cost'],
            ['Apple', 20, 0.75],
            ['Kiwi', 8, 0.80],
            ['Lemon', 12, 0.70],
            ['Mango', 5, 1.75],
            ['Pineapple', 2, 2.00],
            ['Total'],
        ]);
        $sheet->getCell('D2')->setValue('=B2:B6*C2:C6');
        $sheet->getCell('D7')->setValue('=SUM(D2#)');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('C2:D6')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);
        self::assertEqualsWithDelta(
            [
                ['Cost'],
                [15.0],
                [6.4],
                [8.4],
                [8.75],
                [4.0],
                [42.55],
            ],
            $sheet->rangeToArray('D1:D7', calculateFormulas: true, formatData: false, reduceArrays: true),
            1.0e-10
        );
        $sheet->getCell('G2')->setValue('=B2#');
        self::assertSame('#REF!', $sheet->getCell('G2')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
