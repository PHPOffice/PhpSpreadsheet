<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions;

class MUnitTest extends AllSetupTeardown
{
    const MU_PRECISION = 1.0E-12;

    public function testMUNIT(): void
    {
        $identity = MatrixFunctions::identity(3);
        self::assertEquals([[1, 0, 0], [0, 1, 0], [0, 0, 1]], $identity);
        $startArray = [[1, 2, 2], [4, 5, 6], [7, 8, 9]];
        $resultArray = MatrixFunctions::multiply($startArray, $identity);
        self::assertEquals($startArray, $resultArray);
        $inverseArray = MatrixFunctions::inverse($startArray);
        $resultArray = MatrixFunctions::multiply($startArray, $inverseArray);
        self::assertEqualsWithDelta($identity, $resultArray, self::MU_PRECISION);
        self::assertEquals('#VALUE!', MatrixFunctions::identity(0));
        self::assertEquals('#VALUE!', MatrixFunctions::identity(-1));
        self::assertEquals('#VALUE!', MatrixFunctions::identity('X'));
    }
}
