<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions;

class SequenceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSEQUENCE
     *
     * @param mixed[] $arguments
     * @param mixed[]|string $expectedResult
     */
    public function testSEQUENCE(array $arguments, array|string $expectedResult): void
    {
        if (count($arguments) === 0) {
            $result = MatrixFunctions::sequence();
        } elseif (count($arguments) === 1) {
            $result = MatrixFunctions::sequence($arguments[0]);
        } elseif (count($arguments) === 2) {
            $result = MatrixFunctions::sequence($arguments[0], $arguments[1]);
        } elseif (count($arguments) === 3) {
            $result = MatrixFunctions::sequence($arguments[0], $arguments[1], $arguments[2]);
        } else {
            $result = MatrixFunctions::sequence($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
        }
        self::assertEquals($expectedResult, $result);
    }

    public static function providerSEQUENCE(): array
    {
        return require 'tests/data/Calculation/MathTrig/SEQUENCE.php';
    }
}
