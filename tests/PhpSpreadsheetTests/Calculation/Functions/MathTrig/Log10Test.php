<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class Log10Test extends AllSetupTeardown
{
    /**
     * @dataProvider providerLOG10
     */
    public function testLOG10(mixed $expectedResult, mixed $number = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($number !== null) {
            $sheet->getCell('A1')->setValue($number);
        }
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=LOG10()');
        } else {
            $sheet->getCell('B1')->setValue('=LOG10(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerLOG10(): array
    {
        return require 'tests/data/Calculation/MathTrig/LOG10.php';
    }

    /**
     * @dataProvider providerLog10Array
     */
    public function testLog10Array(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LOG10({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLog10Array(): array
    {
        return [
            'row vector' => [[[-0.90308998699194, 0.3701428470511, 1.09691001300806]], '{0.125, 2.345, 12.5}'],
            'column vector' => [[[-0.90308998699194], [0.3701428470511], [1.09691001300806]], '{0.125; 2.345; 12.5}'],
            'matrix' => [[[-0.90308998699194, 0.3701428470511], [0.0, 1.09691001300806]], '{0.125, 2.345; 1.0, 12.5}'],
        ];
    }
}
