<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Matrix;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    /**
     * @dataProvider providerINDEX
     *
     * @param mixed $expectedResult
     * @param mixed $matrix
     * @param mixed $rowNum
     * @param mixed $colNum
     */
    public function testINDEX($expectedResult, $matrix, $rowNum = null, $colNum = null): void
    {
        if ($rowNum === null) {
            $result = Matrix::index($matrix);
        } elseif ($colNum === null) {
            $result = Matrix::index($matrix, $rowNum);
        } else {
            $result = Matrix::index($matrix, $rowNum, $colNum);
        }
        self::assertEquals($expectedResult, $result);
    }

    public function providerINDEX(): array
    {
        return require 'tests/data/Calculation/LookupRef/INDEX.php';
    }

    /**
     * @dataProvider providerIndexArray
     */
    public function testIndexArray(array $expectedResult, string $matrix, string $rows, string $columns): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=INDEX({$matrix}, {$rows}, {$columns})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerIndexArray(): array
    {
        return [
            'row/column vectors' => [
                [[2, 3], [5, 6]],
                '{1, 2, 3; 4, 5, 6; 7, 8, 9}',
                '{1; 2}',
                '{2, 3}',
            ],
            'return row' => [
                [1 => [4, 5, 6]],
                '{1, 2, 3; 4, 5, 6; 7, 8, 9}',
                '2',
                '0',
            ],
            'return column' => [
                [[2], [5], [8]],
                '{1, 2, 3; 4, 5, 6; 7, 8, 9}',
                '0',
                '2',
            ],
        ];
    }
}
