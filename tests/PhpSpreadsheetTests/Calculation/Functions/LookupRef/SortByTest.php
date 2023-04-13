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

        $result = Sort::sortBy($value);
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

    public static function providerSortWithScalarArgumentErrorReturns(): array
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
    public function testSortByRow(array $expectedResult, array $matrix, ...$args): void
    {
        $result = Sort::sortBy($matrix, ...$args);
        self::assertSame($expectedResult, $result);
    }

    public static function providerSortByRow(): array
    {
        return [
            'Simple sort by age' => [
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
                self::sampleDataForSimpleSort(),
                array_column(self::sampleDataForSimpleSort(), 1),
            ],
            'Simple sort by name' => [
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
                self::sampleDataForSimpleSort(),
                array_column(self::sampleDataForSimpleSort(), 0),
            ],
            'Row vector' => [
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
                self::sampleDataForSimpleSort(),
                ['Tom', 'Fred', 'Amy', 'Sal', 'Fritz', 'Srivan', 'Xi', 'Hector'],
            ],
            'Column vector' => [
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
                self::sampleDataForSimpleSort(),
                [['Tom'], ['Fred'], ['Amy'], ['Sal'], ['Fritz'], ['Srivan'], ['Xi'], ['Hector']],
            ],
            'Sort by region asc, name asc' => [
                [
                    ['East', 'Fritz', 19],
                    ['East', 'Tom', 52],
                    ['North', 'Amy', 22],
                    ['North', 'Xi', 19],
                    ['South', 'Hector', 66],
                    ['South', 'Sal', 73],
                    ['West', 'Fred', 65],
                    ['West', 'Srivan', 39],
                ],
                self::sampleDataForMultiSort(),
                array_column(self::sampleDataForMultiSort(), 0),
                Sort::ORDER_ASCENDING,
                array_column(self::sampleDataForMultiSort(), 1),
            ],
            'Sort by region asc, age desc' => [
                [
                    ['East', 'Tom', 52],
                    ['East', 'Fritz', 19],
                    ['North', 'Amy', 22],
                    ['North', 'Xi', 19],
                    ['South', 'Sal', 73],
                    ['South', 'Hector', 66],
                    ['West', 'Fred', 65],
                    ['West', 'Srivan', 39],
                ],
                self::sampleDataForMultiSort(),
                array_column(self::sampleDataForMultiSort(), 0),
                Sort::ORDER_ASCENDING,
                array_column(self::sampleDataForMultiSort(), 2),
                Sort::ORDER_DESCENDING,
            ],
        ];
    }

    private static function sampleDataForSimpleSort(): array
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

    private static function sampleDataForMultiSort(): array
    {
        return [
            ['North', 'Amy', 22],
            ['West', 'Fred', 65],
            ['East', 'Fritz', 19],
            ['South', 'Hector', 66],
            ['South', 'Sal', 73],
            ['West', 'Srivan', 39],
            ['East', 'Tom', 52],
            ['North', 'Xi', 19],
        ];
    }
}
