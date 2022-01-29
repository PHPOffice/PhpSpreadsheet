<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class SequenceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSEQUENCE
     *
     * @param mixed[] $arguments
     * @param mixed[] $expectedResult
     */
    public function testSEQUENCE(array $arguments, array $expectedResult): void
    {
        $result = MathTrig\MatrixFunctions::sequence(...$arguments);
        self::assertEquals($expectedResult, $result);
    }

    public function providerSEQUENCE(): array
    {
        return require 'tests/data/Calculation/MathTrig/SEQUENCE.php';
    }
}
