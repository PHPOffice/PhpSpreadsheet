<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class DegreesTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDEGREES
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testDegrees($expectedResult, $number = 'omitted'): void
    {
        $sheet = $this->getSheet();
        $this->mightHaveException($expectedResult);
        $this->setCell('A1', $number);
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=DEGREES()');
        } else {
            $sheet->getCell('B1')->setValue('=DEGREES(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public static function providerDegrees(): array
    {
        return require 'tests/data/Calculation/MathTrig/DEGREES.php';
    }

    /**
     * @dataProvider providerDegreesArray
     */
    public function testDegreesArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=DEGREES({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDegreesArray(): array
    {
        return [
            'row vector' => [[[143.23944878270600, 7.16197243913529, -183.34649444186300]], '{2.5, 0.125, -3.2}'],
            'column vector' => [[[143.23944878270600], [7.16197243913529], [-183.34649444186300]], '{2.5; 0.125; -3.2}'],
            'matrix' => [[[143.23944878270600, 7.16197243913529], [429.71834634811700, -183.34649444186300]], '{2.5, 0.125; 7.5, -3.2}'],
        ];
    }
}
