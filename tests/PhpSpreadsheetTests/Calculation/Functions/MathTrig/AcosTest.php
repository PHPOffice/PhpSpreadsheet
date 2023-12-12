<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AcosTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAcos
     */
    public function testAcos(mixed $expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue(0.5);
        $sheet->getCell('A1')->setValue("=ACOS($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerAcos(): array
    {
        return require 'tests/data/Calculation/MathTrig/ACOS.php';
    }

    /**
     * @dataProvider providerAcosArray
     */
    public function testAcosArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ACOS({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAcosArray(): array
    {
        return [
            'row vector' => [[[0.0, 1.04719755119660, 3.14159265358979]], '{1, 0.5, -1}'],
            'column vector' => [[[0.0], [1.04719755119660], [3.14159265358979]], '{1; 0.5; -1}'],
            'matrix' => [[[0.0, 1.04719755119660], [1.57079632679490, 3.14159265358979]], '{1, 0.5; 0, -1}'],
        ];
    }
}
