<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CoshTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCosh
     */
    public function testCosh(mixed $expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 2);
        $sheet->getCell('A1')->setValue("=COSH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerCosh(): array
    {
        return require 'tests/data/Calculation/MathTrig/COSH.php';
    }

    /**
     * @dataProvider providerCoshArray
     */
    public function testCoshArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COSH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCoshArray(): array
    {
        return [
            'row vector' => [[[1.54308063481524, 1.12762596520638, 1.54308063481524]], '{1, 0.5, -1}'],
            'column vector' => [[[1.54308063481524], [1.12762596520638], [1.54308063481524]], '{1; 0.5; -1}'],
            'matrix' => [[[1.54308063481524, 1.12762596520638], [1.0, 1.54308063481524]], '{1, 0.5; 0, -1}'],
        ];
    }
}
