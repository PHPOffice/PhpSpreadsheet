<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class OddTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerODD
     */
    public function testODD(int|string $expectedResult, float|int|string $value): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue("=ODD($value)");
        $sheet->getCell('A2')->setValue(3.7);
        self::assertEquals($expectedResult, $sheet->getCell('A1')->getCalculatedValue());
    }

    public static function providerODD(): array
    {
        return require 'tests/data/Calculation/MathTrig/ODD.php';
    }

    /**
     * @dataProvider providerOddArray
     */
    public function testOddArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ODD({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerOddArray(): array
    {
        return [
            'row vector' => [[[-3, 1, 5]], '{-3, 1, 4}'],
            'column vector' => [[[-3], [1], [5]], '{-3; 1; 4}'],
            'matrix' => [[[-3, 1], [5, 3]], '{-3, 1; 4, 1.5}'],
        ];
    }
}
