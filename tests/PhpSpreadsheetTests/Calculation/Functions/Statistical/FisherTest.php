<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FisherTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFISHER
     *
     * @param mixed $expectedResult
     */
    public function testFISHER($expectedResult, ...$args): void
    {
        $this->runTestCases('FISHER', $expectedResult, ...$args);
    }

    public static function providerFISHER(): array
    {
        return require 'tests/data/Calculation/Statistical/FISHER.php';
    }

    /**
     * @dataProvider providerFisherArray
     */
    public function testFisherArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FISHER({$values})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerFisherArray(): array
    {
        return [
            'row vector' => [
                [[-1.4722194895832204, 0.2027325540540821, 0.9729550745276566]],
                '{-0.9, 0.2, 0.75}',
            ],
        ];
    }
}
