<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use ArrayObject;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayTest extends TestCase
{
    private string $originalArrayReturnType;

    protected function setUp(): void
    {
        $this->originalArrayReturnType = Calculation::getArrayReturnType();
    }

    protected function tearDown(): void
    {
        Calculation::setArrayReturnType($this->originalArrayReturnType);
    }

    private function setupMatrix(Worksheet $sheet): void
    {
        $sheet->setCellValue('A1', 2.0);
        $sheet->setCellValue('A2', 0.0);
        $sheet->setCellValue('B1', 0.0);
        $sheet->setCellValue('B2', 1.0);
    }

    public function testMultiDimensionalArrayIsFlattened(): void
    {
        $array = [
            0 => [
                0 => [
                    32 => [
                        'B' => 'PHP',
                    ],
                ],
            ],
            1 => [
                0 => [
                    32 => [
                        'C' => 'Spreadsheet',
                    ],
                ],
            ],
        ];

        $values = Functions::flattenArray($array);

        self::assertCount(2, $values);
        self::assertSame('PHP', $values[0]);
        self::assertSame('Spreadsheet', $values[1]);
    }

    /**
     * @param mixed[] $expected
     * @param mixed[] $array
     */
    #[DataProvider('providerFlattenArray')]
    public function testFlattenArray(array $expected, array $array): void
    {
        self::assertSame($expected, Functions::flattenArray($array));
        self::assertSame($expected, Functions::flattenArray2($array));
        self::assertSame($expected, Functions::flattenArray2(...array_values($array)));
    }

    public static function providerFlattenArray(): array
    {
        return [
            'empty array' => [[], []],
            'flat list unchanged' => [[1, 2, 3], [1, 2, 3]],
            'keys dropped, order kept' => [
                ['x', 'y', 'z'],
                ['b' => 'x', 3 => 'y', 'a' => 'z'],
            ],
            'range read row by row' => [
                [1, 2, 3, 4, 5, 6],
                [[1 => ['A' => 1, 'B' => 2], 2 => ['A' => 3, 'B' => 4], 3 => ['A' => 5, 'B' => 6]]],
            ],
            'deep nesting, issue 2955' => [
                [1, 2, 3, 4, 5, 6],
                [1, [2, [3, [4, [5]]]], 6],
            ],
            'depth first' => [
                ['a', 'b', 'c', 'd', 'e', 'f'],
                [['a', ['b']], 'c', [[['d']], 'e'], 'f'],
            ],
            'empty nested arrays add nothing' => [
                [1, 2],
                [[], 1, [[]], [[], [[[]]]], 2, []],
            ],
            'only empty nested arrays' => [
                [],
                [[], [[]], [[[]]]],
            ],
            'scalars, null and errors kept' => [
                [null, true, false, '', 0, '0', 1.5, ExcelError::NA()],
                [[null, true], [false, ['', 0]], '0', [[1.5]], ExcelError::NA()],
            ],
        ];
    }

    public function testObjectsAreNotFlattened(): void
    {
        $arrayObject = new ArrayObject([1, 2]);
        $object = new stdClass();
        $array = [[$arrayObject], [[$object, null]]];

        self::assertSame([$arrayObject, $object, null], Functions::flattenArray($array));
        self::assertSame([$arrayObject, $object, null], Functions::flattenArray2(...$array));
    }

    public function testFlattenArrayOfNonArray(): void
    {
        self::assertSame([], Functions::flattenArray(null));
        self::assertSame(['abc'], Functions::flattenArray('abc'));
        self::assertSame([1.5], Functions::flattenArray(1.5));
        self::assertSame([false], Functions::flattenArray(false));
    }

    public function testFlattenArray2Variadic(): void
    {
        self::assertSame([], Functions::flattenArray2());
        self::assertSame([null], Functions::flattenArray2(null));
        self::assertSame(['abc'], Functions::flattenArray2('abc'));
        self::assertSame(
            [1, 2, 3, 'x', null, 4],
            Functions::flattenArray2(1, [2, [3]], 'x', [[null]], [], [[4]])
        );
    }

    public function testLargeRangeIsFlattenedInOrder(): void
    {
        $range = [];
        $expected = [];
        for ($row = 1; $row <= 3000; ++$row) {
            foreach (['A', 'B', 'C'] as $column) {
                $range[$row][$column] = $column . $row;
                $expected[] = $column . $row;
            }
        }

        self::assertSame($expected, Functions::flattenArray([$range]));
        self::assertSame($expected, Functions::flattenArray2($range));
    }

    public function testSetArrayReturnTypeWithValidValue(): void
    {
        self::assertTrue(Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY));
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, Calculation::getArrayReturnType());
    }

    public function testSetArrayReturnTypeWithInvalidValue(): void
    {
        $originalType = Calculation::getArrayReturnType();
        self::assertFalse(Calculation::setArrayReturnType('xxx'));
        self::assertSame($originalType, Calculation::getArrayReturnType());
    }

    public function testInstanceArrayReturnTypeInheritsFromStatic(): void
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $calculation = new Calculation();
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, $calculation->getInstanceArrayReturnType());
    }

    public function testInstanceArrayReturnTypeCanBeOverridden(): void
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $calculation = new Calculation();
        self::assertTrue($calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ERROR));
        self::assertSame(Calculation::RETURN_ARRAY_AS_ARRAY, Calculation::getArrayReturnType());
        self::assertSame(Calculation::RETURN_ARRAY_AS_ERROR, $calculation->getInstanceArrayReturnType());
    }

    public function testSetInstanceArrayReturnTypeWithInvalidValue(): void
    {
        $calculation = new Calculation();
        self::assertFalse($calculation->setInstanceArrayReturnType('xxx'));
    }

    public function testReturnTypeAsError(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $this->setupMatrix($sheet);
        $sheet->setCellValue('D1', '=MINVERSE(A1:B2)');

        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ERROR);
        self::assertSame('#VALUE!', $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testReturnTypeAsValue(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $this->setupMatrix($sheet);
        $sheet->setCellValue('D1', '=MINVERSE(A1:B2)');

        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);
        self::assertSame(0.5, $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testReturnTypeAsArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $calculation = Calculation::getInstance($spreadsheet);
        $this->setupMatrix($sheet);
        $sheet->setCellValue('D1', '=MINVERSE(A1:B2)');

        $calculation->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        self::assertSame([[0.5, 0.0], [0.0, 1.0]], $sheet->getCell('D1')->getCalculatedValue());

        $spreadsheet->disconnectWorksheets();
    }
}
