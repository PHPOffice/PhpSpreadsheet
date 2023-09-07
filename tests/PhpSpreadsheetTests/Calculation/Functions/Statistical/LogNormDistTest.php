<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class LogNormDistTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerLOGNORMDIST
     */
    public function testLOGNORMDIST(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('LOGNORMDIST', $expectedResult, ...$args);
    }

    public static function providerLOGNORMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/LOGNORMDIST.php';
    }

    /**
     * @dataProvider providerLogNormDistArray
     */
    public function testLogNormDistArray(array $expectedResult, string $values, string $mean, string $stdDev): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOGNORMDIST({$values}, {$mean}, {$stdDev})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLogNormDistArray(): array
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
