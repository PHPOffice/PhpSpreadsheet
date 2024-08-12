<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class CschTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCSCH
     */
    public function testCSCH(float|int|string $expectedResult, float|int|string $angle): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->setCellValue('A2', 1.3);
        $sheet->setCellValue('A3', 2.7);
        $sheet->setCellValue('A4', -3.8);
        $sheet->setCellValue('A5', -5.2);
        $sheet->getCell('A1')->setValue("=CSCH($angle)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-9);
    }

    public static function providerCSCH(): array
    {
        return require 'tests/data/Calculation/MathTrig/CSCH.php';
    }

    /**
     * @dataProvider providerCschArray
     */
    public function testCschArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CSCH({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCschArray(): array
    {
        return [
            'row vector' => [[[0.85091812823932, 1.91903475133494, -0.85091812823932]], '{1, 0.5, -1}'],
            'column vector' => [[[0.85091812823932], [1.91903475133494], [-0.85091812823932]], '{1; 0.5; -1}'],
            'matrix' => [[[0.85091812823932, 1.91903475133494], ['#DIV/0!', -0.85091812823932]], '{1, 0.5; 0, -1}'],
        ];
    }
}
