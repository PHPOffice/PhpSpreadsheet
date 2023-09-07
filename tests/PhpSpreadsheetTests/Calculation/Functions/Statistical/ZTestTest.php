<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ZTestTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerZTEST
     */
    public function testZTEST(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseReference('ZTEST', $expectedResult, ...$args);
    }

    public static function providerZTEST(): array
    {
        return require 'tests/data/Calculation/Statistical/ZTEST.php';
    }

    /**
     * @dataProvider providerZTestArray
     */
    public function testZTestArray(array $expectedResult, string $dataSet, string $m0): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ZTEST({$dataSet}, {$m0})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerZTestArray(): array
    {
        return [
            'row vector' => [
                [
                    [0.09057419685136381, 0.4516213175273426, 0.8630433891295299],
                ],
                '{3, 6, 7, 8, 6, 5, 4, 2, 1, 9}',
                '{4, 5, 6}',
            ],
        ];
    }
}
