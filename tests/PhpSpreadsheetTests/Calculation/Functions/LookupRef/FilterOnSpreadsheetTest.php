<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class FilterOnSpreadsheetTest extends TestCase
{
    public function testFilterByRow(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $criteria = [[true], [false], [false], [false], [true], [false], [false], [false], [false], [false], [false], [true], [false], [false], [false], [true]];
        $sheet->fromArray($criteria, null, 'A1', true);
        $sheet->fromArray($this->sampleDataForRow(), null, 'C1', true);
        $sheet->getCell('H1')->setValue('=FILTER(C1:F16, A1:A16)');
        $expectedResult = [
            ['East', 'Tom', 'Apple', 6830],
            ['East', 'Fritz', 'Apple', 4394],
            ['South', 'Sal', 'Apple', 1310],
            ['South', 'Hector', 'Apple', 8144],
        ];
        $result = $sheet->getCell('H1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public function testFilterByColumn(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $criteria = [[false, false, true, false, true, false, false, false, true, true]];
        $sheet->fromArray($criteria, null, 'A1', true);
        $sheet->fromArray($this->sampleDataForColumn(), null, 'A3', true);
        $sheet->getCell('A8')->setValue('=FILTER(A3:J5, A1:J1)');
        $expectedResult = [
            ['Betty', 'Charlotte', 'Oliver', 'Zoe'],
            ['B', 'B', 'B', 'B'],
            [1, 2, 4, 8],
        ];
        $result = $sheet->getCell('A8')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public function testFilterInvalidMatchArray(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($this->sampleDataForColumn(), null, 'A3', true);
        $sheet->getCell('A12')->setValue('=FILTER(A3:J5, "INVALID")');
        $expectedResult = ExcelError::VALUE();
        $result = $sheet->getCell('A12')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public function testFilterInvalidLookupArray(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $criteria = [[false, false, true, false, true, false, false, false, true, true]];
        $sheet->fromArray($criteria, null, 'A1', true);
        $sheet->fromArray($this->sampleDataForColumn(), null, 'A3', true);
        $sheet->getCell('A14')->setValue('=FILTER("invalid", A1:J1)');
        $expectedResult = ExcelError::VALUE();
        $result = $sheet->getCell('A14')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public function testFilterEmpty(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)
            ->setInstanceArrayReturnType(
                Calculation::RETURN_ARRAY_AS_ARRAY
            );
        $sheet = $spreadsheet->getActiveSheet();
        $criteria = [[false, false, true, false, true, false, false, false, true, true]];
        $sheet->fromArray($criteria, null, 'A1', true);
        $sheet->fromArray($this->sampleDataForColumn(), null, 'A3', true);
        $sheet->getCell('A16')->setValue('=FILTER(A3:B5, A1:B1)');
        $expectedResult = ExcelError::CALC();
        $result = $sheet->getCell('A16')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<array{string, string, string, int}> */
    protected function sampleDataForRow(): array
    {
        return [
            ['East', 'Tom', 'Apple', 6830],
            ['West', 'Fred', 'Grape', 5619],
            ['North', 'Amy', 'Pear', 4565],
            ['South', 'Sal', 'Banana', 5323],
            ['East', 'Fritz', 'Apple', 4394],
            ['West', 'Sravan', 'Grape', 7195],
            ['North', 'Xi', 'Pear', 5231],
            ['South', 'Hector', 'Banana', 2427],
            ['East', 'Tom', 'Banana', 4213],
            ['West', 'Fred', 'Pear', 3239],
            ['North', 'Amy', 'Grape', 6420],
            ['South', 'Sal', 'Apple', 1310],
            ['East', 'Fritz', 'Banana', 6274],
            ['West', 'Sravan', 'Pear', 4894],
            ['North', 'Xi', 'Grape', 7580],
            ['South', 'Hector', 'Apple', 8144],
        ];
    }

    /** @return array<array<int|string>> */
    protected function sampleDataForColumn(): array
    {
        return [
            ['Aiden', 'Andrew', 'Betty', 'Caden', 'Charlotte', 'Emma', 'Isabella', 'Mason', 'Oliver', 'Zoe'],
            ['A', 'C', 'B', 'A', 'B', 'C', 'A', 'A', 'B', 'B'],
            [0, 4, 1, 2, 2, 0, 2, 4, 4, 8],
        ];
    }
}
