<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TanTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTan
     */
    public function testTan(mixed $expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1);
        $sheet->getCell('A1')->setValue("=TAN($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerTan(): array
    {
        return require 'tests/data/Calculation/MathTrig/TAN.php';
    }

    /**
     * @dataProvider providerTanArray
     */
    public function testTanArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TAN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTanArray(): array
    {
        return [
            'row vector' => [[[1.55740772465490, 0.54630248984379, -1.55740772465490]], '{1, 0.5, -1}'],
            'column vector' => [[[1.55740772465490], [0.54630248984379], [-1.55740772465490]], '{1; 0.5; -1}'],
            'matrix' => [[[1.55740772465490, 0.54630248984379], [0.0, -1.55740772465490]], '{1, 0.5; 0, -1}'],
        ];
    }
}
