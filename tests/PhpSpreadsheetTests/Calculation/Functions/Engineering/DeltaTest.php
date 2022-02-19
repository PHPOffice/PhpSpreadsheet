<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DeltaTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDELTA
     *
     * @param mixed $a
     * @param mixed $b
     * @param mixed $expectedResult
     */
    public function testDELTA($expectedResult, $a, $b): void
    {
        $result = Engineering::DELTA($a, $b);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDELTA(): array
    {
        return require 'tests/data/Calculation/Engineering/DELTA.php';
    }

    /**
     * @dataProvider providerDeltaArray
     */
    public function testDeltaArray(array $expectedResult, string $a, string $b): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DELTA({$a}, {$b})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerDeltaArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [1, 0, 0, 0, 0],
                    [0, 1, 0, 0, 0],
                    [0, 0, 1, 0, 0],
                    [0, 0, 0, 1, 0],
                    [0, 0, 0, 0, 1],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
                '{-1.2; -0.5; 0.0; 0.25; 2.5}',
            ],
        ];
    }
}
