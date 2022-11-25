<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ErfCTest extends AllSetupTeardown
{
    const ERF_PRECISION = 1E-12;

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $expectedResult
     */
    public function testERFC($expectedResult, ...$args): void
    {
        $this->runTestCase('ERFC', $expectedResult, ...$args);
    }

    public function providerERFC(): array
    {
        return require 'tests/data/Calculation/Engineering/ERFC.php';
    }

    /**
     * @dataProvider providerErfCArray
     */
    public function testErfCArray(array $expectedResult, string $lower): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERFC({$lower})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerErfCArray(): array
    {
        return [
            'row vector' => [
                [
                    [1.9103139782296354, 1.5204998778130465, 1.0, 0.7236736098317631, 0.0004069520174449588],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
            ],
        ];
    }
}
