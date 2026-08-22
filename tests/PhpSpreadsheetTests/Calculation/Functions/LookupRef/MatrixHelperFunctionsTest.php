<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MatrixHelperFunctionsTest extends TestCase
{
    /** @param mixed[] $array */
    #[DataProvider('columnVectorProvider')]
    public function testIsColumnVector(bool $expectedResult, array $array): void
    {
        $result = Matrix::isColumnVector($array);
        self::assertSame($expectedResult, $result);
    }

    /** @param mixed[] $array */
    #[DataProvider('rowVectorProvider')]
    public function testIsRowVector(bool $expectedResult, array $array): void
    {
        $result = Matrix::isRowVector($array);
        self::assertSame($expectedResult, $result);
    }

    public static function columnVectorProvider(): array
    {
        return [
            [
                true,
                [
                    [1], [2], [3],
                ],
            ],
            [
                false,
                [1, 2, 3],
            ],
            [
                false,
                [
                    [1, 2, 3],
                    [4, 5, 6],
                ],
            ],
        ];
    }

    public static function rowVectorProvider(): array
    {
        return [
            [
                false,
                [
                    [1], [2], [3],
                ],
            ],
            [
                true,
                [1, 2, 3],
            ],
            [
                true,
                [[1, 2, 3]],
            ],
            [
                false,
                [
                    [1, 2, 3],
                    [4, 5, 6],
                ],
            ],
        ];
    }
}
