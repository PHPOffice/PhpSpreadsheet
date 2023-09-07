<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class LogNormDist2Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerLOGNORMDIST2
     */
    public function testLOGNORMDIST2(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('LOGNORM.DIST', $expectedResult, ...$args);
    }

    public static function providerLOGNORMDIST2(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGNORMDIST2.php';
    }

    /**
     * @dataProvider providerLogNormDist2Array
     */
    public function testLogNormDist2Array(array $expectedResult, string $values, string $mean, string $stdDev): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOGNORM.DIST({$values}, {$mean}, {$stdDev}, true)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLogNormDist2Array(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.20185593420695913, 0.34805905738890675, 0.47717995703671096],
                    [0.06641711479920787, 0.24102205723753728, 0.45897407661978173],
                    [8.579368431449463E-5, 0.03941233670471267, 0.398378394299419],
                ],
                '12',
                '{10, 6, 3}',
                '{9; 5; 2}',
            ],
        ];
    }
}
