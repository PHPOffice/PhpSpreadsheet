<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TinvTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTINV
     *
     * @param mixed $expectedResult
     * @param mixed $probability
     * @param mixed $degrees
     */
    public function testTINV($expectedResult, $probability, $degrees): void
    {
        $this->runTestCaseReference('TINV', $expectedResult, $probability, $degrees);
    }

    public static function providerTINV(): array
    {
        return require 'tests/data/Calculation/Statistical/TINV.php';
    }

    /**
     * @dataProvider providerTInvArray
     */
    public function testTInvArray(array $expectedResult, string $values, string $degrees): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TINV({$values}, {$degrees})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTInvArray(): array
    {
        return [
            'row vector' => [
                [
                    [0.29001075058679815, 0.5023133547575189, 0.4713169827948964],
                ],
                '0.65',
                '{1.5, 3.5, 8}',
            ],
        ];
    }
}
