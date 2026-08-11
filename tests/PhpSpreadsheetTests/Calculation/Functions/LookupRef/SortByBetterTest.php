<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Sort;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SortByBetterTest extends TestCase
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
        $byArray = 'ARRAY';
        $sheet = $this->getSheet([$value, $byArray]);
        $sheet->getCell('Z1')->setValue('=SORTBY(A1, B1:B1, 1)');
        $sheet->getCell('Z2')->setValue('=SORTBY(A1, B1, 1)');
        $sheet->getCell('Z3')->setValue('=SORTBY(A1, B1, "A")');

        $result = $sheet->getCell('Z1')->getCalculatedValue();
        self::assertSame([[$value]], $result);
        $result = $sheet->getCell('Z2')->getCalculatedValue();
        self::assertSame([[$value]], $result);
        $result = $sheet->getCell('Z3')->getCalculatedValue();
        self::assertSame(ExcelError::VALUE(), $result);
    }

    #[DataProvider('providerSortWithScalarArgumentErrorReturns')]
    public function testSortByWithArgumentErrorReturns(string $byArray, int|string $sortOrder = 1): void
    {
        $value = [[1, 2], [3, 4], [5, 6]];
        $sheet = $this->getSheet($value);
        $formula = "=SORTBY({$this->range}, $byArray, $sortOrder)";
        $sheet->getCell('Z1')->setValue($formula);
        $result = $sheet->getCell('Z1')->getCalculatedValue();
        self::assertSame(ExcelError::VALUE(), $result);
    }

    public static function providerSortWithScalarArgumentErrorReturns(): array
    {
        return [
            'Non-array sortIndex' => ['A', 1],
            'Mismatched sortIndex count' => ['{1, 2, 3, 4}', 1],
            'Non-numeric sortOrder' => ['{1, 2, 3}', '"A"'],
            'Invalid negative sortOrder' => ['{1, 2, 3}', -2],
            'Zero sortOrder' => ['{1, 2, 3}', 0],
            'Invalid positive sortOrder' => ['{1, 2, 3}', 2],
        ];
    }

    /**
     * @param mixed[] $matrix
     */
    #[DataProvider('providerSortByRow')]
    public function testSortByRow(array $expectedResult, array $matrix, string $byArray, ?int $sortOrder = null, ?string $byArray2 = null, ?int $sortOrder2 = null): void
    {
        $sheet = $this->getSheet($matrix);
        $sheet->fromArray([['B'], ['D'], ['A'], ['C'], ['H'], ['G'], ['F'], ['E']], null, 'G1', true);
        $sheet->fromArray([[true], [false], [true], [false], [true], [false], [true], [false]], null, 'H1', true);
        $formula = "=SORTBY({$this->range}, $byArray";
        if ($sortOrder !== null) {
            $formula .= ", $sortOrder";
            if ($byArray2 !== null) {
                $formula .= ", $byArray2";
                if ($sortOrder2 !== null) {
                    $formula .= ", $sortOrder2";
                }
            }
        }
        $formula .= ')';
        $sheet->getCell('Z1')->setValue($formula);
        $result = $sheet->getCell('Z1')->getCalculatedValue();
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
                'B1:B8',
                1,
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
                'A1:A8',
            ],
            'More realistic example of when to use SORTBY vs SORT' => [
                [
                    ['Amy', 22],
                    ['Tom', 52],
                    ['Sal', 73],
                    ['Fred', 65],
                    ['Hector', 66],
                    ['Xi', 19],
                    ['Srivan', 39],
                    ['Fritz', 19],
                ],
                self::sampleDataForSimpleSort(),
                'G1:G8',
            ],
            'Boolean sort indexes' => [
                [
                    ['Fred', 65],
                    ['Sal', 73],
                    ['Srivan', 39],
                    ['Hector', 66],
                    ['Tom', 52],
                    ['Amy', 22],
                    ['Fritz', 19],
                    ['Xi', 19],
                ],
                self::sampleDataForSimpleSort(),
                'H1:H8',
            ],
            'Simple sort by name descending' => [
                [
                    ['Xi', 19],
                    ['Tom', 52],
                    ['Srivan', 39],
                    ['Sal', 73],
                    ['Hector', 66],
                    ['Fritz', 19],
                    ['Fred', 65],
                    ['Amy', 22],
                ],
                self::sampleDataForSimpleSort(),
                'A1:A8',
                -1,
            ],
            'Row vector (using Dritz instead of Fritz)' => [
                [
                    ['Amy', 22],
                    ['Fritz', 19],
                    ['Fred', 65],
                    ['Hector', 66],
                    ['Sal', 73],
                    ['Srivan', 39],
                    ['Tom', 52],
                    ['Xi', 19],
                ],
                self::sampleDataForSimpleSort(),
                '{"Tom";"Fred";"Amy";"Sal";"Dritz";"Srivan";"Xi";"Hector"}',
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
                'A1:A8',
                Sort::ORDER_ASCENDING,
                'B1:B8',
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
                'A1:A8',
                Sort::ORDER_ASCENDING,
                'C1:C8',
                Sort::ORDER_DESCENDING,
            ],
        ];
    }

    /** @return array<array{string, int}> */
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

    /** @return array<array{string, string, int}> */
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

    public function testSortByColumn(): void
    {
        $matrix = [
            ['Tom', 'Fred', 'Amy', 'Sal', 'Fritz', 'Srivan', 'Xi', 'Hector'],
            [52, 65, 22, 73, 19, 39, 19, 66],
        ];
        $sheet = $this->getSheet($matrix);
        $formula = "=SORTBY({$this->range}, A1:H1)";
        $expectedResult = [
            ['Amy', 'Fred', 'Fritz', 'Hector', 'Sal', 'Srivan', 'Tom', 'Xi'],
            [22, 65, 19, 66, 73, 39, 52, 19],
        ];
        $sheet->getCell('Z1')->setValue($formula);
        $result = $sheet->getCell('Z1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }
}
