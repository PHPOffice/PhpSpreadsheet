<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AbsTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAbs
     */
    public function testAbs(mixed $expectedResult, mixed $number = 'omitted'): void
    {
        $sheet = $this->getSheet();
        $this->mightHaveException($expectedResult);
        $this->setCell('A1', $number);
        if ($number === 'omitted') {
            $sheet->getCell('B1')->setValue('=ABS()');
        } else {
            $sheet->getCell('B1')->setValue('=ABS(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerAbs(): array
    {
        return require 'tests/data/Calculation/MathTrig/ABS.php';
    }

    /**
     * @dataProvider providerAbsArray
     */
    public function testAbsoluteArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ABS({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAbsArray(): array
    {
        return [
            'row vector' => [[[1, 0, 1]], '{-1, 0, 1}'],
            'column vector' => [[[1], [0], [1]], '{-1; 0; 1}'],
            'matrix' => [[[1, 0], [1, 1.4]], '{-1, 0; 1, -1.4}'],
        ];
    }
}
