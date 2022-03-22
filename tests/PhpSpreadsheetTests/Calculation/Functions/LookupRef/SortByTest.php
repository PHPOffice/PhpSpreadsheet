<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Sort;
use PHPUnit\Framework\TestCase;

class SortByTest extends TestCase
{
    public function testSortOnScalar(): void
    {
        $value = 'NON-ARRAY';

        $result = Sort::sort($value, [1]);
        self::assertSame($value, $result);
    }

    /**
     * @dataProvider providerSortWithScalarArgumentErrorReturns
     *
     * @param mixed $sortIndex
     * @param mixed$sortOrder
     */
    public function testSortByWithArgumentErrorReturns($sortIndex, $sortOrder = 1): void
    {
        $value = [[1, 2], [3, 4], [5, 6]];

        $result = Sort::sortBy($value, $sortIndex, $sortOrder);
        self::assertSame(ExcelError::VALUE(), $result);
    }

    public function providerSortWithScalarArgumentErrorReturns(): array
    {
        return [
            'Non-array sortIndex' => ['A', 1],
            'Mismatched sortIndex count' => [[1, 2, 3, 4], 1],
            'Non-numeric sortOrder' => [[1, 2, 3], 'A'],
            'Invalid negative sortOrder' => [[1, 2, 3], -2],
            'Zero sortOrder' => [[1, 2, 3], 0],
            'Invalid positive sortOrder' => [[1, 2, 3], 2],
        ];
    }

    /**
     * @dataProvider providerSortByRow
     */
    public function testSortByRow(array $expectedResult, array $matrix, array $sortIndex, int $sortOrder = Sort::ORDER_ASCENDING): void
    {
        $result = Sort::sortBy($matrix, $sortIndex, $sortOrder);
        self::assertSame($expectedResult, $result);
    }

    public function providerSortByRow(): array
    {
        return [
            [
                [
                    ['Fritz', 19],
                    ['Xi', 19],
                    ['Amy', 22],
                    ['Srivan', 39],
                    ['Tom', 52],
                    ['Fred', 65],
                    ['Hector', 66],
                    ['Sal', 73],
                ],
                $this->sampleDataForRow(),
                array_column($this->sampleDataForRow(), 1),
            ],
            [
                [
                    ['Amy', 22],
                    ['Fred', 65],
                    ['Fritz', 19],
                    ['Hector', 66],
                    ['Sal', 73],
                    ['Srivan', 39],
                    ['Tom', 52],
                    ['Xi', 19],
                ],
                $this->sampleDataForRow(),
                array_column($this->sampleDataForRow(), 0),
            ],
            [
                [
                    ['Amy', 22],
                    ['Fred', 65],
                    ['Fritz', 19],
                    ['Hector', 66],
                    ['Sal', 73],
                    ['Srivan', 39],
                    ['Tom', 52],
                    ['Xi', 19],
                ],
                $this->sampleDataForRow(),
                ['Tom', 'Fred', 'Amy', 'Sal', 'Fritz', 'Srivan', 'Xi', 'Hector'],
            ],
            [
                [
                    ['Amy', 22],
                    ['Fred', 65],
                    ['Fritz', 19],
                    ['Hector', 66],
                    ['Sal', 73],
                    ['Srivan', 39],
                    ['Tom', 52],
                    ['Xi', 19],
                ],
                $this->sampleDataForRow(),
                [['Tom'], ['Fred'], ['Amy'], ['Sal'], ['Fritz'], ['Srivan'], ['Xi'], ['Hector']],
            ],
        ];
    }

    private function sampleDataForRow(): array
    {
        return [
            ['Tom', 52],
            ['Fred', 65],
            ['Amy', 22],
            ['Sal', 73],
            ['Fritz', 19],
            ['Srivan', 39],
            ['Xi', 19],
            ['Hector', 66],
        ];
    }
}
