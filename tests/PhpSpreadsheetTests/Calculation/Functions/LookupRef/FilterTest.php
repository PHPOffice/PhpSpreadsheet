<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Filter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testFilterByRow(): void
    {
        $criteria = [[true], [false], [false], [false], [true], [false], [false], [false], [false], [false], [false], [true], [false], [false], [false], [true]];
        $expectedResult = [
            ['East', 'Tom', 'Apple', 6830],
            ['East', 'Fritz', 'Apple', 4394],
            ['South', 'Sal', 'Apple', 1310],
            ['South', 'Hector', 'Apple', 8144],
        ];
        $result = Filter::filter($this->sampleDataForRow(), $criteria);
        self::assertSame($expectedResult, $result);
    }

    public function testFilterByColumn(): void
    {
        $criteria = [[false, false, true, false, true, false, false, false, true, true]];
        $expectedResult = [
            ['Betty', 'Charlotte', 'Oliver', 'Zoe'],
            ['B', 'B', 'B', 'B'],
            [1, 2, 4, 8],
        ];
        $result = Filter::filter($this->sampleDataForColumn(), $criteria);
        self::assertSame($expectedResult, $result);
    }

    public function testFilterException(): void
    {
        $criteria = 'INVALID';
        $result = Filter::filter($this->sampleDataForColumn(), $criteria);
        self::assertSame(ExcelError::VALUE(), $result);
    }

    public function testFilterEmpty(): void
    {
        $criteria = [[false], [false], [false]];
        $expectedResult = ExcelError::CALC();
        $result = Filter::filter([[1], [2], [3]], $criteria);
        self::assertSame($expectedResult, $result);

        $expectedResult = 'Invalid Data';
        $result = Filter::filter([[1], [2], [3]], $criteria, $expectedResult);
        self::assertSame($expectedResult, $result);
    }

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

    protected function sampleDataForColumn(): array
    {
        return [
            ['Aiden', 'Andrew', 'Betty', 'Caden', 'Charlotte', 'Emma', 'Isabella', 'Mason', 'Oliver', 'Zoe'],
            ['A', 'C', 'B', 'A', 'B', 'C', 'A', 'A', 'B', 'B'],
            [0, 4, 1, 2, 2, 0, 2, 4, 4, 8],
        ];
    }
}
