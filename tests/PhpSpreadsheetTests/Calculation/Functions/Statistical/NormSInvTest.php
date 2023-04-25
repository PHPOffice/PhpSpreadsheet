<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class NormSInvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerNORMSINV
     *
     * @param mixed $expectedResult
     */
    public function testNORMSINV($expectedResult, ...$args): void
    {
        $this->runTestCases('NORMSINV', $expectedResult, ...$args);
    }

    public static function providerNORMSINV(): array
    {
        return require 'tests/data/Calculation/Statistical/NORMSINV.php';
    }

    /**
     * @dataProvider providerNormSInvArray
     */
    public function testNormSInvArray(array $expectedResult, string $probabilities): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=NORMSINV({$probabilities})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerNormSInvArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    ['#NUM!', -1.959963986120195],
                    [-0.3853204662702544, 0.6744897502234225],
                ],
                '{-0.75, 0.025; 0.35, 0.75}',
            ],
        ];
    }
}
