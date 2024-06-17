<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ArrayFunctionsSpillTest extends TestCase
{
    private string $arrayReturnType;

    protected function setUp(): void
    {
        $this->arrayReturnType = Calculation::getArrayReturnType();
    }

    protected function tearDown(): void
    {
        Calculation::setArrayReturnType($this->arrayReturnType);
    }

    public function testArrayOutput(): void
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B5', 'OCCUPIED');

        $columnArray = [[1], [2], [2], [2], [3], [3], [3], [3], [4], [4], [4], [5]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [['#SPILL!'], [null], [null], [null], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'spill with B5 unchanged');
        $calculation->clearCalculationCache();

        $columnArray = [[1], [2], [2], [2], [3], [3], [3], [3], [4], [4], [4], [4]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [[1], [2], [3], [4], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'fill B1:B4 with B5 unchanged');
        $calculation->clearCalculationCache();

        $columnArray = [[1], [3], [3], [3], [3], [3], [3], [3], [3], [3], [3], [3]];
        $sheet->fromArray($columnArray, 'A1');
        $sheet->setCellValue('B1', '=UNIQUE(A1:A12)');
        $expected = [[1], [3], [null], [null], ['OCCUPIED']];
        self::assertSame($expected, $sheet->rangeToArray('B1:B5', calculateFormulas: true, formatData: false, reduceArrays: true), 'fill B1:B2(changed from prior) set B3:B4 to null B5 unchanged');
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

        $spreadsheet->disconnectWorksheets();
    }
}
