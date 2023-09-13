<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ModTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMOD
     */
    public function testMOD(mixed $expectedResult, mixed $dividend = 'omitted', mixed $divisor = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($dividend !== null) {
            $sheet->getCell('A1')->setValue($dividend);
        }
        if ($divisor !== null) {
            $sheet->getCell('A2')->setValue($divisor);
        }
        if ($dividend === 'omitted') {
            $sheet->getCell('B1')->setValue('=MOD()');
        } elseif ($divisor === 'omitted') {
            $sheet->getCell('B1')->setValue('=MOD(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=MOD(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerMOD(): array
    {
        return require 'tests/data/Calculation/MathTrig/MOD.php';
    }

    /**
     * @dataProvider providerModArray
     */
    public function testModArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MOD({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerModArray(): array
    {
        return [
            'matrix' => [[[4, 3, 2], [1, 0, 4], [3, 2, 1]], '{9, 8, 7; 6, 5, 4; 3, 2, 1}', '5'],
        ];
    }
}
