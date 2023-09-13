<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class AcoshTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAcosh
     */
    public function testAcosh(mixed $expectedResult, string $formula): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A2')->setValue('1.5');
        $sheet->getCell('A1')->setValue("=ACOSH($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public static function providerAcosh(): array
    {
        return require 'tests/data/Calculation/MathTrig/ACOSH.php';
    }

    /**
     * @dataProvider providerAcoshArray
     */
    public function testAcoshArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ACOSH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerAcoshArray(): array
    {
        return [
            'row vector' => [[[0.0, 1.31695789692482, 1.76274717403909]], '{1, 2, 3}'],
            'column vector' => [[[0.0], [1.31695789692482], [1.76274717403909]], '{1; 2; 3}'],
            'matrix' => [[[0.0, 1.31695789692482], [1.76274717403909, 2.06343706889556]], '{1, 2; 3, 4}'],
        ];
    }
}
