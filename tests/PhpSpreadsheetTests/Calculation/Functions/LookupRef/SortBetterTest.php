<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Sort;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SortBetterTest extends TestCase
{
    private Spreadsheet $spreadsheet;

    private int $maxRow;

    private string $maxCol;

    private string $range;

    protected function tearDown(): void
    {
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    /** @param mixed[] $values */
    public function getSheet(array $values): Worksheet
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->returnArrayAsArray();
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray($values);
        $this->maxRow = $sheet->getHighestDataRow();
        $this->maxCol = $sheet->getHighestDataColumn();
        $this->range = "A1:{$this->maxCol}{$this->maxRow}";

        return $sheet;
    }

    public function testSortOnScalar(): void
    {
        $value = 'NON-ARRAY';
        $sheet = $this->getSheet([$value]);
        $sheet->getCell('Z1')->setValue('=SORT(A1, 1, -1)');
        $sheet->getCell('Z2')->setValue('=SORT(A1:A1, 1, -1)');
        $sheet->getCell('Z3')->setValue('=SORT(A1, "A", -1)');

        $result = $sheet->getCell('Z1')->getCalculatedValue();
        self::assertSame([[$value]], $result);
        $result = $sheet->getCell('Z2')->getCalculatedValue();
        self::assertSame([[$value]], $result);
        $result = $sheet->getCell('Z3')->getCalculatedValue();
        self::assertSame(ExcelError::VALUE(), $result);
    }

    #[DataProvider('providerSortWithScalarArgumentErrorReturns')]
    public function testSortWithScalarArgumentErrorReturns(int|string $sortIndex, int|string $sortOrder = 1): void
    {
        $value = [[1, 2], [3, 4], [5, 6]];
        $sheet = $this->getSheet($value);
        $formula = "=SORT({$this->range}, $sortIndex, $sortOrder)";
        $sheet->getCell('Z1')->setValue($formula);
        $result = $sheet->getCell('Z1')->getCalculatedValue();
        self::assertSame(ExcelError::VALUE(), $result);
    }

    public static function providerSortWithScalarArgumentErrorReturns(): array
    {
        return [
            'Negative sortIndex' => [-1, -1],
            'Non-numeric sortIndex' => ['"A"', -1],
            'Zero sortIndex' => [0, -1],
            'Too high sortIndex' => [3, -1],
            'Non-numeric sortOrder' => [1, '"A"'],
            'Invalid negative sortOrder' => [1, -2],
            'Zero sortOrder' => [1, 0],
            'Invalid positive sortOrder' => [1, 2],
            'Too many sortOrders (scalar and array)' => [1, '{-1, 1}'],
            'Too many sortOrders (both array)' => ['{1, 2}', '{1, 2, 3}'],
            'Zero positive sortIndex in vector' => ['{0, 1}'],
            'Too high sortIndex in vector' => ['{1, 3}'],
            'Invalid sortOrder in vector' => ['{1, 2}', '{1, -2}'],
        ];
    }

    /**
     * @param mixed[] $expectedResult
     * @param mixed[] $matrix
     */
    #[DataProvider('providerSortByRow')]
    public function testSortByRow(array $expectedResult, array $matrix, int $sortIndex, int $sortOrder = Sort::ORDER_ASCENDING): void
    {
        $sheet = $this->getSheet($matrix);
        $formula = "=SORT({$this->range}, $sortIndex, $sortOrder)";
        $sheet->getCell('Z1')->setValue($formula);
        $result = $sheet->getCell('Z1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    /** @return mixed[] */
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
     * @param mixed[] $expectedResult
     * @param mixed[] $matrix
     */
    #[DataProvider('providerSortByRowMultiLevel')]
    public function testSortByRowMultiLevel(array $expectedResult, array $matrix, string $sortIndex, int $sortOrder = Sort::ORDER_ASCENDING): void
    {
        $sheet = $this->getSheet($matrix);
        $formula = "=SORT({$this->range}, $sortIndex, $sortOrder)";
        $sheet->getCell('Z1')->setValue($formula);
        $result = $sheet->getCell('Z1')->getCalculatedValue();
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
                '{1, 2}',
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
                '{1, 3}',
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
                '{2, 3}',
            ],
        ];
    }

    /**
     * @param mixed[] $expectedResult
     * @param mixed[] $matrix
     */
    #[DataProvider('providerSortByColumn')]
    public function testSortByColumn(array $expectedResult, array $matrix, int $sortIndex, int $sortOrder): void
    {
        $sheet = $this->getSheet($matrix);
        $formula = "=SORT({$this->range}, $sortIndex, $sortOrder, TRUE)";
        $sheet->getCell('Z1')->setValue($formula);
        $result = $sheet->getCell('Z1')->getCalculatedValue();
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

    /** @return array<int[]> */
    public static function sampleDataForRow(): array
    {
        return [
            [622], [961], [691], [445], [378], [483], [650], [783], [142], [404],
        ];
    }

    /** @return array<array{string, string, int}> */
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

    /** @return array<int[]> */
    public static function sampleDataForColumn(): array
    {
        return [
            [622, 961, 691, 445, 378, 483, 650, 783, 142, 404],
        ];
    }
}
