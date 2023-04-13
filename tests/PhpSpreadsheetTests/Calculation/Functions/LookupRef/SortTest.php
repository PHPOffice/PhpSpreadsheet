<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Sort;
use PHPUnit\Framework\TestCase;

class SortTest extends TestCase
{
    public function testSortOnScalar(): void
    {
        $value = 'NON-ARRAY';

        $result = Sort::sort($value, 1, -1);
        self::assertSame($value, $result);
    }

    /**
     * @dataProvider providerSortWithScalarArgumentErrorReturns
     *
     * @param mixed $sortIndex
     * @param mixed$sortOrder
     */
    public function testSortWithScalarArgumentErrorReturns($sortIndex, $sortOrder = 1): void
    {
        $value = [[1, 2], [3, 4], [5, 6]];

        $result = Sort::sort($value, $sortIndex, $sortOrder);
        self::assertSame(ExcelError::VALUE(), $result);
    }

    public static function providerSortWithScalarArgumentErrorReturns(): array
    {
        return [
            'Negative sortIndex' => [-1, -1],
            'Non-numeric sortIndex' => ['A', -1],
            'Zero sortIndex' => [0, -1],
            'Too high sortIndex' => [3, -1],
            'Non-numeric sortOrder' => [1, 'A'],
            'Invalid negative sortOrder' => [1, -2],
            'Zero sortOrder' => [1, 0],
            'Invalid positive sortOrder' => [1, 2],
            'Too many sortOrders (scalar and array)' => [1, [-1, 1]],
            'Too many sortOrders (both array)' => [[1, 2], [1, 2, 3]],
            'Zero positive sortIndex in vector' => [[0, 1]],
            'Too high sortIndex in vector' => [[1, 3]],
            'Invalid sortOrder in vector' => [[1, 2], [1, -2]],
        ];
    }

    /**
     * @dataProvider providerSortByRow
     */
    public function testSortByRow(array $expectedResult, array $matrix, int $sortIndex, int $sortOrder = Sort::ORDER_ASCENDING): void
    {
        $result = Sort::sort($matrix, $sortIndex, $sortOrder);
        self::assertSame($expectedResult, $result);
    }

    public static function providerSortByRow(): array
    {
        return [
            [
                [[142], [378], [404], [445], [483], [622], [650], [691], [783], [961]],
                self::sampleDataForRow(),
                1,
            ],
            [
                [[961], [783], [691], [650], [622], [483], [445], [404], [378], [142]],
                self::sampleDataForRow(),
                1,
                Sort::ORDER_DESCENDING,
            ],
            [
                [['Peaches', 25], ['Cherries', 29], ['Grapes', 31], ['Lemons', 34], ['Oranges', 36], ['Apples', 38], ['Pears', 40]],
                [['Apples', 38], ['Cherries', 29], ['Grapes', 31], ['Lemons', 34], ['Oranges', 36], ['Peaches', 25], ['Pears', 40]],
                2,
            ],
        ];
    }

    /**
     * @dataProvider providerSortByRowMultiLevel
     */
    public function testSortByRowMultiLevel(array $expectedResult, array $matrix, array $sortIndex, int $sortOrder = Sort::ORDER_ASCENDING): void
    {
        $result = Sort::sort($matrix, $sortIndex, $sortOrder);
        self::assertSame($expectedResult, $result);
    }

    public static function providerSortByRowMultiLevel(): array
    {
        return [
            [
                [
                    ['East', 'Grapes', 31],
                    ['East', 'Lemons', 36],
                    ['North', 'Cherries', 29],
                    ['North', 'Grapes', 27],
                    ['North', 'Peaches', 25],
                    ['South', 'Apples', 38],
                    ['South', 'Cherries', 28],
                    ['South', 'Oranges', 36],
                    ['South', 'Pears', 40],
                    ['West', 'Apples', 30],
                    ['West', 'Lemons', 34],
                    ['West', 'Oranges', 25],
                ],
                self::sampleDataForMultiRow(),
                [1, 2],
            ],
            [
                [
                    ['East', 'Grapes', 31],
                    ['East', 'Lemons', 36],
                    ['North', 'Peaches', 25],
                    ['North', 'Grapes', 27],
                    ['North', 'Cherries', 29],
                    ['South', 'Cherries', 28],
                    ['South', 'Oranges', 36],
                    ['South', 'Apples', 38],
                    ['South', 'Pears', 40],
                    ['West', 'Oranges', 25],
                    ['West', 'Apples', 30],
                    ['West', 'Lemons', 34],
                ],
                self::sampleDataForMultiRow(),
                [1, 3],
            ],
            [
                [
                    ['West', 'Apples', 30],
                    ['South', 'Apples', 38],
                    ['South', 'Cherries', 28],
                    ['North', 'Cherries', 29],
                    ['North', 'Grapes', 27],
                    ['East', 'Grapes', 31],
                    ['West', 'Lemons', 34],
                    ['East', 'Lemons', 36],
                    ['West', 'Oranges', 25],
                    ['South', 'Oranges', 36],
                    ['North', 'Peaches', 25],
                    ['South', 'Pears', 40],
                ],
                self::sampleDataForMultiRow(),
                [2, 3],
            ],
        ];
    }

    /**
     * @dataProvider providerSortByColumn
     */
    public function testSortByColumn(array $expectedResult, array $matrix, int $sortIndex, int $sortOrder): void
    {
        $result = Sort::sort($matrix, $sortIndex, $sortOrder, true);
        self::assertSame($expectedResult, $result);
    }

    public static function providerSortByColumn(): array
    {
        return [
            [
                [[142, 378, 404, 445, 483, 622, 650, 691, 783, 961]],
                self::sampleDataForColumn(),
                1,
                Sort::ORDER_ASCENDING,
            ],
            [
                [[961, 783, 691, 650, 622, 483, 445, 404, 378, 142]],
                self::sampleDataForColumn(),
                1,
                Sort::ORDER_DESCENDING,
            ],
        ];
    }

    public static function sampleDataForRow(): array
    {
        return [
            [622], [961], [691], [445], [378], [483], [650], [783], [142], [404],
        ];
    }

    public static function sampleDataForMultiRow(): array
    {
        return [
            ['South', 'Pears', 40],
            ['South', 'Apples', 38],
            ['South', 'Oranges', 36],
            ['East', 'Lemons', 36],
            ['West', 'Lemons', 34],
            ['East', 'Grapes', 31],
            ['West', 'Apples', 30],
            ['North', 'Cherries', 29],
            ['South', 'Cherries', 28],
            ['North', 'Grapes', 27],
            ['North', 'Peaches', 25],
            ['West', 'Oranges', 25],
        ];
    }

    public static function sampleDataForColumn(): array
    {
        return [
            [622, 961, 691, 445, 378, 483, 650, 783, 142, 404],
        ];
    }
}
