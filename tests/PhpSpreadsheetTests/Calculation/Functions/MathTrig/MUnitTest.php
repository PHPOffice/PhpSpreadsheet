<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions;

class MUnitTest extends AllSetupTeardown
{
    public function testMUNIT(): void
    {
        $identity = MatrixFunctions::Identity(3);
        self::assertEquals([[1, 0, 0], [0, 1, 0], [0, 0, 1]], $identity);
        $startArray = [[1, 2, 2], [4, 5, 6], [7, 8, 9]];
        $resultArray = MatrixFunctions::Multiply($startArray, $identity);
        self::assertEquals($startArray, $resultArray);
        $inverseArray = MatrixFunctions::Inverse($startArray);
        $resultArray = MatrixFunctions::Multiply($startArray, $inverseArray);
        self::assertEquals($identity, $resultArray);
        self::assertEquals('#VALUE!', MatrixFunctions::Identity(0));
        self::assertEquals('#VALUE!', MatrixFunctions::Identity(-1));
        self::assertEquals('#VALUE!', MatrixFunctions::Identity('X'));
    }
}
