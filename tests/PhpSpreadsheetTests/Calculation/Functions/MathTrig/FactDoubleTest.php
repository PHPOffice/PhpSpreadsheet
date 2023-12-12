<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FactDoubleTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerFACTDOUBLE
     */
    public function testFACTDOUBLE(mixed $expectedResult, mixed $value): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue($value);
        $sheet->getCell('B1')->setValue('=FACTDOUBLE(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerFACTDOUBLE(): array
    {
        return require 'tests/data/Calculation/MathTrig/FACTDOUBLE.php';
    }

    /**
     * @dataProvider providerFactDoubleArray
     */
    public function testFactDoubleArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=FACTDOUBLE({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerFactDoubleArray(): array
    {
        return [
            'row vector' => [[['#NUM!', 48, 945]], '{-2, 6, 9}'],
            'column vector' => [[['#NUM!'], [48], [945]], '{-2; 6; 9}'],
            'matrix' => [[['#NUM!', 48], [945, 3]], '{-2, 6; 9, 3.5}'],
        ];
    }
}
