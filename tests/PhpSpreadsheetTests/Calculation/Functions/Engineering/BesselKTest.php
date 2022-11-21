<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class BesselKTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBESSELK
     *
     * @param mixed $expectedResult
     */
    public function testBESSELK($expectedResult, ...$args): void
    {
        $this->runTestCase('BESSELK', $expectedResult, ...$args);
    }

    public function providerBESSELK(): array
    {
        return require 'tests/data/Calculation/Engineering/BESSELK.php';
    }

    /**
     * @dataProvider providerBesselKArray
     */
    public function testBesselKArray(array $expectedResult, string $value, string $ord): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BESSELK({$value}, {$ord})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerBesselKArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [4.721244734980139, 1.5415067364690132, 0.9244190350213235, 0.2976030874538336, 0.06234755419101918],
                    [99.97389411857176, 3.747025980669556, 1.6564411280110791, 0.4021240820149834, 0.07389081565026694],
                    [19999.500068449335, 31.517714581825462, 7.5501835470656395, 0.9410016186778072, 0.12146020671123273],
                ],
                '{0.01, 0.25, 0.5, 1.25, 2.5}',
                '{0; 1; 2}',
            ],
        ];
    }
}
