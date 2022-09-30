<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class FunctionsMatrixResizeTest extends TestCase
{
    /**
     * @dataProvider providerMatrixResize
     */
    public function testIsBlank(array $matrix, int $rows, int $columns, array $expectedResult): void
    {
        $result = Functions::resizeMatrix($matrix, $rows, $columns);

        self::assertSame($expectedResult, $result);
    }

    public function providerMatrixResize(): array
    {
        return require 'tests/data/Calculation/MatrixResize.php';
    }
}
